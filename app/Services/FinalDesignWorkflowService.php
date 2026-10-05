<?php

namespace App\Services;

use App\Models\DesignChangeRequest;
use App\Models\DesignConcept;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FinalDesignWorkflowService
{
    public function __construct(
        private ActivityLogService $activity,
        private NotificationService $notifications,
    ) {}

    public function package(Project $project, User $designer): DesignConcept
    {
        return $project->designConcepts()->firstOrCreate(
            ['title' => 'Final Design Package'],
            [
                'designer_id' => $designer->id,
                'description' => 'Final architectural package for '.$project->name.'.',
                'notes' => '',
                'status' => DesignConcept::STATUS_DRAFT,
            ],
        );
    }

    public function addFile(DesignConcept $concept, string $kind, UploadedFile $image, ?string $caption = null): void
    {
        $this->assertEditable($concept);

        if ($kind === 'floor_plan') {
            $concept->files()->where('kind', 'floor_plan')->delete();
        }

        $concept->files()->create([
            'kind' => $kind,
            'path' => $image->store('concepts/'.$concept->id, 'public'),
            'caption' => $caption ?: ucfirst(str_replace('_', ' ', $kind)),
        ]);
    }

    public function addMaterial(DesignConcept $concept, array $data): void
    {
        $this->assertEditable($concept);

        $concept->project->materialRequirements()->create([
            'design_concept_id' => $concept->id,
            'name' => $data['name'],
            'room' => $data['room'],
            'category' => $data['category'] ?? 'finish',
            'quantity' => $data['quantity'],
            'unit' => $data['unit'],
            'specification' => $data['specification'] ?? null,
            'estimated_value' => $data['estimated_value'] ?? null,
            'status' => 'pending',
        ]);
    }

    public function submit(DesignConcept $concept, User $designer): void
    {
        $this->assertEditable($concept);

        if ($concept->files()->doesntExist() || $concept->project->materialRequirements()->where('design_concept_id', $concept->id)->doesntExist()) {
            throw ValidationException::withMessages([
                'final_design' => 'Add a design image, a floor plan and at least one material before sending the package.',
            ]);
        }

        DB::transaction(function () use ($concept, $designer) {
            $concept->update([
                'status' => DesignConcept::STATUS_AWAITING,
                'submitted_at' => now(),
                'approved_at' => null,
                'designer_id' => $designer->id,
            ]);

            $project = $concept->project;
            $this->activity->record($project, $designer, 'final-design.submitted', 'Final design sent for homeowner approval.');
            $this->notifications->notify(
                $project->homeowner,
                'Final design ready for review',
                $project->name.' has a final design waiting for approval.',
                'design',
                route('homeowner.projects.final-design', $project),
            );
        });
    }

    public function approve(DesignConcept $concept, User $homeowner): void
    {
        abort_unless($concept->status === DesignConcept::STATUS_AWAITING, 403);

        $concept->update([
            'status' => DesignConcept::STATUS_APPROVED,
            'approved_at' => now(),
        ]);

        $project = $concept->project;
        $this->activity->record($project, $homeowner, 'final-design.approved', 'Homeowner approved the final design for construction.');

        if ($project->designer) {
            $this->notifications->notify(
                $project->designer,
                'Final design approved',
                $project->name.' is approved for construction.',
                'design',
                route('designer.projects.final-design', $project),
            );
        }
    }

    public function requestChanges(DesignConcept $concept, User $homeowner, string $title, string $comment): void
    {
        abort_unless($concept->status === DesignConcept::STATUS_AWAITING, 403);

        $concept->update([
            'status' => DesignConcept::STATUS_REVISION,
            'approved_at' => null,
        ]);

        $concept->project->designChangeRequests()->create([
            'requested_by' => $homeowner->id,
            'title' => $title,
            'description' => $comment,
            'design_impact' => 'final_design',
            'status' => DesignChangeRequest::STATUS_PENDING,
        ]);

        if ($concept->project->designer) {
            $this->notifications->notify(
                $concept->project->designer,
                'Final design change requested',
                $title,
                'design',
                route('designer.projects.final-design', $concept->project),
            );
        }
    }

    public function respond(DesignChangeRequest $change, User $designer, string $decision, ?string $note = null): void
    {
        abort_unless(in_array($decision, ['accepted', 'rejected', 'needs_discussion', 'resolved'], true), 422);

        $change->update([
            'status' => $decision,
            'rejection_reason' => $note,
            'decided_at' => now(),
        ]);

        $this->activity->record($change->project, $designer, 'final-design.change', 'Designer marked a final design request as '.$decision.'.');
    }

    private function assertEditable(DesignConcept $concept): void
    {
        if (! $concept->isEditable() || $concept->project->isClosedRecord()) {
            throw ValidationException::withMessages([
                'final_design' => 'This final design has been sent for approval and cannot be edited.',
            ]);
        }
    }
}
