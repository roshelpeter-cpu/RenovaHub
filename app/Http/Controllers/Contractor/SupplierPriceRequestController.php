<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contractor\StoreSupplierPriceRequest;
use App\Models\Supplier;
use App\Models\SupplierPrice;
use App\Models\SupplierPriceRequest as PriceRequestModel;
use App\Services\Contractor\ContractorWorkspaceService;
use App\Services\Contractor\SupplierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierPriceRequestController extends Controller
{
    public function create(Request $request, Supplier $supplier, ContractorWorkspaceService $workspace): View
    {
        return view('contractor.suppliers.request-price', [
            'supplier' => $supplier->load('startingPrices'),
            'projects' => $workspace->cards($request->user()),
        ]);
    }

    public function store(StoreSupplierPriceRequest $request, Supplier $supplier): RedirectResponse
    {
        $priceRequest = PriceRequestModel::query()->create([
            'project_id' => $request->integer('project_id'),
            'contractor_id' => $request->user()->id,
            'supplier_id' => $supplier->id,
            'material' => $request->string('material')->toString(),
            'product' => $request->string('product')->toString(),
            'quantity' => $request->input('quantity'),
            'unit' => $request->string('unit')->toString(),
            'required_by' => $request->date('required_by'),
            'notes' => $request->input('notes'),
            'status' => PriceRequestModel::STATUS_SENT,
        ]);

        return redirect()
            ->route('contractor.suppliers.show', $supplier)
            ->with('status', 'Price request sent for '.$priceRequest->product.'.');
    }

    public function show(Request $request, SupplierPrice $price): View
    {
        $this->authorize('view', $price);
        $price->load(['supplier', 'request.project']);

        return view('contractor.suppliers.price', compact('price'));
    }

    public function revise(Request $request, SupplierPrice $price, SupplierService $suppliers): RedirectResponse
    {
        $this->authorize('view', $price);
        $request->validate(['note' => ['nullable', 'string', 'max:2000']]);
        $suppliers->requestRevision($request->user(), $price, $request->string('note')->toString() ?: null);

        return back()->with('status', 'Revision requested from the supplier.');
    }
}
