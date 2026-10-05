<?php

namespace App\Http\Controllers\Designer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Designer\StoreDesignConceptRequest;
use App\Models\DesignConcept;
use App\Models\Project;
use App\Services\ActivityLogService;
use App\Services\DesignerWorkspaceService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DesignConceptController extends Controller
{
    public function index(Request $request, DesignerWorkspaceService $workspace): View
    {
        $concepts = DesignConcept::query()
            ->where('designer_id', $request->user()->id)
            ->whereIn('project_id', $workspace->accepted($request->user())->pluck('id'))
            ->with(['project', 'files'])
            ->latest()
            ->get();

        return view('designer.concepts.index', [
            'concepts' => $concepts,
            'projects' => $workspace->cards($request->user(), 'active'),
        ]);
    }

    public function store(StoreDesignConceptRequest $request, Project $project): RedirectResponse
    {
        $concept = $project->designConcepts()->create([
            'designer_id' => $request->user()->id,
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'notes' => $request->validated('notes'),
            'status' => DesignConcept::STATUS_DRAFT,
        ]);

        foreach ($request->file('images', []) as $image) {
            $concept->files()->create([
                'kind' => 'render',
                'path' => $image->store('concepts/'.$concept->id, 'public'),
                'caption' => $concept->title,
            ]);
        }

        return redirect()->route('designer.concepts.show', [$project, $concept])->with('status', 'Concept saved as a draft.');
    }

    public function show(Request $request, Project $project, DesignConcept $concept, DesignerWorkspaceService $workspace): View
    {
        $workspace->findAccepted($request->user(), $project);
        abort_unless($concept->project_id === $project->id, 404);
        $this->authorize('view', $concept);
        $concept->load('files');

        return view('designer.concepts.show', compact('project', 'concept'));
    }

    public function update(StoreDesignConceptRequest $request, Project $project, DesignConcept $concept): RedirectResponse
    {
        abort_unless($concept->project_id === $project->id, 404);
        $this->authorize('update', $concept);

        $concept->update($request->safe()->only(['title', 'description', 'notes']));

        foreach ($request->file('images', []) as $image) {
            $concept->files()->create([
                'kind' => 'render',
                'path' => $image->store('concepts/'.$concept->id, 'public'),
                'caption' => $concept->title,
            ]);
        }

        return back()->with('status', 'Concept updated.');
    }

    public function submit(Request $request, Project $project, DesignConcept $concept, DesignerWorkspaceService $workspace, ActivityLogService $activity, NotificationService $notifications): RedirectResponse
    {
        $workspace->findAccepted($request->user(), $project);
        abort_unless($concept->project_id === $project->id, 404);
        $this->authorize('submit', $concept);

        $concept->update([
            'status' => DesignConcept::STATUS_AWAITING,
            'submitted_at' => now(),
            'approved_at' => null,
        ]);

        $activity->record($project, $request->user(), 'concept.submitted', $concept->title.' submitted for approval.');
        $notifications->notify(
            $project->homeowner,
            'Design concept ready for review',
            $concept->title.' is waiting for your decision.',
            'design',
            route('homeowner.projects.mood-board', $project),
        );

        return back()->with('status', 'Concept submitted for homeowner approval.');
    }
}
