<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Models\ContractorEarning;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EarningsController extends Controller
{
    public function index(Request $request): View
    {
        $earnings = ContractorEarning::query()
            ->where('contractor_id', $request->user()->id)
            ->with(['project', 'supplierOrder'])
            ->latest()
            ->get();

        $recorded = $earnings->where('status', ContractorEarning::STATUS_RECORDED);
        $pending = $earnings->where('status', ContractorEarning::STATUS_PENDING);
        $month = $recorded->filter(fn (ContractorEarning $earning) => $earning->recorded_on?->isSameMonth(now()));

        return view('contractor.earnings.index', [
            'earnings' => $earnings,
            'summary' => [
                'gross' => (float) $recorded->sum('gross_amount'),
                'month' => (float) $month->sum('net_amount'),
                'pending' => (float) $pending->sum('net_amount'),
                'paid' => (float) $recorded->sum('net_amount'),
                'fees' => (float) $recorded->sum('fee_amount'),
                'net' => (float) $recorded->sum('net_amount'),
            ],
        ]);
    }

    public function show(ContractorEarning $earning): View
    {
        $this->authorize('view', $earning);
        $earning->load(['project', 'supplierOrder.supplier', 'payment']);

        return view('contractor.earnings.show', compact('earning'));
    }
}
