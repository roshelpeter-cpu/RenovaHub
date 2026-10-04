<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectTaskRequest;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;

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
}
