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
    public function index(Request $request, ContractorWorkspaceService $workspace): View
    {
        $projects = $workspace->accepted($request->user())->with('budgetItems')->orderBy('name')->get();
        $selected = $projects->firstWhere('id', $request->integer('project')) ?? $projects->first();

        return view('contractor.budget.index', [
            'projects' => $projects,
            'project' => $selected,
        ]);
    }

    public function update(UpdateBudgetRequest $request, Project $project, BudgetService $budgets): RedirectResponse
    {
        $budgets->updateSpend($project, $request->validated('spent'));

        return back()->with('status', 'Budget spend updated.');
    }
}
