<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Project;
use App\Services\Contractor\ContractorWorkspaceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        return view('contractor.messages.inbox', ['conversationId' => null]);
    }

    public function show(Request $request, Conversation $conversation): View
    {
        $this->authorize('view', $conversation);

        return view('contractor.messages.inbox', ['conversationId' => $conversation->id]);
    }

    public function project(Request $request, Project $project, ContractorWorkspaceService $workspace): View
    {
        $workspace->findAccepted($request->user(), $project);
        $project->load(['homeowner', 'designer', 'referenceImages']);

        $conversation = Conversation::query()
            ->where('project_id', $project->id)
            ->whereHas('participants', fn ($query) => $query->where('users.id', $request->user()->id))
            ->latest('last_message_at')
            ->first();

        return view('contractor.messages.project', [
            'project' => $project,
            'conversationId' => $conversation?->id,
        ]);
    }
}
