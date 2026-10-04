<?php

namespace App\Policies;

use App\Models\ProjectInvitation;
use App\Models\User;

class ProjectInvitationPolicy
{
    /**
     * Accept and decline belong to the invited professional, not the homeowner.
     */
    public function respond(User $user, ProjectInvitation $invitation): bool
    {
        return $invitation->user_id === $user->id && $invitation->isPending();
    }
}
