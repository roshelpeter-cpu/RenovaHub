<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectBudgetRequest;
use App\Http\Requests\UpdateProjectLocationRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Requests\UpdateProjectTeamRequest;
use App\Models\Project;
use App\Models\User;
use App\Services\ProjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * @var list<string>
     */
    private const TABS = [
        'overview',
        'tasks',
        'documents',
        'mood-board',
        'quotations',
        'change-requests',
        'payments',
    ];

    /**
     * My Projects is a browsing catalogue, not another dashboard.
     * Counts come from every owned project so filters never fake the summary tiles.
     */
    public function index(Request $request, ProjectService $projects): View
    {
        Gate::authorize('viewAny', Project::class);

        return view('homeowner.projects.index', $projects->catalogue(
            $request->user(),
            $request->string('status')->toString(),
            $request->string('sort')->toString(),
        ));
    }

    public function create(): View
    {
        Gate::authorize('create', Project::class);

        return view('homeowner.projects.create');
    }

    public function store(StoreProjectRequest $request, ProjectService $projects): RedirectResponse
    {
        $project = $projects->createForHomeowner(
            $request->user(),
            $request->safe()->only(['name', 'description', 'requirements', 'additional_instructions', 'renovation_type', 'property_type']),
        );

        foreach ($request->file('reference_images', []) as $image) {
            $project->referenceImages()->create([
                'path' => $image->store('projects/'.$project->id.'/references', 'public'),
                'original_name' => $image->getClientOriginalName(),
            ]);
        }

        return redirect()
            ->route('homeowner.projects.location', $project)
            ->with('status', 'Project details saved. Add the property location next.');
    }

    public function show(Request $request, Project $project): View|RedirectResponse
    {
        Gate::authorize('view', $project);

        $tab = $request->string('tab')->toString();
        $tabRoutes = [
            'design-process' => null,
            'tasks' => 'homeowner.projects.tasks',
            'documents' => 'homeowner.projects.documents',
            'mood-board' => 'homeowner.projects.mood-board',
            'quotations' => 'homeowner.projects.quotations',
            'change-requests' => 'homeowner.projects.change-requests',
            'payments' => 'homeowner.projects.payments',
            'messages' => 'homeowner.projects.messages',
        ];

        if (isset($tabRoutes[$tab]) && $tabRoutes[$tab] !== null) {
            return redirect()->route($tabRoutes[$tab], $project);
        }

        $project->load([
            'designer.professionalProfile',
            'contractor.professionalProfile',
            'invitations.professional',
            'progressStages',
            'milestones',
            'quotations',
            'payments',
            'activity.user',
            'referenceImages',
        ]);

        foreach (['designer', 'contractor'] as $role) {
            $member = $project->{$role};
            $member?->professionalProfile?->setRelation('user', $member);
        }

        return view('homeowner.projects.show', [
            'project' => $project,
            'section' => in_array($tab, ['design-process'], true) ? $tab : 'overview',
        ]);
    }

    /**
     * Full project history stays on its own tab so the overview remains a summary.
     */
    public function activity(Project $project): View
    {
        Gate::authorize('view', $project);

        return view('homeowner.projects.activity', [
            'project' => $project,
            'entries' => $project->activity()->with('user')->latest()->paginate(20),
        ]);
    }

    public function edit(Project $project): View
    {
        Gate::authorize('update', $project);

        return view('homeowner.projects.edit', [
            'project' => $project,
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project, ProjectService $projects): RedirectResponse
    {
        $projects->updateDetails(
            $project,
            $request->safe()->only(['name', 'description', 'requirements', 'additional_instructions', 'renovation_type', 'property_type']),
        );

        if ($request->exists('address') || $request->exists('city')) {
            $projects->updateLocation($project, $request->safe()->only(['address', 'city', 'province', 'postal_code']));
        }

        if ($request->exists('estimated_budget') || $request->exists('expected_start_date')) {
            $projects->updateBudget($project, $request->safe()->only(['estimated_budget', 'expected_start_date', 'expected_completion_date', 'timeline_notes']));
        }

        return redirect()
            ->route('homeowner.projects.show', $project)
            ->with('status', 'Project updated.');
    }

    public function destroy(Project $project, ProjectService $projects): RedirectResponse
    {
        Gate::authorize('delete', $project);

        $projects->deleteProject($project);

        return redirect()
            ->route('homeowner.projects.index')
            ->with('status', 'Project deleted.');
    }

    public function location(Project $project): View
    {
        Gate::authorize('view', $project);

        return view('homeowner.projects.location', [
            'project' => $project,
        ]);
    }

    public function updateLocation(UpdateProjectLocationRequest $request, Project $project, ProjectService $projects): RedirectResponse
    {
        $projects->updateLocation(
            $project,
            $request->safe()->only(['address', 'city', 'province', 'postal_code', 'latitude', 'longitude']),
        );

        return redirect()
            ->route('homeowner.projects.budget', $project)
            ->with('status', 'Location saved.');
    }

    public function budget(Project $project): View
    {
        Gate::authorize('view', $project);

        return view('homeowner.projects.budget', [
            'project' => $project,
        ]);
    }

    public function updateBudget(UpdateProjectBudgetRequest $request, Project $project, ProjectService $projects): RedirectResponse
    {
        $projects->updateBudget(
            $project,
            $request->safe()->only(['estimated_budget', 'expected_start_date', 'expected_completion_date', 'timeline_notes']),
        );

        return redirect()
            ->route('homeowner.projects.team', $project)
            ->with('status', 'Budget and timeline saved.');
    }

    public function team(Project $project): View
    {
        Gate::authorize('view', $project);

        $designers = User::query()->where('role', 'designer')->whereHas('professionalProfile')->with('professionalProfile')->orderBy('name')->get();
        $contractors = User::query()->where('role', 'contractor')->whereHas('professionalProfile')->with('professionalProfile')->orderBy('name')->get();

        // Keep the already loaded user on each profile so the cards do not query again.
        $designers->concat($contractors)->each(function (User $person) {
            $person->professionalProfile?->setRelation('user', $person);
        });

        return view('homeowner.projects.team', [
            'project' => $project,
            'designers' => $designers,
            'contractors' => $contractors,
        ]);
    }

    public function updateTeam(UpdateProjectTeamRequest $request, Project $project, ProjectService $projects): RedirectResponse
    {
        $designerId = $request->validated('designer_id');
        $contractorId = $request->validated('contractor_id');

        $projects->assignTeam(
            $project,
            $designerId !== null ? (int) $designerId : null,
            $contractorId !== null ? (int) $contractorId : null,
        );

        if ($request->boolean('complete')) {
            $projects->markAwaitingTeam($project->fresh());

            return redirect()
                ->route('homeowner.projects.show', $project)
                ->with('status', 'Project created successfully.');
        }

        return redirect()
            ->route('homeowner.projects.team', $project)
            ->with('status', 'Team selection saved.');
    }
}
