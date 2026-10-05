<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contractor\StoreConstructionQuoteRequest;
use App\Models\ConstructionFirm;
use App\Models\Project;
use App\Services\Contractor\ContractorWorkspaceService;
use App\Services\Contractor\ProcurementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConstructionFirmController extends Controller
{
    public function index(Request $request): View
    {
        $firms = ConstructionFirm::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where(fn ($inner) => $inner->where('name', 'like', $term)->orWhere('specialisation', 'like', $term)->orWhere('city', 'like', $term));
            })
            ->when($request->filled('city'), fn ($query) => $query->where('city', $request->string('city')))
            ->when($request->string('sort') === 'rating', fn ($query) => $query->orderByDesc('rating'))
            ->when($request->string('sort') !== 'rating', fn ($query) => $query->orderByDesc('completed_projects'))
            ->get();

        return view('contractor.firms.index', [
            'firms' => $firms,
            'cities' => ConstructionFirm::query()->distinct()->orderBy('city')->pluck('city'),
        ]);
    }

    public function show(ConstructionFirm $firm, Request $request, ContractorWorkspaceService $workspace): View
    {
        return view('contractor.firms.show', [
            'firm' => $firm,
            'projects' => $workspace->cards($request->user()),
        ]);
    }

    public function quote(Request $request, ConstructionFirm $firm, ContractorWorkspaceService $workspace): View
    {
        return view('contractor.firms.quote', [
            'firm' => $firm,
            'projects' => $workspace->cards($request->user()),
        ]);
    }

    public function storeQuote(StoreConstructionQuoteRequest $request, ConstructionFirm $firm, ProcurementService $procurement, ContractorWorkspaceService $workspace): RedirectResponse
    {
        $project = $workspace->findAccepted($request->user(), Project::query()->findOrFail($request->integer('project_id')));

        $procurement->recordFirmQuote($request->user(), $project, [
            'construction_firm_id' => $firm->id,
            'price' => $request->input('price'),
            'duration_days' => $request->integer('duration_days'),
            'start_date' => $request->date('start_date'),
            'completion_date' => $request->date('completion_date'),
            'scope' => $request->input('scope'),
            'terms' => $request->input('terms'),
            'notes' => $request->input('notes'),
            'warranty' => $request->input('warranty'),
        ]);

        return redirect()->route('contractor.quotations.index', ['type' => 'firms'])->with('status', 'Construction firm quotation recorded.');
    }
}
