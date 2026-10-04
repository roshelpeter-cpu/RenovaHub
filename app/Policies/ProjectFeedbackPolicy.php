<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\ProjectFeedback;
use App\Models\User;

class ProjectFeedbackPolicy
{
    public function view(User $user, ProjectFeedback $feedback): bool
    {
        return $user->can('view', $feedback->project);
    }

    /**
     * Final ratings exist only after completion, and only for the
     * designer or contractor actually assigned to that project.
     */
    public function create(User $user, Project $project): bool
    {
        return $user->can('view', $project) && $project->isCompleted();
    }
}
