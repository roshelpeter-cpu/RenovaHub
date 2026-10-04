<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Homeowner\TaskIndexRequest;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Services\TaskService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(TaskIndexRequest $request, TaskService $tasks): View
    {
        Gate::authorize('viewAny', Project::class);

        $filters = $tasks->filters($request->user(), $request->validated());

        return view('homeowner.tasks.index', [
            ...$tasks->globalIndex($request->user(), $filters),
            'project' => null,
        ]);
    }

    public function project(Project $project): View
    {
        Gate::authorize('view', $project);

        return view('homeowner.tasks.index', [
            'tasks' => $project->tasks()->with(['assignee.professionalProfile', 'project'])->orderBy('due_on')->paginate(12),
            'status' => '',
            'project' => $project,
            'projects' => collect(),
            'summary' => null,
            'filters' => ['project' => null, 'status' => '', 'category' => '', 'search' => ''],
        ]);
    }

    public function show(Project $project, ProjectTask $task): View
    {
        Gate::authorize('view', $project);
        Gate::authorize('view', $task);
        abort_unless($task->project_id === $project->id, 404);

        $task->load('assignee.professionalProfile');

        return view('homeowner.tasks.show', [
            'project' => $project,
            'task' => $task,
        ]);
    }
}
