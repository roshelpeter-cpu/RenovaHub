<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierCategory;
use App\Models\SupplierOrder;
use App\Services\Contractor\ContractorWorkspaceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request, ContractorWorkspaceService $workspace): View
    {
        return view('contractor.suppliers.index', [
            'categories' => SupplierCategory::query()->orderBy('name')->get(),
            'cities' => Supplier::query()->whereNotNull('city')->distinct()->orderBy('city')->pluck('city'),
            'orders' => SupplierOrder::query()
                ->where('contractor_id', $request->user()->id)
                ->with(['supplier', 'project', 'payment'])
                ->latest()
                ->limit(6)
                ->get(),
            'projects' => $workspace->accepted($request->user())->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function show(Supplier $supplier): View
    {
        $supplier->load(['categories', 'startingPrices', 'reviews']);

        return view('contractor.suppliers.show', compact('supplier'));
    }
}
