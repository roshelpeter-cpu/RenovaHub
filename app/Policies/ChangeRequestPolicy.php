<?php

namespace App\Policies;

use App\Models\ChangeRequest;
use App\Models\Project;
use App\Models\User;

class ChangeRequestPolicy
{
    public function view(User $user, ChangeRequest $changeRequest): bool
    {
        return $user->can('view', $changeRequest->project);
    }

    /**
     * A change request can move cost or time, so it is limited to an open project.
     */
    public function create(User $user, Project $project): bool
    {
        return $user->can('contribute', $project);
    }

    public function update(User $user, ChangeRequest $changeRequest): bool
    {
        return $this->view($user, $changeRequest);
    }
}
