<?php

namespace App\Events;

use App\Models\ProjectInvitation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectInvitationResponded
{
    use Dispatchable, SerializesModels;

    public function __construct(public ProjectInvitation $invitation, public bool $accepted) {}
}
