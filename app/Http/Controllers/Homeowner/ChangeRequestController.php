<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChangeRequestRequest;
use App\Models\ChangeRequest;
use App\Models\Project;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ChangeRequestController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Project::class);

        $changes = ChangeRequest::query()
            ->whereIn('project_id', request()->user()->projects()->select('id'))
            ->with('project')
            ->latest()
            ->paginate(10);

        return view('homeowner.change-requests.index', [
            'changes' => $changes,
            'project' => null,
        ]);
    }

    public function project(Project $project): View
    {
        Gate::authorize('view', $project);

        return view('homeowner.change-requests.index', [
            'changes' => $project->changeRequests()->with('project')->latest()->paginate(10),
            'project' => $project,
        ]);
    }

    public function create(Project $project): View
    {
        Gate::authorize('update', $project);

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
