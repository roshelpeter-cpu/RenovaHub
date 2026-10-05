<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\Contractor\ContractorWorkspaceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request, ContractorWorkspaceService $workspace): View
    {
        $filter = $request->string('filter')->toString();
        $filter = in_array($filter, ['all', 'active', 'planning', 'construction', 'completed'], true) ? $filter : 'all';

        return view('contractor.projects.index', [
            'projects' => $workspace->cards($request->user(), $filter === 'all' ? null : $filter),
            'filter' => $filter,
        ]);
    }

    public function show(Request $request, Project $project, ContractorWorkspaceService $workspace): View
    {
        return $this->section($request, $project, 'overview', $workspace);
    }

    public function section(Request $request, Project $project, string $section, ContractorWorkspaceService $workspace): View
    {
        abort_unless(in_array($section, ['overview', 'quotations', 'budget', 'suppliers', 'tasks', 'documents', 'change-requests'], true), 404);

        $project = $workspace->findAccepted($request->user(), $project);
        $project->load([
            'homeowner',
            'designer.professionalProfile',
            'referenceImages',
            'budgetItems',
            'milestones',
            'progressStages',
            'moodBoard',
            'quotations.items',
            'tasks',
            'documents',
            'changeRequests.requester',
            'supplierOrders.supplier',
            'supplierOrders.payment',
            'supplierPriceRequests.supplier',
            'supplierPriceRequests.prices',
        ]);

        return view('contractor.projects.show', [
            'project' => $project,
            'section' => $section,
        ]);
    }
}
