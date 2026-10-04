<?php

namespace App\Listeners;

use App\Events\ProjectInvitationResponded;
use App\Events\QuotationDecided;
use App\Services\ActivityLogService;

/**
 * Live workflow events become project history.
 * Seeded history is written directly so it can keep its original dates.
 */
class RecordWorkspaceActivity
{
    public function __construct(private ActivityLogService $activity) {}

    public function handleInvitation(ProjectInvitationResponded $event): void
    {
        $invitation = $event->invitation;
        $name = $invitation->professional?->name ?? 'A professional';

        $this->activity->record(
            $invitation->project,
            $invitation->professional,
            $event->accepted ? 'invitation.accepted' : 'invitation.declined',
            $event->accepted
                ? $name.' accepted the '.$invitation->role.' invitation.'
                : $name.' declined the '.$invitation->role.' invitation.',
        );
    }

    public function handleQuotation(QuotationDecided $event): void
    {
        $this->activity->record(
            $event->quotation->project,
            $event->quotation->project->homeowner,
            'quotation.'.$event->decision,
            'Quotation '.$event->quotation->number.' was marked '.$event->decision.'.',
        );
    }
}
