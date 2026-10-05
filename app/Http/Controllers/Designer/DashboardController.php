<?php

namespace App\Http\Controllers\Designer;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Conversation;
use App\Models\DesignTask;
use App\Services\DesignerWorkspaceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, DesignerWorkspaceService $workspace): View
    {
        $designer = $request->user();
        $projects = $workspace->cards($designer, 'active')->take(3);
        $ids = $workspace->accepted($designer)->pluck('id');

        return view('designer.dashboard', [
            'summary' => $workspace->summary($designer),
            'projects' => $projects,
            'activity' => ActivityLog::query()
                ->whereIn('project_id', $ids)
                ->latest()
                ->limit(6)
                ->get(),
            'tasks' => DesignTask::query()
                ->where('designer_id', $designer->id)
                ->where('status', '!=', DesignTask::STATUS_COMPLETED)
                ->with('project')
                ->orderBy('due_on')
                ->limit(5)
                ->get(),
            'messages' => Conversation::query()
                ->whereHas('participants', fn ($query) => $query->where('users.id', $designer->id))
                ->with(['participants', 'project'])
                ->orderByDesc('last_message_at')
                ->limit(3)
                ->get(),
        ]);
    }
}
