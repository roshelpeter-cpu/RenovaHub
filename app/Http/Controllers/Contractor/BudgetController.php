<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contractor\UpdateBudgetRequest;
use App\Models\Project;
use App\Services\Contractor\BudgetService;
use App\Services\Contractor\ContractorWorkspaceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function index(Request $request, ContractorWorkspaceService $workspace, \App\Services\Contractor\ProjectBudgetService $budgets): View
    {
        $projects = $workspace->accepted($request->user())
            ->with(['budgetItems', 'budgetSubmissions.payment', 'constructionAssignment.quotation', 'changeRequests', 'homeowner', 'designer'])
            ->orderBy('name')
            ->get();
        $selected = $projects->firstWhere('id', $request->integer('project')) ?? $projects->first();

        return view('contractor.budget.index', [
            'projects' => $projects,
            'project' => $selected,
            'preview' => $selected ? $budgets->preview($selected) : null,
            'submission' => $selected?->budgetSubmissions->sortByDesc('id')->first(),
        ]);
    }

    public function update(UpdateBudgetRequest $request, Project $project, BudgetService $budgets): RedirectResponse
    {
        $budgets->updateSpend($project, $request->validated('spent'));

        return back()->with('status', 'Budget spend updated.');
    }

    public function submit(Request $request, Project $project, ContractorWorkspaceService $workspace, \App\Services\Contractor\ProjectBudgetService $budgets): RedirectResponse
    {
        $project = $workspace->findAccepted($request->user(), $project);
        $budgets->submit($request->user(), $project);

        return redirect()
            ->route('contractor.budget.index', ['project' => $project->id, 'receipt' => 1])
            ->with('status', 'Final budget calculated and sent to the homeowner.');
    }
}
