<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectService
{
    public function __construct(private ProjectInvitationService $invitations) {}
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
     * Store the manually entered location. Coordinates stay nullable until
     * a geocoding provider is connected.
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
            $project->update([
                'designer_id' => $designerId,
                'contractor_id' => $contractorId,
            ]);

            $this->invitations->invite($project, 'designer', $designerId);
            $this->invitations->invite($project, 'contractor', $contractorId);

            return $project->refresh();
        });
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
