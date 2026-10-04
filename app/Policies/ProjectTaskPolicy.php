<?php

namespace App\Policies;

use App\Models\ProjectTask;
use App\Models\User;

class ProjectTaskPolicy
{
    /**
     * A task is visible only when the homeowner owns the project it belongs to.
     */
    public function view(User $user, ProjectTask $task): bool
    {
        return $user->can('view', $task->project);
    }
}
