<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', \App\Models\Project::class);

        return view('homeowner.messages.inbox', ['conversationId' => null]);
    }

    public function show(Conversation $conversation): View
    {
        Gate::authorize('view', $conversation);

        return view('homeowner.messages.inbox', ['conversationId' => $conversation->id]);
    }
}
