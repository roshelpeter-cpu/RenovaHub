<?php

namespace App\Services\Contractor;

use App\Models\DesignConcept;
use App\Models\MaterialRequirement;
use App\Models\Project;
use Illuminate\Support\Collection;

/**
 * The approved design concept is the only construction source of truth.
 * Drafts and concepts still awaiting the homeowner stay invisible to procurement.
 */
class FinalDesignService
{
    public function approved(Project $project): ?DesignConcept
    {
        return $project->designConcepts()
            ->where('status', DesignConcept::STATUS_APPROVED)
            ->whereNotNull('approved_at')
            ->with(['files', 'designer'])
            ->latest('approved_at')
            ->first();
    }

    /**
     * @return Collection<int, MaterialRequirement>
     */
    public function materials(Project $project): Collection
    {
        $concept = $this->approved($project);

        if ($concept === null) {
            return collect();
        }

        return MaterialRequirement::query()
            ->where('project_id', $project->id)
            ->where('design_concept_id', $concept->id)
            ->orderBy('room')
            ->orderBy('name')
            ->get();
    }

    /**
     * @return list<array{key: string, label: string, state: string, detail: string}>
     */
    public function timeline(Project $project): array
    {
        $board = $project->moodBoard;
        $concept = $this->approved($project);
        $anyConcept = $project->designConcepts()->exists();
        $materials = MaterialRequirement::query()->where('project_id', $project->id)->exists();
        $supplier = $project->procurementProposals()->where('type', 'supplier')->where('status', 'approved')->exists();
        $firm = $project->constructionAssignment()->exists();
        $budget = $project->budgetSubmissions()->whereIn('status', ['awaiting_homeowner', 'approved'])->latest()->first();
        $paid = $budget?->payment?->status === 'paid';

        $stages = [
            ['key' => 'created', 'label' => 'Project Created', 'done' => true, 'detail' => $project->created_at?->format('j M Y') ?? ''],
            ['key' => 'brief', 'label' => 'Design Brief', 'done' => filled($project->description), 'detail' => 'Homeowner brief'],
            ['key' => 'mood', 'label' => 'Mood Board', 'done' => $board !== null, 'detail' => $board?->statusLabel() ?? 'Not started'],
            ['key' => 'mood_ok', 'label' => 'Mood Board Approved', 'done' => (bool) $board?->approved_at, 'detail' => $board?->approved_at?->format('j M Y') ?? 'Waiting'],
            ['key' => 'final', 'label' => 'Final Design', 'done' => $anyConcept, 'detail' => $anyConcept ? 'Package received' : 'Not started'],
            ['key' => 'final_ok', 'label' => 'Final Design Approved', 'done' => $concept !== null, 'detail' => $concept?->approved_at?->format('j M Y') ?? 'Waiting'],
            ['key' => 'materials', 'label' => 'Material Procurement', 'done' => $materials && $concept !== null, 'detail' => $concept ? 'From the approved package' : 'Needs an approved design'],
            ['key' => 'supplier', 'label' => 'Supplier Selection', 'done' => $supplier, 'detail' => $supplier ? 'Homeowner approved' : 'Waiting'],
            ['key' => 'firm', 'label' => 'Construction Firm Selection', 'done' => $firm, 'detail' => $firm ? 'Firm assigned' : 'Waiting'],
            ['key' => 'budget', 'label' => 'Final Budget', 'done' => $budget?->status === 'approved', 'detail' => $budget?->statusLabel() ?? 'Not prepared'],
            ['key' => 'payment', 'label' => 'Payment', 'done' => $paid, 'detail' => $paid ? 'Verified' : 'One project payment'],
            ['key' => 'build', 'label' => 'Construction', 'done' => (int) $project->progress >= 100 || $project->status === Project::STATUS_COMPLETED, 'detail' => ((int) $project->progress).'%'],
            ['key' => 'inspect', 'label' => 'Final Inspection', 'done' => $project->status === Project::STATUS_COMPLETED, 'detail' => $project->status === Project::STATUS_COMPLETED ? 'Complete' : 'Upcoming'],
            ['key' => 'done', 'label' => 'Completed', 'done' => $project->status === Project::STATUS_COMPLETED, 'detail' => $project->actual_completion_date?->format('j M Y') ?? 'Upcoming'],
        ];

        $currentSet = false;

        return array_map(function (array $stage) use (&$currentSet) {
            if ($stage['done']) {
                $stage['state'] = 'completed';
            } elseif (! $currentSet) {
                $stage['state'] = 'current';
                $currentSet = true;
            } else {
                $stage['state'] = 'upcoming';
            }

            return $stage;
        }, $stages);
    }
}
