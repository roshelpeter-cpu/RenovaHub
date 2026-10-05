<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contractor\ConfirmDisbursementRequest;
use App\Models\ContractorEarning;
use App\Models\PaymentAllocation;
use App\Models\Project;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EarningsController extends Controller
{
    public function index(Request $request, PaymentService $payments): View
    {
        abort_unless($request->user()->isContractor(), 403);

        return view('contractor.earnings.index', [
            'projects' => $payments->contractorProjects($request->user()),
        ]);
    }

    public function project(Request $request, Project $project, PaymentService $payments): View
    {
        abort_unless($request->user()->can('construct', $project), 403);

        $breakdown = $payments->breakdown($project);

        return view('contractor.earnings.project', [
            'project' => $project->loadMissing('homeowner'),
            'payment' => $breakdown['payment'],
            'sections' => $breakdown['sections'],
            'receipts' => $payments->receiptsForBreakdown($breakdown),
        ]);
    }

    public function pay(ConfirmDisbursementRequest $request, Project $project, PaymentAllocation $allocation, PaymentService $payments): RedirectResponse
    {
        $payments->confirmDisbursement($request->user(), $allocation);

        return redirect()
            ->route('contractor.earnings.project', $project)
            ->with('paid_allocation', $allocation->id);
    }

    public function show(ContractorEarning $earning): RedirectResponse
    {
        $this->authorize('view', $earning);

        return redirect()->route('contractor.earnings.project', $earning->project_id);
    }
}
