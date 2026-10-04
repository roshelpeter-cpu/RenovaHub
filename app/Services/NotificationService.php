<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\WorkspaceNotification;
use Illuminate\Support\Carbon;

class NotificationService
{
    public function notify(User $user, string $title, string $body, string $category, ?string $url = null, ?Carbon $at = null): void
    {
        $user->notify(new WorkspaceNotification($title, $body, $category, $url));

        if ($at === null) {
            return;
        }

        $notification = $user->notifications()->latest()->first();
        $notification?->forceFill(['created_at' => $at, 'updated_at' => $at])->save();
    }
}
