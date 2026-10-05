<?php

namespace App\Services\Contractor;

use App\Models\ChangeRequest;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class ChangeRequestService
{
    public function __construct(private NotificationService $notifications) {}

    /**
     * The contractor records cost, time and feasibility. Approval remains a
     * homeowner decision and is not written by this method.
     */
    public function respond(User $contractor, ChangeRequest $change, array $impact): ChangeRequest
    {
        return DB::transaction(function () use ($contractor, $change, $impact) {
            $change->update([
                'status' => ChangeRequest::STATUS_AWAITING_APPROVAL,
                'contractor_response' => $impact['contractor_response'],
                'cost_impact' => $impact['cost_impact'],
                'timeline_impact' => $impact['timeline_impact'],
                'feasibility' => $impact['feasibility'],
                'design_affected' => (bool) $impact['design_affected'],
            ]);

            $homeowner = $change->project->homeowner;

            if ($homeowner) {
                $this->notifications->notify(
                    $homeowner,
                    'Cost impact ready for '.$change->title,
                    'The contractor reviewed the change request. Your approval is still required.',
                    'changes',
                    route('homeowner.change-requests.show', [$change->project, $change]),
                );
            }

            if ($impact['design_affected'] && $change->project->designer) {
                $this->notifications->notify(
                    $change->project->designer,
                    'Design may be affected',
                    $change->title.' on '.$change->project->name.' may need a design review.',
                    'design',
                    route('designer.revisions.index'),
                );
            }

            return $change->fresh();
        });
    }
}
