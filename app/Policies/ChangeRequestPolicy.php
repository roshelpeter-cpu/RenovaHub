<?php

namespace App\Policies;

use App\Models\ChangeRequest;
use App\Models\Project;
use App\Models\User;

class ChangeRequestPolicy
{
    public function view(User $user, ChangeRequest $changeRequest): bool
    {
        return $user->can('view', $changeRequest->project) || $user->can('construct', $changeRequest->project);
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
        return $user->can('view', $changeRequest->project);
    }

    /**
     * Cost and time impact belong to the assigned contractor. Approving the
     * change stays with the homeowner, so this method cannot set approved.
     */
    public function respond(User $user, ChangeRequest $changeRequest): bool
    {
        return $user->can('construct', $changeRequest->project)
            && ! $changeRequest->project->isClosedRecord()
            && in_array($changeRequest->status, [
                ChangeRequest::STATUS_SUBMITTED,
                ChangeRequest::STATUS_UNDER_REVIEW,
                ChangeRequest::STATUS_COST_PROVIDED,
            ], true);
    }
}
