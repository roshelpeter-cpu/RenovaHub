<?php

namespace App\Policies;

use App\Models\ProfessionalProfile;
use App\Models\User;

class ProfessionalProfilePolicy
{
    /**
     * Explore is a homeowner directory. Other roles are blocked here as well
     * as by the homeowner middleware so a posted user_id cannot open the list.
     */
    public function viewAny(User $user): bool
    {
        return $user->isHomeowner();
    }

    /**
     * Profiles expose public professional data only. Ownership of a project
     * is not required to view a listed designer or contractor.
     */
    public function view(User $user, ProfessionalProfile $profile): bool
    {
        return $user->isHomeowner() && ($profile->listed || $profile->user_id !== null);
    }
}
