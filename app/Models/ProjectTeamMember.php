<?php

namespace App\Models;

/**
 * Confirmed team members are accepted invitations.
 * Keeping them in one table avoids a second copy of the same people.
 */
class ProjectTeamMember extends ProjectInvitation
{
    protected $table = 'project_invitations';

    protected static function booted(): void
    {
        static::addGlobalScope('accepted', function ($query) {
            $query->where('status', ProjectInvitation::STATUS_ACCEPTED);
        });
    }
}
