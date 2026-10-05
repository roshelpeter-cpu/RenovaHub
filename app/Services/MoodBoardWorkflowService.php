<?php

namespace App\Services;

use App\Models\MoodBoard;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MoodBoardWorkflowService
{
    public function __construct(
        private ActivityLogService $activity,
        private NotificationService $notifications,
    ) {}

    /**
     * The designer owns the official board. Homeowners review it; they do not start it.
     */
    public function ensureBoard(Project $project, User $designer): MoodBoard
    {
        return $project->moodBoard()->firstOrCreate(
            ['project_id' => $project->id],
            [
                'created_by' => $designer->id,
                'title' => $project->name.' Mood Board',
                'summary' => 'Draft design direction',
                'status' => MoodBoard::STATUS_DRAFT,
                'version' => 1,
            ],
        );
    }

    public function addItem(MoodBoard $board, User $designer, array $data, ?UploadedFile $image = null): void
    {
        $this->assertEditable($board);

        $path = $image?->store('mood-boards/'.$board->id, 'public');

        $board->items()->create([
            'kind' => $data['kind'],
            'title' => $data['title'],
            'body' => $data['body'] ?? null,
            'colour' => $data['colour'] ?? null,
            'image' => $path,
        ]);

        $board->forceFill(['created_by' => $board->created_by ?: $designer->id])->save();
    }

    public function submit(MoodBoard $board, User $designer): void
    {
        $this->assertEditable($board);

        if ($board->items()->doesntExist()) {
            throw ValidationException::withMessages([
                'mood_board' => 'Add at least one inspiration, colour, material or note before submitting.',
            ]);
        }

        DB::transaction(function () use ($board, $designer) {
            $board->update([
                'status' => MoodBoard::STATUS_AWAITING,
                'submitted_at' => now(),
                'created_by' => $designer->id,
                'approved_at' => null,
                'revision_note' => null,
            ]);

            $project = $board->project;
            $this->activity->record($project, $designer, 'moodboard.submitted', 'Mood board submitted for homeowner approval.');
            $this->notifications->notify(
                $project->homeowner,
                'Mood board ready for review',
                $project->name.' has a mood board waiting for your approval.',
                'design',
                route('homeowner.projects.mood-board', $project),
            );
        });
    }

    /**
     * Final status is set only by the homeowner, after the designer has submitted.
     */
    public function approve(MoodBoard $board, User $homeowner): void
    {
        abort_unless($board->status === MoodBoard::STATUS_AWAITING, 403);

        $board->update([
            'status' => MoodBoard::STATUS_APPROVED,
            'approved_at' => now(),
            'revision_note' => null,
        ]);

        $project = $board->project;
        $this->activity->record($project, $homeowner, 'moodboard.approved', 'The homeowner approved the mood board.');

        if ($project->designer) {
            $this->notifications->notify(
                $project->designer,
                'Mood board approved',
                $project->name.' mood board is now the final design direction.',
                'design',
                route('designer.projects.mood-board', $project),
            );
        }
    }

    public function requestChanges(MoodBoard $board, User $homeowner, string $note): void
    {
        abort_unless($board->status === MoodBoard::STATUS_AWAITING, 403);

        $board->update([
            'status' => MoodBoard::STATUS_REVISION,
            'revision_note' => $note,
            'approved_at' => null,
            'version' => $board->version + 1,
        ]);

        $project = $board->project;
        $this->activity->record($project, $homeowner, 'moodboard.revision', 'The homeowner requested mood board changes.');

        if ($project->designer) {
            $this->notifications->notify(
                $project->designer,
                'Mood board revision requested',
                $note,
                'design',
                route('designer.projects.mood-board', $project),
            );
        }
    }

    private function assertEditable(MoodBoard $board): void
    {
        if (! $board->canSubmit() || $board->project->isClosedRecord()) {
            throw ValidationException::withMessages([
                'mood_board' => 'This mood board is with the homeowner or already final, so it cannot be edited.',
            ]);
        }
    }
}
