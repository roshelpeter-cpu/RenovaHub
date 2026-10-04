<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Carbon;

class ActivityLogService
{
    /**
     * @param  array<string, mixed>|null  $properties
     */
    public function record(
        ?Project $project,
        ?User $user,
        string $event,
        string $description,
        ?array $properties = null,
        ?Carbon $at = null,
    ): ActivityLog {
        $log = ActivityLog::query()->create([
            'project_id' => $project?->id,
            'user_id' => $user?->id,
            'event' => $event,
            'description' => $description,
            'properties' => $properties,
        ]);

        if ($at !== null) {
            $log->forceFill(['created_at' => $at, 'updated_at' => $at])->save();
        }

        return $log;
    }
}
