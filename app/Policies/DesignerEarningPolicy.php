<?php

namespace App\Policies;

use App\Models\DesignerEarning;
use App\Models\User;

class DesignerEarningPolicy
{
    public function view(User $user, DesignerEarning $earning): bool
    {
        return $user->isDesigner() && (int) $earning->designer_id === (int) $user->id;
    }
}
