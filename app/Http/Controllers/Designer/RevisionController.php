<?php

namespace App\Http\Controllers\Designer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Designer\RejectDesignChangeRequest;
use App\Models\DesignChangeRequest;
use App\Models\Project;
use App\Services\ActivityLogService;
use App\Services\DesignerWorkspaceService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RevisionController extends Controller
{
    public function index(Request $request, DesignerWorkspaceService $workspace): View
    {
        $revisions = DesignChangeRequest::query()
            ->whereIn('project_id', $workspace->accepted($request->user())->pluck('id'))
            ->with(['project', 'requester'])
            ->latest()
            ->get();

        return view('designer.revisions.index', compact('revisions'));
    }

    public function project(Request $request, Project $project, DesignerWorkspaceService $workspace): View
    {
        $project = $workspace->findAccepted($request->user(), $project);

        return view('designer.revisions.project', [
            'project' => $project,
            'revisions' => $project->designChangeRequests()->with('requester')->latest()->get(),
        ]);
    }

    public function accept(Request $request, DesignChangeRequest $revision, ActivityLogService $activity, NotificationService $notifications): RedirectResponse
    {
        $this->authorize('decide', $revision);

        $revision->update([
            'status' => DesignChangeRequest::STATUS_ACCEPTED,
            'decided_at' => now(),
        ]);

        $activity->record($revision->project, $request->user(), 'design-change.accepted', $revision->title.' was accepted.');
        $notifications->notify(
            $revision->requester,
            'Design change accepted',
            $revision->title.' will be included in the revised design.',
            'design',
            route('homeowner.projects.mood-board', $revision->project),
        );

        return back()->with('status', 'Change request accepted.');
    }

    public function reject(RejectDesignChangeRequest $request, DesignChangeRequest $revision, ActivityLogService $activity, NotificationService $notifications): RedirectResponse
    {
        $revision->update([
            'status' => DesignChangeRequest::STATUS_REJECTED,
            'rejection_reason' => $request->validated('rejection_reason'),
            'decided_at' => now(),
        ]);

        $activity->record($revision->project, $request->user(), 'design-change.rejected', $revision->title.' was rejected.');
        $notifications->notify(
            $revision->requester,
            'Design change rejected',
            $request->validated('rejection_reason'),
            'design',
            route('homeowner.projects.mood-board', $revision->project),
        );

        return back()->with('status', 'Change request rejected.');
    }

    public function resubmit(Request $request, DesignChangeRequest $revision, ActivityLogService $activity, NotificationService $notifications): RedirectResponse
    {
        abort_unless($revision->status === DesignChangeRequest::STATUS_ACCEPTED, 403);
        $this->authorize('view', $revision);
        abort_unless($request->user()->can('design', $revision->project), 403);

        $request->validate([
            'file' => ['nullable', 'file', 'max:15360', 'mimes:jpg,jpeg,png,pdf,webp'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($request->file('file')) {
            $revision->project->documents()->create([
                'uploaded_by' => $request->user()->id,
                'name' => 'Revised design — '.$revision->title,
                'description' => $request->string('note')->toString(),
                'original_name' => $request->file('file')->getClientOriginalName(),
                'category' => 'design',
                'disk' => 'local',
                'path' => $request->file('file')->store('projects/'.$revision->project_id.'/documents', 'local'),
                'mime' => $request->file('file')->getMimeType(),
                'size' => $request->file('file')->getSize(),
            ]);
        }

        $revision->update(['status' => DesignChangeRequest::STATUS_REVISED]);
        $activity->record($revision->project, $request->user(), 'design-change.revised', 'Revised design submitted for '.$revision->title.'.');
        $notifications->notify(
            $revision->requester,
            'Revised design submitted',
            $revision->title.' has an updated design to review.',
            'design',
            route('homeowner.projects.mood-board', $revision->project),
        );

        return back()->with('status', 'Revised design submitted.');
    }
}
