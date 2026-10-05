<?php

namespace App\Policies;

use App\Models\ContractorEarning;
use App\Models\User;

class ContractorEarningPolicy
{
    public function view(User $user, ContractorEarning $earning): bool
    {
        return $user->isContractor() && (int) $earning->contractor_id === (int) $user->id;
    }
}
