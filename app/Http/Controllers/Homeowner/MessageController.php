<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Message;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Project::class);

        $projects = request()->user()->projects()
            ->with(['messages' => fn ($query) => $query->latest()->limit(1), 'messages.sender'])
            ->withCount(['messages as unread_messages_count' => function ($query) {
                $query->whereNull('read_at')->where('sender_id', '!=', request()->user()->id);
            }])
            ->latest()
            ->get();

        return view('homeowner.messages.index', ['projects' => $projects]);
    }

    public function show(Project $project): View
    {
        Gate::authorize('view', $project);

        $project->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', request()->user()->id)
            ->update(['read_at' => now()]);

        $project->load(['messages.sender', 'messages.attachments', 'designer', 'contractor']);

        return view('homeowner.messages.show', ['project' => $project]);
    }

    public function store(StoreMessageRequest $request, Project $project): RedirectResponse
    {
        $message = $project->messages()->create([
            'sender_id' => $request->user()->id,
            'body' => $request->string('body')->toString(),
            'read_at' => now(),
        ]);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $message->attachments()->create([
                'path' => $file->store('projects/'.$project->id.'/messages', 'local'),
                'original_name' => $file->getClientOriginalName(),
            ]);
        }

        return back()->with('status', 'Message sent.');
    }
}
