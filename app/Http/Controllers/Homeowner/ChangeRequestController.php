<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Homeowner\ChangeRequestIndexRequest;
use App\Http\Requests\StoreChangeRequestRequest;
use App\Models\ChangeRequest;
use App\Models\Project;
use App\Services\ActivityLogService;
use App\Services\ChangeRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ChangeRequestController extends Controller
{
    public function index(ChangeRequestIndexRequest $request, ChangeRequestService $changes): View
    {
        Gate::authorize('viewAny', Project::class);

        return view('homeowner.change-requests.index', [
            ...$changes->homeownerBoard($request->user(), $request->validated()),
            'project' => null,
        ]);
    }

    public function project(Project $project, ChangeRequestService $changes): View
    {
        Gate::authorize('view', $project);

        return view('homeowner.change-requests.index', [
            ...$changes->homeownerBoard(request()->user(), [
                'project' => $project->id,
                'status' => null,
                'category' => null,
                'search' => null,
                'from' => null,
                'to' => null,
            ]),
            'project' => $project,
        ]);
    }

    public function create(Project $project): View
    {
        Gate::authorize('create', [ChangeRequest::class, $project]);

        return view('homeowner.change-requests.create', ['project' => $project]);
    }

    public function store(StoreChangeRequestRequest $request, Project $project, ActivityLogService $activity): RedirectResponse
    {
        $path = $request->hasFile('attachment')
            ? $request->file('attachment')->store('projects/'.$project->id.'/changes', 'local')
            : null;

        $change = $project->changeRequests()->create([
            'requested_by' => $request->user()->id,
            'title' => $request->string('title')->toString(),
            'description' => $request->string('description')->toString(),
            'reason' => $request->string('reason')->toString() ?: null,
            'category' => $request->string('category')->toString(),
            'priority' => $request->string('priority')->toString(),
            'status' => ChangeRequest::STATUS_SUBMITTED,
            'attachment' => $path,
        ]);

        $activity->record($project, $request->user(), 'change.submitted', 'Change request submitted: '.$change->title);

        return redirect()
            ->route('homeowner.change-requests.show', [$project, $change])
            ->with('status', 'Change request submitted.');
    }

    public function show(Project $project, ChangeRequest $changeRequest): View
    {
        Gate::authorize('view', $changeRequest);
        abort_unless($changeRequest->project_id === $project->id, 404);

        $changeRequest->load('requester');

        return view('homeowner.change-requests.show', [
            'project' => $project,
            'change' => $changeRequest,
        ]);
    }
}
