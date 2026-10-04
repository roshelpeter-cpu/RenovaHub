<?php

namespace App\Policies;

use App\Models\ChangeRequest;
use App\Models\User;

class ChangeRequestPolicy
{
    public function view(User $user, ChangeRequest $changeRequest): bool
    {
        return $user->can('view', $changeRequest->project);
    }

    public function update(User $user, ChangeRequest $changeRequest): bool
    {
        return $this->view($user, $changeRequest);
    }
}
