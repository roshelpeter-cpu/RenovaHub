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

    /**
     * A designer reaches private design records only after accepting the offer.
     * Knowing the project id is not enough, which blocks IDOR on /designer/projects/{id}.
     */
    public function design(User $user, Project $project): bool
    {
        if (! $user->isDesigner() || (int) $project->designer_id !== (int) $user->id) {
            return false;
        }

        return $project->invitations()
            ->where('user_id', $user->id)
            ->where('role', 'designer')
            ->where('status', 'accepted')
            ->exists();
    }

    /**
     * Construction records open only for the contractor who accepted the offer.
     * A changed project id in the URL is rejected even if the user is a contractor.
     */
    public function construct(User $user, Project $project): bool
    {
        if (! $user->isContractor() || (int) $project->contractor_id !== (int) $user->id) {
            return false;
        }

        return $project->invitations()
            ->where('user_id', $user->id)
            ->where('role', 'contractor')
            ->where('status', 'accepted')
            ->exists();
    }
}
