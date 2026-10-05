<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Models\BudgetSubmission;
use App\Models\ProcurementProposal;
use App\Services\Contractor\ProcurementService;
use App\Services\Contractor\ProjectBudgetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Homeowner decisions for contractor procurement.
 * A contractor posting to these routes is rejected inside the services.
 */
class ProcurementDecisionController extends Controller
{
    public function proposal(Request $request, ProcurementProposal $proposal, ProcurementService $procurement): RedirectResponse
    {
        $data = $request->validate([
            'decision' => ['required', 'in:approve,reject,clarification'],
            'option_id' => ['nullable', 'integer'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $procurement->decide($request->user(), $proposal, $data['decision'], $data['option_id'] ?? null, $data['note'] ?? null);

        return back()->with('status', 'Your decision was saved.');
    }

    public function budget(Request $request, BudgetSubmission $submission, ProjectBudgetService $budgets): RedirectResponse
    {
        $data = $request->validate(['decision' => ['required', 'in:approve,reject']]);
        $budgets->decide($request->user(), $submission, $data['decision']);

        return back()->with('status', $data['decision'] === 'approve'
            ? 'Budget approved. One payment request is now pending.'
            : 'Budget rejected.');
    }
}
