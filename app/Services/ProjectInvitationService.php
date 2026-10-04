<?php

namespace App\Services;

use App\Events\ProjectInvitationResponded;
use App\Models\Project;
use App\Models\ProjectInvitation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProjectInvitationService
{
    public function __construct(private NotificationService $notifications) {}

    /**
     * The homeowner's selection is stored on the project and as a pending invite.
     * The professional must accept before the team is treated as confirmed.
     */
    public function invite(Project $project, string $role, ?int $userId): ?ProjectInvitation
    {
        if ($userId === null) {
            return null;
        }

        $existing = $project->invitations()
            ->where('role', $role)
            ->where('user_id', $userId)
            ->latest('id')
            ->first();

        if ($existing !== null && $existing->status !== ProjectInvitation::STATUS_DECLINED) {
            return $existing;
        }

        $project->invitations()
            ->where('role', $role)
            ->where('status', ProjectInvitation::STATUS_PENDING)
            ->where('user_id', '!=', $userId)
            ->update(['status' => ProjectInvitation::STATUS_DECLINED, 'responded_at' => now()]);

        return $project->invitations()->create([
            'user_id' => $userId,
            'role' => $role,
            'status' => ProjectInvitation::STATUS_PENDING,
        ]);
    }

    /**
     * Only the invited person can answer. The browser cannot post a role or a project owner.
     */
    public function respond(ProjectInvitation $invitation, User $actor, string $decision): ProjectInvitation
    {
        if ($invitation->user_id !== $actor->id) {
            throw ValidationException::withMessages([
                'invitation' => 'Only the invited professional can respond.',
            ]);
        }

        if (! in_array($decision, [ProjectInvitation::STATUS_ACCEPTED, ProjectInvitation::STATUS_DECLINED], true)) {
            throw ValidationException::withMessages([
                'decision' => 'Choose accept or decline.',
            ]);
        }

        return DB::transaction(function () use ($invitation, $actor, $decision) {
            $invitation->update([
                'status' => $decision,
                'responded_at' => now(),
            ]);

            $project = $invitation->project()->lockForUpdate()->first();
            $this->refreshProjectStatus($project);

            $name = $actor->professionalProfile?->displayName() ?? $actor->name;
            $accepted = $decision === ProjectInvitation::STATUS_ACCEPTED;

            $this->notifications->notify(
                $project->homeowner,
                $accepted ? $name.' accepted your invitation.' : $name.' declined the project invitation.',
                $accepted
                    ? 'The project team can continue once every invite is accepted.'
                    : 'Choose another '.($invitation->role === 'designer' ? 'designer' : 'contractor').'.',
                'projects',
                route('homeowner.projects.team', $project),
            );

            ProjectInvitationResponded::dispatch($invitation->fresh(), $accepted);

            return $invitation->fresh();
        });
    }

    /**
     * Confirmed means both invited roles have accepted.
     * A project already under construction is not moved backwards.
     */
    public function refreshProjectStatus(Project $project): void
    {
        $roles = array_filter([
            $project->designer_id ? 'designer' : null,
            $project->contractor_id ? 'contractor' : null,
        ]);

        if ($roles === []) {
            return;
        }

        $accepted = $project->invitations()
            ->where('status', ProjectInvitation::STATUS_ACCEPTED)
            ->whereIn('role', $roles)
            ->pluck('role')
            ->unique();

        $bothAccepted = collect($roles)->every(fn (string $role) => $accepted->contains($role));

        if ($bothAccepted && in_array($project->status, [Project::STATUS_PLANNING, Project::STATUS_AWAITING_TEAM, Project::STATUS_DRAFT], true)) {
            $project->update(['status' => Project::STATUS_CONFIRMED]);
        }
    }
}
