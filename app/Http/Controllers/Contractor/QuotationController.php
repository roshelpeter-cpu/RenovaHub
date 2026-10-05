<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contractor\StoreQuotationRequest;
use App\Http\Requests\Contractor\UpdateQuotationRequest;
use App\Models\ConstructionFirmQuotation;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\SupplierPrice;
use App\Services\Contractor\ContractorWorkspaceService;
use App\Services\Contractor\QuotationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuotationController extends Controller
{
    public function index(Request $request, ContractorWorkspaceService $workspace): View
    {
        $projects = $workspace->accepted($request->user())->orderBy('name')->get();
        $projectId = $request->integer('project');

        if ($projectId > 0 && ! $projects->contains('id', $projectId)) {
            abort(404);
        }

        $quotations = Quotation::query()
            ->where('contractor_id', $request->user()->id)
            ->whereIn('project_id', $projects->pluck('id'))
            ->when($projectId > 0, fn ($query) => $query->where('project_id', $projectId))
            ->with(['project', 'items'])
            ->latest()
            ->get();

        $ids = $projects->pluck('id');
        $prices = SupplierPrice::query()
            ->whereHas('request', fn ($query) => $query->whereIn('project_id', $ids)->where('contractor_id', $request->user()->id))
            ->with(['supplier', 'request.project'])
            ->latest()
            ->get();
        $firmQuotes = ConstructionFirmQuotation::query()
            ->whereIn('project_id', $ids)
            ->where('contractor_id', $request->user()->id)
            ->with(['firm', 'project'])
            ->latest()
            ->get();

        return view('contractor.quotations.index', compact('quotations', 'projects', 'projectId', 'prices', 'firmQuotes'));
    }

    public function create(Request $request, ContractorWorkspaceService $workspace): View
    {
        return view('contractor.quotations.form', [
            'projects' => $workspace->cards($request->user()),
            'quotation' => null,
        ]);
    }

    public function store(StoreQuotationRequest $request, QuotationService $quotations): RedirectResponse
    {
        $project = Project::query()->findOrFail($request->integer('project_id'));
        $quotation = $quotations->saveDraft(
            $request->user(),
            $project,
            $request->validated('items'),
            $request->string('description')->toString(),
            $request->input('valid_until'),
        );

        if ($request->boolean('submit')) {
            $quotations->submit($request->user(), $quotation);
        }

        return redirect()
            ->route('contractor.quotations.show', $quotation)
            ->with('status', $request->boolean('submit') ? 'Quotation submitted to the homeowner.' : 'Draft saved.');
    }

    public function show(Request $request, Quotation $quotation): View
    {
        $this->authorize('view', $quotation);
        $quotation->load(['items', 'project.homeowner']);

        return view('contractor.quotations.show', compact('quotation'));
    }

    public function edit(Request $request, Quotation $quotation, ContractorWorkspaceService $workspace): View
    {
        $this->authorize('prepare', $quotation);
        $quotation->load('items');

        return view('contractor.quotations.form', [
            'projects' => $workspace->cards($request->user()),
            'quotation' => $quotation,
        ]);
    }

    public function update(UpdateQuotationRequest $request, Quotation $quotation, QuotationService $quotations): RedirectResponse
    {
        $quotations->saveDraft(
            $request->user(),
            $quotation->project,
            $request->validated('items'),
            $request->string('description')->toString(),
            $request->input('valid_until'),
            $quotation,
        );

        if ($request->boolean('submit')) {
            $quotations->submit($request->user(), $quotation->fresh());
        }

        return redirect()
            ->route('contractor.quotations.show', $quotation)
            ->with('status', $request->boolean('submit') ? 'Quotation submitted to the homeowner.' : 'Draft updated.');
    }

    public function submit(Request $request, Quotation $quotation, QuotationService $quotations): RedirectResponse
    {
        $this->authorize('submit', $quotation);
        $quotations->submit($request->user(), $quotation);

        return redirect()
            ->route('contractor.quotations.show', $quotation)
            ->with('status', 'Quotation submitted. Waiting for the homeowner.');
    }
}
