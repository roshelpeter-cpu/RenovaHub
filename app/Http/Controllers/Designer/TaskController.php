<?php

namespace App\Http\Controllers\Designer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Designer\UpdateDesignTaskRequest;
use App\Models\DesignTask;
use App\Models\Project;
use App\Services\DesignerWorkspaceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request, DesignerWorkspaceService $workspace): View
    {
        $projectId = $request->integer('project');

        $tasks = DesignTask::query()
            ->where('designer_id', $request->user()->id)
            ->whereIn('project_id', $workspace->accepted($request->user())->pluck('id'))
            ->when($projectId > 0, fn ($query) => $query->where('project_id', $projectId))
            ->with('project')
            ->orderBy('due_on')
            ->paginate(12)
            ->withQueryString();

        return view('designer.tasks.index', [
            'tasks' => $tasks,
            'projects' => $workspace->cards($request->user()),
            'project' => null,
        ]);
    }

    public function project(Request $request, Project $project, DesignerWorkspaceService $workspace): View
    {
        $project = $workspace->findAccepted($request->user(), $project);

        return view('designer.tasks.project', [
            'project' => $project,
            'tasks' => $project->designTasks()->where('designer_id', $request->user()->id)->orderBy('due_on')->get(),
            'projects' => collect(),
        ]);
    }

    public function update(UpdateDesignTaskRequest $request, DesignTask $task): RedirectResponse
    {
        $data = $request->safe()->only(['status', 'progress', 'notes']);

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('design-tasks/'.$task->id, 'local');
        }

        if ($data['status'] === DesignTask::STATUS_COMPLETED) {
            $data['progress'] = 100;
        }

        $task->update($data);

        return back()->with('status', 'Design task updated.');
    }
}
