<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Project::class);

        $status = $request->string('status')->toString();

        $tasks = ProjectTask::query()
            ->whereIn('project_id', $request->user()->projects()->select('id'))
            ->with(['project', 'assignee'])
            ->when(in_array($status, ['pending', 'in_progress', 'completed', 'blocked'], true), fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('homeowner.tasks.index', [
            'tasks' => $tasks,
            'status' => $status,
            'project' => null,
        ]);
    }

    public function project(Request $request, Project $project): View
    {
        Gate::authorize('view', $project);

        $tasks = $project->tasks()->with('assignee')->latest()->paginate(12);

        return view('homeowner.tasks.index', [
            'tasks' => $tasks,
            'status' => '',
            'project' => $project,
        ]);
    }

    public function show(Project $project, ProjectTask $task): View
    {
        Gate::authorize('view', $project);
        abort_unless($task->project_id === $project->id, 404);

        $task->load('assignee');

        return view('homeowner.tasks.show', [
            'project' => $project,
            'task' => $task,
        ]);
    }
}
