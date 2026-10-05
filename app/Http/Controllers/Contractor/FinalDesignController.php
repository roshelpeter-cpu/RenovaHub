<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Models\DesignConcept;
use App\Models\Project;
use App\Services\Contractor\ContractorWorkspaceService;
use App\Services\Contractor\FinalDesignService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Contractors can read an approved final design. They cannot edit the designer's package.
 */
class FinalDesignController extends Controller
{
    public function index(Request $request, ContractorWorkspaceService $workspace): View
    {
        $projects = $workspace->accepted($request->user())
            ->with(['designConcepts.files', 'materialRequirements', 'referenceImages'])
            ->orderBy('name')
            ->get();

        return view('contractor.final-designs.index', compact('projects'));
    }

    public function show(Request $request, Project $project, ContractorWorkspaceService $workspace, FinalDesignService $designs): View
    {
        $project = $workspace->findAccepted($request->user(), $project);
        $concept = $designs->approved($project);
        abort_if($concept === null, 404);

        return view('contractor.final-designs.show', [
            'project' => $project->load('designer'),
            'concept' => $concept,
            'materials' => $designs->materials($project),
            'history' => $project->designConcepts()->with('designer')->latest()->get(),
        ]);
    }
}
