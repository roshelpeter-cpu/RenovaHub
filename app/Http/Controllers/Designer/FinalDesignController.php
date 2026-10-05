<?php

namespace App\Http\Controllers\Designer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Designer\StoreFinalDesignFileRequest;
use App\Http\Requests\Designer\StoreFinalMaterialRequest;
use App\Models\DesignChangeRequest;
use App\Models\Project;
use App\Services\DesignerWorkspaceService;
use App\Services\FinalDesignWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinalDesignController extends Controller
{
    public function show(Request $request, Project $project, DesignerWorkspaceService $workspace, FinalDesignWorkflowService $designs): View
    {
        $project = $workspace->findAccepted($request->user(), $project);
        $concept = $designs->package($project, $request->user());
        $concept->load('files');
        $project->load(['materialRequirements' => fn ($query) => $query->where('design_concept_id', $concept->id), 'designChangeRequests.requester']);

        return view('designer.final-design.show', compact('project', 'concept'));
    }

    public function storeFile(StoreFinalDesignFileRequest $request, Project $project, DesignerWorkspaceService $workspace, FinalDesignWorkflowService $designs): RedirectResponse
    {
        $project = $workspace->findAccepted($request->user(), $project);
        $concept = $designs->package($project, $request->user());
        $designs->addFile($concept, $request->validated('kind'), $request->file('image'), $request->validated('caption'));

        return back()->with('status', 'Final design file saved.');
    }

    public function storeMaterial(StoreFinalMaterialRequest $request, Project $project, DesignerWorkspaceService $workspace, FinalDesignWorkflowService $designs): RedirectResponse
    {
        $project = $workspace->findAccepted($request->user(), $project);
        $designs->addMaterial($designs->package($project, $request->user()), $request->validated());

        return back()->with('status', 'Material added to the final design.');
    }

    public function submit(Request $request, Project $project, DesignerWorkspaceService $workspace, FinalDesignWorkflowService $designs): RedirectResponse
    {
        $project = $workspace->findAccepted($request->user(), $project);
        $concept = $project->designConcepts()->where('title', 'Final Design Package')->firstOrFail();
        $designs->submit($concept, $request->user());

        return back()->with('status', 'Final design sent for homeowner approval.');
    }

    public function respond(Request $request, DesignChangeRequest $change, FinalDesignWorkflowService $designs): RedirectResponse
    {
        $project = $change->project;
        abort_unless($request->user()->can('design', $project), 403);
        abort_unless($change->design_impact === 'final_design', 404);

        $designs->respond($change, $request->user(), $request->string('decision')->toString(), $request->string('note')->toString() ?: null);

        return back()->with('status', 'Change request updated.');
    }
}
