<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Homeowner\RequestFinalDesignChangesRequest;
use App\Models\Project;
use App\Services\FinalDesignWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class FinalDesignController extends Controller
{
    public function show(Project $project): View
    {
        Gate::authorize('view', $project);

        $concept = $project->designConcepts()->where('title', 'Final Design Package')->with('files')->latest('id')->first()
            ?? $project->designConcepts()->where('status', 'approved')->with('files')->latest('approved_at')->first();

        $project->load(['materialRequirements', 'designChangeRequests.requester', 'designer.professionalProfile']);

        return view('homeowner.projects.final-design', [
            'project' => $project,
            'concept' => $concept,
        ]);
    }

    public function approve(Project $project, FinalDesignWorkflowService $designs): RedirectResponse
    {
        Gate::authorize('contribute', $project);
        $concept = $project->designConcepts()->where('title', 'Final Design Package')->firstOrFail();
        $designs->approve($concept, request()->user());

        return back()->with('status', 'Final design approved for construction.');
    }

    public function requestChanges(RequestFinalDesignChangesRequest $request, Project $project, FinalDesignWorkflowService $designs): RedirectResponse
    {
        $concept = $project->designConcepts()->where('title', 'Final Design Package')->firstOrFail();
        $designs->requestChanges($concept, $request->user(), $request->validated('title'), $request->validated('comment'));

        return back()->with('status', 'Change request sent to the designer.');
    }
}
