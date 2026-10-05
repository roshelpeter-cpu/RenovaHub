<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contractor\StoreTaskRequest;
use App\Http\Requests\Contractor\UpdateTaskRequest;
use App\Http\Requests\StoreProjectTaskRequest;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Services\ActivityLogService;
use App\Services\Contractor\ContractorWorkspaceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Construction tasks are created by the contractor assigned to the project.
     */
    public function store(StoreProjectTaskRequest $request, Project $project, ActivityLogService $activity): RedirectResponse
    {
        $task = $project->tasks()->create([
            'assignee_id' => $request->integer('assignee_id') ?: $request->user()->id,
            'name' => $request->string('name')->toString(),
            'description' => $request->string('description')->toString() ?: null,
            'category' => $request->string('category')->toString(),
            'status' => ProjectTask::STATUS_PENDING,
            'priority' => $request->string('priority')->toString() ?: 'normal',
            'started_on' => $request->date('started_on'),
            'due_on' => $request->date('due_on'),
            'progress' => 0,
        ]);

        $activity->record($project, $request->user(), 'task.created', 'Task created: '.$task->name);

        return back()->with('status', 'Task created.');
    }

    public function index(Request $request, ContractorWorkspaceService $workspace): View
    {
        $projects = $workspace->accepted($request->user())->orderBy('name')->get();
        $projectId = $request->integer('project');
        $status = $request->string('status')->toString();

        if ($projectId > 0 && ! $projects->contains('id', $projectId)) {
            abort(404);
        }

        $tasks = ProjectTask::query()
            ->whereIn('project_id', $projects->pluck('id'))
            ->when($projectId > 0, fn ($query) => $query->where('project_id', $projectId))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->with('project')
            ->orderBy('due_on')
            ->get();

        return view('contractor.tasks.index', compact('tasks', 'projects', 'projectId', 'status'));
    }

    public function storeForWorkspace(StoreTaskRequest $request, ActivityLogService $activity): RedirectResponse
    {
        $project = Project::query()->findOrFail($request->integer('project_id'));
        $status = $request->string('status')->toString();

        $task = $project->tasks()->create([
            'assignee_id' => $request->user()->id,
            'assignee_label' => $request->input('assignee_label') ?: 'Construction Team',
            'name' => $request->string('name')->toString(),
            'notes' => $request->input('notes'),
            'description' => $request->input('notes'),
            'category' => $request->string('category')->toString(),
            'status' => $status,
            'priority' => $request->string('priority')->toString(),
            'started_on' => $request->date('started_on'),
            'due_on' => $request->date('due_on'),
            'progress' => $status === 'completed' ? 100 : $request->integer('progress'),
        ]);

        $activity->record($project, $request->user(), 'task.created', 'Construction task created: '.$task->name);

        return redirect()->route('contractor.tasks.index')->with('status', 'Task created.');
    }

    public function update(UpdateTaskRequest $request, ProjectTask $task, ActivityLogService $activity): RedirectResponse
    {
        $status = $request->string('status')->toString();

        $task->update([
            'name' => $request->string('name')->toString(),
            'notes' => $request->input('notes'),
            'assignee_label' => $request->input('assignee_label'),
            'priority' => $request->string('priority')->toString(),
            'started_on' => $request->date('started_on'),
            'due_on' => $request->date('due_on'),
            'status' => $status,
            'progress' => $status === 'completed' ? 100 : $request->integer('progress'),
        ]);

        $activity->record($task->project, $request->user(), 'task.updated', 'Construction task updated: '.$task->name);

        return back()->with('status', 'Task updated.');
    }
}
