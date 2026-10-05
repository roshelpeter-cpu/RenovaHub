<?php

namespace App\Policies;

use App\Models\DesignChangeRequest;
use App\Models\Project;
use App\Models\User;

class DesignChangeRequestPolicy
{
    public function view(User $user, DesignChangeRequest $request): bool
    {
        return $user->can('design', $request->project) || $user->can('view', $request->project);
    }

    public function create(User $user, Project $project): bool
    {
        return $user->can('view', $project) && ! $project->isClosedRecord();
    }

    /**
     * Accept and reject belong to the project's designer, and a reason is required to reject.
     */
    public function decide(User $user, DesignChangeRequest $request): bool
    {
        return $user->can('design', $request->project)
            && $request->status === DesignChangeRequest::STATUS_PENDING
            && ! $request->project->isClosedRecord();
    }
}
