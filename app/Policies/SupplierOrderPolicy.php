<?php

namespace App\Policies;

use App\Models\SupplierOrder;
use App\Models\User;

class SupplierOrderPolicy
{
    /**
     * The order id is not an access key. Another contractor who guesses the
     * URL is rejected even when both users share the contractor role.
     */
    public function view(User $user, SupplierOrder $order): bool
    {
        return $user->can('construct', $order->project)
            && (int) $order->contractor_id === (int) $user->id;
    }

    public function update(User $user, SupplierOrder $order): bool
    {
        return $this->view($user, $order) && ! $order->project->isClosedRecord();
    }
}
