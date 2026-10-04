<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDesignFeedbackRequest;
use App\Models\Project;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MoodBoardController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Project::class);

        $projects = request()->user()->projects()->with('moodBoard.author')->latest()->get();

        return view('homeowner.mood-board.index', ['projects' => $projects]);
    }

    public function show(Project $project): View
    {
        Gate::authorize('view', $project);

        $project->load(['moodBoard.author', 'moodBoard.items', 'moodBoard.feedback.author']);

        return view('homeowner.mood-board.show', ['project' => $project]);
    }

    public function feedback(StoreDesignFeedbackRequest $request, Project $project, ActivityLogService $activity): RedirectResponse
    {
        $board = $project->moodBoard;
        abort_unless($board !== null, 404);

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('projects/'.$project->id.'/feedback', 'local');
        }

        $board->feedback()->create([
            'user_id' => $request->user()->id,
            'title' => $request->string('title')->toString(),
            'comment' => $request->string('comment')->toString(),
            'attachment' => $path,
        ]);

        $activity->record($project, $request->user(), 'design.feedback', 'Design feedback was added to the mood board.');

        return back()->with('status', 'Design feedback sent.');
    }

    public function approve(Project $project, ActivityLogService $activity, NotificationService $notifications): RedirectResponse
    {
        Gate::authorize('update', $project);

        $board = $project->moodBoard;
        abort_unless($board !== null, 404);

        $board->update(['approved_at' => now()]);
        $activity->record($project, request()->user(), 'moodboard.approved', 'The mood board was approved.');
        $notifications->notify(request()->user(), 'Mood board approved', $project->name.' design direction is approved.', 'design', route('homeowner.projects.mood-board', $project));

        return back()->with('status', 'Mood board approved.');
    }
}
