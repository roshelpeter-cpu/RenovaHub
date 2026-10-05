<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Homeowner\StoreDesignChangeRequest;
use App\Models\DesignChangeRequest;
use App\Models\DesignConcept;
use App\Models\Project;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DesignReviewController extends Controller
{
    public function storeChange(StoreDesignChangeRequest $request, Project $project, ActivityLogService $activity, NotificationService $notifications): RedirectResponse
    {
        $change = $project->designChangeRequests()->create([
            'requested_by' => $request->user()->id,
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'design_impact' => $request->validated('design_impact'),
            'status' => DesignChangeRequest::STATUS_PENDING,
        ]);

        $activity->record($project, $request->user(), 'design-change.submitted', $change->title);
        if ($project->designer) {
            $notifications->notify(
                $project->designer,
                'Design change request',
                $change->title,
                'design',
                route('designer.revisions.index'),
            );
        }

        return back()->with('status', 'Design change request sent.');
    }

    public function decideConcept(Request $request, Project $project, DesignConcept $concept, ActivityLogService $activity, NotificationService $notifications): RedirectResponse
    {
        $this->authorize('view', $project);
        abort_unless($concept->project_id === $project->id, 404);
        abort_unless($concept->status === DesignConcept::STATUS_AWAITING, 403);

        $decision = $request->validate([
            'decision' => ['required', 'in:approve,changes'],
            'note' => ['required_if:decision,changes', 'nullable', 'string', 'max:2000'],
        ]);

        if ($decision['decision'] === 'approve') {
            $concept->update([
                'status' => DesignConcept::STATUS_APPROVED,
                'approved_at' => now(),
            ]);
            $activity->record($project, $request->user(), 'concept.approved', $concept->title.' approved.');
            $title = 'Design concept approved';
        } else {
            $concept->update(['status' => DesignConcept::STATUS_REVISION]);
            $activity->record($project, $request->user(), 'concept.revision', $concept->title.' needs changes.');
            $title = 'Design concept changes requested';
        }

        if ($project->designer) {
            $notifications->notify($project->designer, $title, $decision['note'] ?? $concept->title, 'design', route('designer.concepts.show', [$project, $concept]));
        }

        return back()->with('status', $title.'.');
    }
}
