<?php

namespace App\Http\Controllers\Designer;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\ProjectFeedback;
use App\Services\DesignerWorkspaceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request, DesignerWorkspaceService $workspace): View
    {
        $filter = $request->string('filter')->toString();
        $filter = in_array($filter, ['active', 'awaiting', 'completed'], true) ? $filter : 'all';

        return view('designer.projects.index', [
            'filter' => $filter,
            'projects' => $workspace->cards($request->user(), $filter === 'all' ? null : $filter),
        ]);
    }

    public function designWork(Request $request, DesignerWorkspaceService $workspace): View
    {
        $projects = $workspace->cards($request->user(), 'active');
        // An empty filter means every accepted project. A chosen id must still
        // belong to this designer; unknown ids fall back to the full list.
        $selected = $request->filled('project') ? (int) $request->integer('project') : null;
        $focus = $selected ? $projects->firstWhere('id', $selected) : null;
        $shown = $focus ? collect([$focus]) : $projects;

        return view('designer.design-work', [
            'projects' => $projects,
            'focus' => $focus,
            'boards' => $shown->map(fn ($project) => [
                'project' => $project,
                'stages' => $workspace->stages($project),
            ]),
        ]);
    }

    public function show(Request $request, Project $project, DesignerWorkspaceService $workspace): View
    {
        $project = $workspace->findAccepted($request->user(), $project);
        $project->load(['homeowner', 'contractor.professionalProfile', 'referenceImages', 'moodBoard', 'designConcepts']);

        return view('designer.projects.show', [
            'project' => $project,
            'stages' => $workspace->stages($project),
            'activity' => ActivityLog::query()->where('project_id', $project->id)->latest()->limit(6)->get(),
            'feedback' => ProjectFeedback::query()
                ->where('project_id', $project->id)
                ->where('role', 'designer')
                ->latest()
                ->get(),
        ]);
    }

    public function brief(Request $request, Project $project, DesignerWorkspaceService $workspace): View
    {
        $project = $workspace->findAccepted($request->user(), $project);
        $project->load(['homeowner', 'referenceImages']);

        return view('designer.projects.brief', ['project' => $project]);
    }
}
