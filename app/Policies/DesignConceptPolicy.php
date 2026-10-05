<?php

namespace App\Policies;

use App\Models\DesignConcept;
use App\Models\Project;
use App\Models\User;

class DesignConceptPolicy
{
    public function view(User $user, DesignConcept $concept): bool
    {
        return $user->can('design', $concept->project) || $user->can('view', $concept->project);
    }

    /**
     * Concepts are design work. Contractors do not create them, and a designer
     * cannot edit a concept on a project they have not accepted.
     */
    public function create(User $user, Project $project): bool
    {
        return $user->can('design', $project) && ! $project->isClosedRecord();
    }

    public function update(User $user, DesignConcept $concept): bool
    {
        return $user->can('design', $concept->project)
            && (int) $concept->designer_id === (int) $user->id
            && $concept->isEditable()
            && ! $concept->project->isClosedRecord();
    }

    public function submit(User $user, DesignConcept $concept): bool
    {
        return $this->update($user, $concept);
    }
}
