<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Homeowner\StoreMoodBoardItemRequest;
use App\Models\Project;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MoodBoardController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Project::class);

        $owned = $request->user()->projects()->orderBy('name')->get();
        $selected = $request->integer('project');

        if ($selected > 0 && ! $owned->contains('id', $selected)) {
            abort(404);
        }

        $boards = $request->user()->projects()
            ->with(['moodBoard.items', 'moodBoard.author.professionalProfile', 'designer.professionalProfile'])
            ->when($selected > 0, fn ($query) => $query->where('id', $selected))
            ->orderBy('name')
            ->get();

        return view('homeowner.mood-board.index', [
            'projects' => $owned,
            'boards' => $boards,
            'selected' => $selected,
        ]);
    }

    public function show(Project $project): View
    {
        Gate::authorize('view', $project);

        $project->load(['moodBoard.author', 'moodBoard.items', 'moodBoard.feedback.author', 'designConcepts', 'designChangeRequests']);

        return view('homeowner.mood-board.show', ['project' => $project]);
    }

    public function storeItem(StoreMoodBoardItemRequest $request, ActivityLogService $activity): RedirectResponse
    {
        $project = $request->project();
        abort_unless($project !== null, 404);

        $board = $project->moodBoard()->firstOrCreate([], [
            'created_by' => $project->designer_id ?? $request->user()->id,
            'title' => 'Design Mood Board',
            'summary' => 'Inspiration collected for '.$project->name.'.',
            'version' => 1,
        ]);

        $colour = $request->string('colour')->toString();
        if ($colour !== '' && ! str_starts_with($colour, '#')) {
            $colour = '#'.$colour;
        }

        $board->items()->create([
            'kind' => $request->string('kind')->toString(),
            'title' => $request->string('title')->toString(),
            'body' => $request->string('body')->toString() ?: null,
            'colour' => $colour !== '' ? $colour : null,
            'image' => $request->hasFile('image')
                ? $request->file('image')->store('mood-boards/'.$board->id, 'public')
                : null,
        ]);

        $activity->record($project, $request->user(), 'moodboard.inspiration', 'Inspiration was added to the mood board.');

        return back()->with('status', 'Inspiration added.');
    }

    public function feedback(): RedirectResponse
    {
        // Designer and contractor ratings belong on the completed Feedback tab.
        abort(403);
    }

    public function approve(Project $project, \App\Services\MoodBoardWorkflowService $boards): RedirectResponse
    {
        Gate::authorize('contribute', $project);

        $board = $project->moodBoard;
        abort_unless($board !== null, 404);
        $boards->approve($board, request()->user());

        return back()->with('status', 'Mood board approved.');
    }

    public function requestChanges(\App\Http\Requests\Homeowner\RequestMoodBoardChangesRequest $request, Project $project, \App\Services\MoodBoardWorkflowService $boards): RedirectResponse
    {
        $board = $project->moodBoard;
        abort_unless($board !== null, 404);
        $boards->requestChanges($board, $request->user(), $request->validated('revision_note'));

        return back()->with('status', 'Change request sent to the designer.');
    }
}
