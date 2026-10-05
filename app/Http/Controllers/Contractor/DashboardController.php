<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Conversation;
use App\Models\ProjectTask;
use App\Services\Contractor\ContractorWorkspaceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, ContractorWorkspaceService $workspace): View
    {
        $contractor = $request->user();
        $projects = $workspace->cards($contractor)->take(3);
        $ids = $workspace->accepted($contractor)->pluck('id');

        return view('contractor.dashboard', [
            'summary' => $workspace->summary($contractor),
            'projects' => $projects,
            'activity' => ActivityLog::query()->whereIn('project_id', $ids)->latest()->limit(6)->get(),
            'tasks' => ProjectTask::query()
                ->whereIn('project_id', $ids)
                ->where('status', '!=', ProjectTask::STATUS_COMPLETED)
                ->with('project')
                ->orderBy('due_on')
                ->limit(5)
                ->get(),
            'messages' => Conversation::query()
                ->whereHas('participants', fn ($query) => $query->where('users.id', $contractor->id))
                ->with(['participants', 'project'])
                ->orderByDesc('last_message_at')
                ->limit(3)
                ->get(),
        ]);
    }
}
