<?php

namespace App\Policies;

use App\Models\DesignTask;
use App\Models\User;

class DesignTaskPolicy
{
    /**
     * Design tasks are the designer's own schedule. Construction tasks stay on ProjectTask.
     */
    public function update(User $user, DesignTask $task): bool
    {
        return $user->can('design', $task->project) && (int) $task->designer_id === (int) $user->id;
    }
}
