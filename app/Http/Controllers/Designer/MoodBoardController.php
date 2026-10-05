<?php

namespace App\Http\Controllers\Designer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Designer\StoreMoodBoardItemRequest;
use App\Models\Project;
use App\Services\DesignerWorkspaceService;
use App\Services\MoodBoardWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MoodBoardController extends Controller
{
    public function index(Request $request, DesignerWorkspaceService $workspace): View
    {
        $projects = $workspace->accepted($request->user())
            ->with(['moodBoard.items', 'homeowner', 'referenceImages'])
            ->orderBy('name')
            ->get();

        return view('designer.mood-boards.index', compact('projects'));
    }

    public function show(Request $request, Project $project, DesignerWorkspaceService $workspace, MoodBoardWorkflowService $boards): View
    {
        $project = $workspace->findAccepted($request->user(), $project);
        $board = $boards->ensureBoard($project, $request->user());
        $board->load('items');
        $project->setRelation('moodBoard', $board);

        return view('designer.mood-boards.show', compact('project', 'board'));
    }

    public function storeItem(StoreMoodBoardItemRequest $request, Project $project, MoodBoardWorkflowService $boards): RedirectResponse
    {
        $board = $boards->ensureBoard($project, $request->user());
        $boards->addItem($board, $request->user(), $request->validated(), $request->file('image'));

        return back()->with('status', 'Mood board item added.');
    }

    public function submit(Request $request, Project $project, DesignerWorkspaceService $workspace, MoodBoardWorkflowService $boards): RedirectResponse
    {
        $project = $workspace->findAccepted($request->user(), $project);
        $board = $project->moodBoard ?? abort(404);
        $boards->submit($board, $request->user());

        return back()->with('status', 'Mood board submitted for homeowner approval.');
    }
}
