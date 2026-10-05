<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Models\MaterialRequirement;
use App\Services\Contractor\ContractorWorkspaceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaterialRequirementController extends Controller
{
    public function index(Request $request, ContractorWorkspaceService $workspace): View
    {
        $projects = $workspace->accepted($request->user())->orderBy('name')->get();
        $projectId = $request->integer('project');

        $materials = MaterialRequirement::query()
            ->whereIn('project_id', $projects->pluck('id'))
            ->when($projectId, fn ($query) => $query->where('project_id', $projectId))
            ->when($request->filled('room'), fn ($query) => $query->where('room', $request->string('room')))
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->string('category')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->with('project')
            ->orderBy('room')
            ->get();

        return view('contractor.materials.index', [
            'projects' => $projects,
            'materials' => $materials,
            'rooms' => MaterialRequirement::query()->whereIn('project_id', $projects->pluck('id'))->distinct()->pluck('room'),
            'categories' => MaterialRequirement::query()->whereIn('project_id', $projects->pluck('id'))->distinct()->pluck('category'),
        ]);
    }
}
