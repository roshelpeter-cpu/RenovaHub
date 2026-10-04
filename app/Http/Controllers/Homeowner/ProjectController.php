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

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Project::class);

        $status = $request->string('status')->toString();
        $filtering = array_key_exists($status, Project::statuses());
        $search = trim($request->string('search')->toString());

        $type = $request->string('type')->toString();
        $sort = $request->string('sort')->toString();

        $projects = $request->user()
            ->projects()
            ->with(['designer.professionalProfile', 'contractor.professionalProfile'])
            ->when($filtering, fn ($query) => $query->where('status', $status))
            ->when(array_key_exists($type, Project::renovationTypes()), fn ($query) => $query->where('renovation_type', $type))
            ->when($search !== '', function ($query) use ($search) {
                $term = '%'.addcslashes($search, '%_\\').'%';
                $query->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', $term)->orWhere('city', 'like', $term);
                });
            })
            ->when($sort === 'oldest', fn ($query) => $query->oldest())
            ->when($sort === 'progress', fn ($query) => $query->orderByDesc('progress'))
            ->when($sort === 'budget', fn ($query) => $query->orderByDesc('estimated_budget'))
            ->when(! in_array($sort, ['oldest', 'progress', 'budget'], true), fn ($query) => $query->latest())
            ->paginate(9)
            ->withQueryString();

        return view('homeowner.projects.index', [
            'projects' => $projects,
            'status' => $filtering ? $status : null,
            'search' => $search,
            'type' => array_key_exists($type, Project::renovationTypes()) ? $type : null,
            'sort' => in_array($sort, ['oldest', 'progress', 'budget'], true) ? $sort : 'newest',
        ]);
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
            'tasks' => 'homeowner.projects.tasks',
            'documents' => 'homeowner.projects.documents',
            'mood-board' => 'homeowner.projects.mood-board',
            'quotations' => 'homeowner.projects.quotations',
            'change-requests' => 'homeowner.projects.change-requests',
            'payments' => 'homeowner.projects.payments',
            'messages' => 'homeowner.projects.messages',
        ];

        if (isset($tabRoutes[$tab])) {
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
        ]);

        foreach (['designer', 'contractor'] as $role) {
            $member = $project->{$role};
            $member?->professionalProfile?->setRelation('user', $member);
        }

        return view('homeowner.projects.show', [
            'project' => $project,
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
