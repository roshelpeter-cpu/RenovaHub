<?php

namespace App\Services;

use App\Models\DesignConcept;
use App\Models\DesignerEarning;
use App\Models\MoodBoard;
use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DesignerWorkspaceService
{
    /**
     * Accepted projects are the only ones the designer may open.
     * Pending and declined invitations stay on the invitations list.
     */
    public function accepted(User $designer): Builder
    {
        return Project::query()
            ->where('designer_id', $designer->id)
            ->whereHas('invitations', function (Builder $query) use ($designer) {
                $query->where('user_id', $designer->id)
                    ->where('role', 'designer')
                    ->where('status', ProjectInvitation::STATUS_ACCEPTED);
            });
    }

    public function findAccepted(User $designer, Project $project): Project
    {
        abort_unless($designer->can('design', $project), 404);

        return $project;
    }

    /**
     * @return array<string, int|float>
     */
    public function summary(User $designer): array
    {
        $projects = $this->accepted($designer);
        $projectIds = (clone $projects)->pluck('id');

        $awaitingBoards = MoodBoard::query()
            ->whereIn('project_id', $projectIds)
            ->where('status', MoodBoard::STATUS_AWAITING)
            ->count();

        $awaitingConcepts = DesignConcept::query()
            ->whereIn('project_id', $projectIds)
            ->whereIn('status', [DesignConcept::STATUS_AWAITING, DesignConcept::STATUS_SUBMITTED])
            ->count();

        $revisions = $designer->id
            ? \App\Models\DesignChangeRequest::query()
                ->whereIn('project_id', $projectIds)
                ->where('status', \App\Models\DesignChangeRequest::STATUS_PENDING)
                ->count()
            : 0;

        $monthGross = (float) DesignerEarning::query()
            ->where('designer_id', $designer->id)
            ->where('status', DesignerEarning::STATUS_RECORDED)
            ->whereBetween('recorded_on', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->sum('gross_amount');

        return [
            'active' => (clone $projects)->whereNotIn('status', [Project::STATUS_COMPLETED, Project::STATUS_ARCHIVED])->count(),
            'invitations' => ProjectInvitation::query()
                ->where('user_id', $designer->id)
                ->where('role', 'designer')
                ->where('status', ProjectInvitation::STATUS_PENDING)
                ->count(),
            'awaiting' => $awaitingBoards + $awaitingConcepts,
            'revisions' => $revisions,
            'month_earnings' => $monthGross,
        ];
    }

    /**
     * @return list<array{key: string, label: string, status: string}>
     */
    public function stages(Project $project): array
    {
        $project->loadMissing(['moodBoard', 'designConcepts', 'designChangeRequests']);
        $board = $project->moodBoard;
        $concepts = $project->designConcepts;
        $approvedConcept = $concepts->contains('status', DesignConcept::STATUS_APPROVED);
        $awaitingConcept = $concepts->contains(fn (DesignConcept $concept) => in_array($concept->status, [DesignConcept::STATUS_AWAITING, DesignConcept::STATUS_SUBMITTED], true));
        $revision = $board?->status === MoodBoard::STATUS_REVISION
            || $concepts->contains('status', DesignConcept::STATUS_REVISION)
            || $project->designChangeRequests->contains('status', 'pending');

        $brief = filled($project->requirements) || filled($project->description);

        return [
            ['key' => 'brief', 'label' => 'Design Brief', 'status' => $brief ? 'Completed' : 'Not Started'],
            ['key' => 'mood', 'label' => 'Mood Board', 'status' => $this->boardStage($board)],
            ['key' => 'concepts', 'label' => 'Design Concepts', 'status' => $concepts->isEmpty() ? 'Not Started' : ($approvedConcept ? 'Completed' : 'In Progress')],
            ['key' => 'review', 'label' => 'Homeowner Review', 'status' => $awaitingConcept || $board?->status === MoodBoard::STATUS_AWAITING ? 'Awaiting Approval' : ($approvedConcept ? 'Completed' : 'Not Started')],
            ['key' => 'revisions', 'label' => 'Revisions', 'status' => $revision ? 'Revision Requested' : ($approvedConcept ? 'Completed' : 'Not Started')],
            ['key' => 'final', 'label' => 'Final Design', 'status' => $approvedConcept ? 'Completed' : 'Not Started'],
            ['key' => 'approval', 'label' => 'Homeowner Approval', 'status' => $approvedConcept ? 'Completed' : 'Not Started'],
            ['key' => 'done', 'label' => 'Design Completed', 'status' => (int) data_get($project->workspace_meta, 'design_progress') >= 100 || $project->isCompleted() ? 'Completed' : 'Not Started'],
        ];
    }

    private function boardStage(?MoodBoard $board): string
    {
        return match ($board?->status) {
            MoodBoard::STATUS_APPROVED => 'Completed',
            MoodBoard::STATUS_AWAITING => 'Awaiting Approval',
            MoodBoard::STATUS_REVISION => 'Revision Requested',
            MoodBoard::STATUS_DRAFT => 'In Progress',
            default => 'Not Started',
        };
    }

    /**
     * @return Collection<int, Project>
     */
    public function cards(User $designer, ?string $filter = null): Collection
    {
        $query = $this->accepted($designer)
            ->with(['homeowner', 'contractor.professionalProfile', 'moodBoard', 'designConcepts'])
            ->orderByDesc('updated_at');

        if ($filter === 'active') {
            $query->whereNotIn('status', [Project::STATUS_COMPLETED, Project::STATUS_ARCHIVED]);
        } elseif ($filter === 'awaiting') {
            $query->where(function (Builder $inner) {
                $inner->whereHas('moodBoard', fn (Builder $board) => $board->where('status', MoodBoard::STATUS_AWAITING))
                    ->orWhereHas('designConcepts', fn (Builder $concept) => $concept->whereIn('status', [DesignConcept::STATUS_AWAITING, DesignConcept::STATUS_SUBMITTED]));
            });
        } elseif ($filter === 'completed') {
            $query->whereIn('status', [Project::STATUS_COMPLETED, Project::STATUS_ARCHIVED]);
        }

        return $query->get();
    }
}
