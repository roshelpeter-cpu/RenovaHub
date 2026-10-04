<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Quotation;
use App\Models\User;

class QuotationPolicy
{
    public function view(User $user, Quotation $quotation): bool
    {
        return $user->can('view', $quotation->project);
    }

    /**
     * Contractors prepare quotations. Homeowners only approve or reject
     * an open quotation on a project that is still underway.
     */
    public function create(User $user, Project $project): bool
    {
        return $user->isContractor()
            && (int) $project->contractor_id === (int) $user->id
            && ! $project->isClosedRecord();
    }

    public function update(User $user, Quotation $quotation): bool
    {
        if (! $this->view($user, $quotation) || $quotation->project->isClosedRecord()) {
            return false;
        }

        return in_array($quotation->status, [
            Quotation::STATUS_PENDING,
            Quotation::STATUS_CLARIFICATION,
        ], true);
    }
}
