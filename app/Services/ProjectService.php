<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectService
{
    public function __construct(private ProjectInvitationService $invitations) {}

    /**
     * Catalogue totals and lists are built here so the Blade template never
     * runs its own queries. Ongoing means in_progress to match the screenshot.
     *
     * @return array<string, mixed>
     */
    public function catalogue(User $homeowner, string $status, string $sort): array
    {
        $owned = $homeowner->projects();
        $upcomingStatuses = Project::upcomingStatuses();
        $summary = [
            'total' => (clone $owned)->whereNotIn('status', $upcomingStatuses)->count(),
            'ongoing' => (clone $owned)->where('status', Project::STATUS_IN_PROGRESS)->count(),
            'completed' => (clone $owned)->where('status', Project::STATUS_COMPLETED)->count(),
            'upcoming' => (clone $owned)->whereIn('status', $upcomingStatuses)->count(),
            'value' => (float) (clone $owned)->whereNotIn('status', $upcomingStatuses)->sum('estimated_budget'),
        ];

        $query = $homeowner->projects()
            ->with([
                'designer.professionalProfile',
                'contractor.professionalProfile',
                'progressStages',
                'referenceImages',
                'invitations.professional.professionalProfile',
            ])
            ->when($status === 'ongoing', fn ($inner) => $inner->where('status', Project::STATUS_IN_PROGRESS))
            ->when($status === 'completed', fn ($inner) => $inner->where('status', Project::STATUS_COMPLETED))
            ->when($status === 'upcoming', fn ($inner) => $inner->whereIn('status', $upcomingStatuses))
            ->when(! in_array($status, ['ongoing', 'completed', 'upcoming'], true), fn ($inner) => $inner->whereNotIn('status', $upcomingStatuses))
            ->when($sort === 'oldest', fn ($inner) => $inner->oldest())
            ->when($sort === 'budget_high', fn ($inner) => $inner->orderByDesc('estimated_budget'))
            ->when($sort !== 'oldest' && $sort !== 'budget_high', fn ($inner) => $inner->latest());

        $projects = $query->get();
        $projects->each(function (Project $project) {
            $project->designer?->professionalProfile?->setRelation('user', $project->designer);
            $project->contractor?->professionalProfile?->setRelation('user', $project->contractor);
            $project->invitations->each(function (ProjectInvitation $invitation) {
                $invitation->professional?->professionalProfile?->setRelation('user', $invitation->professional);
            });
        });

        return [
            'summary' => $summary,
            'ongoing' => $projects->where('status', Project::STATUS_IN_PROGRESS)->values(),
            'completed' => $projects->where('status', Project::STATUS_COMPLETED)->values(),
            'upcoming' => $projects->filter(fn (Project $project) => $project->isUpcoming())->values(),
            'projects' => $projects,
            'status' => in_array($status, ['ongoing', 'completed', 'upcoming'], true) ? $status : 'all',
            'sort' => in_array($sort, ['oldest', 'budget_high'], true) ? $sort : 'newest',
        ];
    }
    /**
     * Create the project through the authenticated homeowner relationship
     * so ownership is set on the server and cannot be posted from the browser.
     *
     * @param  array{name: string, description: string, renovation_type: string, property_type: string, requirements?: ?string, additional_instructions?: ?string}  $details
     */
    public function createForHomeowner(User $homeowner, array $details): Project
    {
        return $homeowner->projects()->create([
            'name' => $details['name'],
            'description' => $details['description'],
            'requirements' => $details['requirements'] ?? null,
            'additional_instructions' => $details['additional_instructions'] ?? null,
            'renovation_type' => $details['renovation_type'],
            'property_type' => $details['property_type'],
            'status' => Project::STATUS_PLANNING,
            'currency' => 'LKR',
        ]);
    }

    /**
     * Store the manually entered location. No external location API is called.
     *
     * @param  array{address?: ?string, city?: ?string, province?: ?string, postal_code?: ?string, latitude?: ?float, longitude?: ?float}  $location
     */
    public function updateLocation(Project $project, array $location): Project
    {
        $project->update([
            'address' => $location['address'] ?? null,
            'city' => $location['city'] ?? null,
            'province' => $location['province'] ?? null,
            'postal_code' => $location['postal_code'] ?? null,
            'latitude' => $location['latitude'] ?? null,
            'longitude' => $location['longitude'] ?? null,
        ]);

        return $project;
    }

    /**
     * Homeowners can revise the brief. Progress and quotation totals stay with the workflow.
     *
     * @param  array{name: string, description: string, renovation_type: string, property_type: string, requirements?: ?string, additional_instructions?: ?string}  $details
     */
    public function updateDetails(Project $project, array $details): Project
    {
        $project->update([
            'name' => $details['name'],
            'description' => $details['description'],
            'requirements' => $details['requirements'] ?? null,
            'additional_instructions' => $details['additional_instructions'] ?? null,
            'renovation_type' => $details['renovation_type'],
            'property_type' => $details['property_type'],
        ]);

        return $project;
    }

    /**
     * @param  array{estimated_budget?: ?float, expected_start_date?: ?string, expected_completion_date?: ?string, timeline_notes?: ?string}  $budget
     */
    public function updateBudget(Project $project, array $budget): Project
    {
        $project->update([
            'estimated_budget' => $budget['estimated_budget'] ?? null,
            'expected_start_date' => $budget['expected_start_date'] ?? null,
            'expected_completion_date' => $budget['expected_completion_date'] ?? null,
            'timeline_notes' => $budget['timeline_notes'] ?? null,
        ]);

        return $project;
    }

    /**
     * Designer and contractor are saved together so a failed write
     * cannot leave only one of the two assignments applied.
     */
    public function assignTeam(Project $project, ?int $designerId, ?int $contractorId): Project
    {
        return DB::transaction(function () use ($project, $designerId, $contractorId) {
            $this->stageSelection($project, 'designer', $designerId);
            $this->stageSelection($project, 'contractor', $contractorId);

            return $project->refresh();
        });
    }

    /**
     * Selection creates a pending invitation. The professional is written onto
     * the project only after they accept, so the team card cannot show them early.
     */
    private function stageSelection(Project $project, string $role, ?int $userId): void
    {
        if ($userId === null) {
            return;
        }

        $accepted = $project->invitations()
            ->where('role', $role)
            ->where('user_id', $userId)
            ->where('status', ProjectInvitation::STATUS_ACCEPTED)
            ->exists();

        $column = $role.'_id';

        if ($accepted) {
            $project->update([$column => $userId]);

            return;
        }

        $project->update([$column => null]);
        $this->invitations->invite($project, $role, $userId);
    }

    public function markAwaitingTeam(Project $project): void
    {
        if (in_array($project->status, [Project::STATUS_DRAFT, Project::STATUS_PLANNING], true)) {
            $project->update(['status' => Project::STATUS_AWAITING_TEAM]);
        }
    }

    public function deleteProject(Project $project): void
    {
        DB::transaction(function () use ($project) {
            $project->load(['documents', 'referenceImages', 'messages.attachments']);

            foreach ($project->documents as $document) {
                $this->deleteStoredFile($document);
            }

            foreach ($project->referenceImages as $image) {
                Storage::disk('public')->delete($image->path);
            }

            $project->delete();
        });
    }

    private function deleteStoredFile(Document $document): void
    {
        if ($document->path !== '') {
            Storage::disk($document->disk)->delete($document->path);
        }
    }
}
