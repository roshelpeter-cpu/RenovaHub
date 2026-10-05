<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;

class ProjectTaskPolicy
{
    /**
     * A task is visible only when the homeowner owns the project it belongs to.
     */
    public function view(User $user, ProjectTask $task): bool
    {
        return $user->can('view', $task->project) || $user->can('construct', $task->project);
    }

    /**
     * The assigned contractor owns the task list. Homeowners can read
     * progress, but they do not create or edit construction tasks.
     */
    public function create(User $user, Project $project): bool
    {
        return $this->contractorManages($user, $project);
    }

    public function update(User $user, ProjectTask $task): bool
    {
        return $this->contractorManages($user, $task->project);
    }

    public function delete(User $user, ProjectTask $task): bool
    {
        return $this->update($user, $task);
    }

    private function contractorManages(User $user, Project $project): bool
    {
        return $user->isContractor()
            && (int) $project->contractor_id === (int) $user->id
            && ! $project->isClosedRecord();
    }
}
