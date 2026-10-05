<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Quotation;
use App\Models\User;

class QuotationPolicy
{
    public function view(User $user, Quotation $quotation): bool
    {
        if ($quotation->status === Quotation::STATUS_DRAFT) {
            return $this->prepare($user, $quotation) || (
                $user->isContractor()
                && (int) $quotation->contractor_id === (int) $user->id
                && $user->can('construct', $quotation->project)
            );
        }

        if ($user->can('view', $quotation->project)) {
            return true;
        }

        return $user->can('construct', $quotation->project)
            && (int) $quotation->contractor_id === (int) $user->id;
    }

    /**
     * Contractors prepare quotations. Homeowners only approve or reject
     * an open quotation on a project that is still underway.
     */
    public function create(User $user, Project $project): bool
    {
        return $user->can('construct', $project) && ! $project->isClosedRecord();
    }

    /**
     * Homeowner approval stays on the homeowner policy path. A contractor
     * who can see a submitted quotation still cannot decide it.
     */
    public function update(User $user, Quotation $quotation): bool
    {
        if (! $user->isHomeowner() || (int) $quotation->project->user_id !== (int) $user->id || $quotation->project->isClosedRecord()) {
            return false;
        }

        return in_array($quotation->status, [
            Quotation::STATUS_PENDING,
            Quotation::STATUS_SUBMITTED,
            Quotation::STATUS_CLARIFICATION,
        ], true);
    }

    public function prepare(User $user, Quotation $quotation): bool
    {
        return $user->can('construct', $quotation->project)
            && (int) $quotation->contractor_id === (int) $user->id
            && $quotation->status === Quotation::STATUS_DRAFT
            && ! $quotation->project->isClosedRecord();
    }

    public function submit(User $user, Quotation $quotation): bool
    {
        return $this->prepare($user, $quotation);
    }
}
