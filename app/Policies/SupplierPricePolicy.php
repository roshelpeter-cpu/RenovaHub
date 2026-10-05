<?php

namespace App\Policies;

use App\Models\SupplierPrice;
use App\Models\SupplierPriceRequest;
use App\Models\User;

class SupplierPricePolicy
{
    public function view(User $user, SupplierPrice $price): bool
    {
        $price->loadMissing('request');

        return $user->can('construct', $price->request->project)
            && (int) $price->request->contractor_id === (int) $user->id;
    }

    public function viewRequest(User $user, SupplierPriceRequest $request): bool
    {
        return $user->can('construct', $request->project)
            && (int) $request->contractor_id === (int) $user->id;
    }
}
