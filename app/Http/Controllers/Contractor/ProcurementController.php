<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Models\ConstructionFirmQuotation;
use App\Models\ProcurementProposal;
use App\Models\Project;
use App\Models\SupplierPrice;
use App\Services\Contractor\ContractorWorkspaceService;
use App\Services\Contractor\ProcurementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Comparison pages only send options to the homeowner.
 * Approval routes live on the homeowner side.
 */
class ProcurementController extends Controller
{
    public function suppliers(Request $request, Project $project, ContractorWorkspaceService $workspace): View
    {
        $project = $workspace->findAccepted($request->user(), $project);
        $prices = SupplierPrice::query()
            ->whereHas('request', fn ($query) => $query->where('project_id', $project->id)->where('contractor_id', $request->user()->id))
            ->with(['supplier', 'request'])
            ->get();

        return view('contractor.procurement.suppliers', compact('project', 'prices'));
    }

    public function sendSuppliers(Request $request, Project $project, ContractorWorkspaceService $workspace, ProcurementService $procurement): RedirectResponse
    {
        $project = $workspace->findAccepted($request->user(), $project);
        $ids = $request->validate(['prices' => ['required', 'array', 'min:2'], 'prices.*' => ['integer']])['prices'];
        $procurement->sendSupplierOptions($request->user(), $project, $ids);

        return redirect()->route('contractor.quotations.index')->with('status', 'Supplier options were sent to the homeowner.');
    }

    public function firms(Request $request, Project $project, ContractorWorkspaceService $workspace): View
    {
        $project = $workspace->findAccepted($request->user(), $project);
        $quotes = ConstructionFirmQuotation::query()
            ->where('project_id', $project->id)
            ->where('contractor_id', $request->user()->id)
            ->with('firm')
            ->get();

        return view('contractor.procurement.firms', compact('project', 'quotes'));
    }

    public function sendFirms(Request $request, Project $project, ContractorWorkspaceService $workspace, ProcurementService $procurement): RedirectResponse
    {
        $project = $workspace->findAccepted($request->user(), $project);
        $ids = $request->validate(['quotes' => ['required', 'array', 'min:2'], 'quotes.*' => ['integer']])['quotes'];
        $procurement->sendFirmOptions($request->user(), $project, $ids);

        return redirect()->route('contractor.quotations.index', ['type' => 'firms'])->with('status', 'Construction firm options were sent to the homeowner.');
    }

    public function assign(Request $request, Project $project, ConstructionFirmQuotation $quotation, ContractorWorkspaceService $workspace, ProcurementService $procurement): RedirectResponse
    {
        $project = $workspace->findAccepted($request->user(), $project);
        $procurement->assign($request->user(), $project, $quotation);

        return back()->with('status', 'Approved construction firm assigned.');
    }
}
