<?php

namespace App\Services\Contractor;

use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ContractorWorkspaceService
{
    /**
     * Accepted invitations are the only projects this contractor may open.
     * Pending and declined offers stay on the invitations list.
     */
    public function accepted(User $contractor): Builder
    {
        return Project::query()
            ->where('contractor_id', $contractor->id)
            ->whereHas('invitations', function (Builder $query) use ($contractor) {
                $query->where('user_id', $contractor->id)
                    ->where('role', 'contractor')
                    ->where('status', ProjectInvitation::STATUS_ACCEPTED);
            });
    }

    public function findAccepted(User $contractor, Project $project): Project
    {
        abort_unless($contractor->can('construct', $project), 404);

        return $project;
    }

    /**
     * @return array{active: int, invitations: int, quotations: int, changes: int, month_earnings: float}
     */
    public function summary(User $contractor): array
    {
        $projectIds = $this->accepted($contractor)->pluck('id');

        return [
            'active' => $projectIds->count(),
            'invitations' => ProjectInvitation::query()
                ->where('user_id', $contractor->id)
                ->where('role', 'contractor')
                ->where('status', ProjectInvitation::STATUS_PENDING)
                ->count(),
            'quotations' => \App\Models\SupplierPrice::query()
                ->whereHas('request', fn ($query) => $query->whereIn('project_id', $projectIds)->where('contractor_id', $contractor->id))
                ->where('status', \App\Models\SupplierPrice::STATUS_OFFERED)
                ->count()
                + \App\Models\ConstructionFirmQuotation::query()
                    ->whereIn('project_id', $projectIds)
                    ->where('contractor_id', $contractor->id)
                    ->where('status', \App\Models\ConstructionFirmQuotation::STATUS_RECEIVED)
                    ->count(),
            'awaiting' => \App\Models\ProcurementProposal::query()
                ->whereIn('project_id', $projectIds)
                ->where('status', \App\Models\ProcurementProposal::STATUS_AWAITING)
                ->count()
                + \App\Models\BudgetSubmission::query()
                    ->whereIn('project_id', $projectIds)
                    ->where('status', \App\Models\BudgetSubmission::STATUS_AWAITING)
                    ->count(),
            'value' => (float) (clone $this->accepted($contractor))->sum('estimated_budget'),
            'month_earnings' => (float) $contractor->contractorEarnings()
                ->where('status', \App\Models\ContractorEarning::STATUS_RECORDED)
                ->sum('net_amount'),
        ];
    }

    /**
     * @return Collection<int, Project>
     */
    public function cards(User $contractor, ?string $filter = null): Collection
    {
        $query = $this->accepted($contractor)
            ->with([
                'homeowner',
                'designer.professionalProfile',
                'referenceImages',
                'budgetItems',
                'milestones',
                'progressStages',
            ]);

        if ($filter === 'active') {
            $query->whereIn('status', [Project::STATUS_IN_PROGRESS, Project::STATUS_CONFIRMED]);
        } elseif ($filter === 'planning') {
            $query->whereIn('status', [Project::STATUS_PLANNING, Project::STATUS_CONFIRMED, Project::STATUS_AWAITING_TEAM]);
        } elseif ($filter === 'construction') {
            $query->where('status', Project::STATUS_IN_PROGRESS);
        } elseif ($filter === 'procurement') {
            $query->whereHas('materialRequirements');
        } elseif ($filter === 'completed') {
            $query->where('status', Project::STATUS_COMPLETED);
        }

        return $query->latest('updated_at')->get();
    }
}
