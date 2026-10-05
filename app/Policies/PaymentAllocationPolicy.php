<?php

namespace App\Policies;

use App\Models\PaymentAllocation;
use App\Models\User;

class PaymentAllocationPolicy
{
    public function view(User $user, PaymentAllocation $allocation): bool
    {
        if ($user->can('view', $allocation->project)) {
            return true;
        }

        return $user->can('construct', $allocation->project);
    }

    /**
     * Only the assigned contractor can pay a pending supplier or construction share.
     * The contractor's own earning is never payable from this action.
     */
    public function pay(User $user, PaymentAllocation $allocation): bool
    {
        return $user->isContractor()
            && (int) $allocation->project->contractor_id === (int) $user->id
            && in_array($allocation->bucket, [PaymentAllocation::MATERIALS, PaymentAllocation::CONSTRUCTION], true)
            && $allocation->status === PaymentAllocation::STATUS_PENDING;
    }
}
