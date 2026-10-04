<?php

namespace App\Policies;

use App\Models\Quotation;
use App\Models\User;

class QuotationPolicy
{
    public function view(User $user, Quotation $quotation): bool
    {
        return $user->can('view', $quotation->project);
    }

    public function update(User $user, Quotation $quotation): bool
    {
        return $this->view($user, $quotation);
    }
}
