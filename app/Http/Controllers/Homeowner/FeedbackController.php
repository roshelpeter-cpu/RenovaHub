<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Homeowner\StoreProjectFeedbackRequest;
use App\Models\Project;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function show(Project $project): View
    {
        Gate::authorize('view', $project);
        abort_unless($project->isCompleted(), 404);

        $project->load(['feedback.professional.professionalProfile', 'designer.professionalProfile', 'contractor.professionalProfile']);

        return view('homeowner.projects.feedback', [
            'project' => $project,
            'designerFeedback' => $project->feedback->firstWhere('role', 'designer'),
            'contractorFeedback' => $project->feedback->firstWhere('role', 'contractor'),
        ]);
    }

    public function store(StoreProjectFeedbackRequest $request, Project $project, ActivityLogService $activity): RedirectResponse
    {
        $role = $request->string('role')->toString();
        $professionalId = $role === 'designer' ? $project->designer_id : $project->contractor_id;
        abort_unless($professionalId !== null, 422);

        $exists = $project->feedback()->where('role', $role)->exists();
        abort_if($exists, 422);

        $path = $request->hasFile('attachment')
            ? $request->file('attachment')->store('projects/'.$project->id.'/feedback', 'local')
            : null;

        $project->feedback()->create([
            'homeowner_id' => $request->user()->id,
            'professional_id' => $professionalId,
            'role' => $role,
            'rating' => $request->integer('rating'),
            'title' => $request->string('title')->toString(),
            'comment' => $request->string('comment')->toString(),
            'attachment' => $path,
        ]);

        $activity->record($project, $request->user(), 'project.feedback', ucfirst($role).' feedback was submitted.');

        return back()->with('status', ucfirst($role).' feedback submitted.');
    }
}
