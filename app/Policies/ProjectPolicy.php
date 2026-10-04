<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Only homeowners manage renovation projects in this stage.
     */
    public function viewAny(User $user): bool
    {
        return $user->isHomeowner();
    }

    public function view(User $user, Project $project): bool
    {
        return $user->isHomeowner() && $project->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isHomeowner();
    }

    /**
     * Edits, location, budget and team changes all require ownership.
     */
    public function update(User $user, Project $project): bool
    {
        return $this->view($user, $project);
    }

    public function delete(User $user, Project $project): bool
    {
        return $this->view($user, $project);
    }

    /**
     * Homeowners can add documents, inspiration and change requests only
     * while the project is still open. Hiding the button is not the check.
     */
    public function contribute(User $user, Project $project): bool
    {
        return $this->view($user, $project) && ! $project->isClosedRecord();
    }
}
