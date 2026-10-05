<?php

namespace App\Http\Controllers\Designer;

use App\Http\Controllers\Controller;
use App\Models\DesignerEarning;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EarningController extends Controller
{
    public function index(Request $request, PaymentService $payments): View
    {
        $earnings = DesignerEarning::query()
            ->where('designer_id', $request->user()->id)
            ->with(['project.homeowner', 'designer'])
            ->latest('recorded_on')
            ->get();

        $recorded = $earnings->where('status', DesignerEarning::STATUS_RECORDED);
        $pending = $earnings->where('status', DesignerEarning::STATUS_PENDING);
        $receipts = [];

        foreach ($recorded as $earning) {
            $receipts['earning-'.$earning->id] = $payments->receiptForDesigner($earning);
        }

        return view('designer.earnings.index', [
            'earnings' => $earnings,
            'receipts' => $receipts,
            'totals' => [
                'gross' => (float) $recorded->sum('gross_amount'),
                'net' => (float) $recorded->sum('net_amount'),
                'month' => (float) $recorded->filter(fn (DesignerEarning $row) => $row->recorded_on?->isSameMonth(now()))->sum('net_amount'),
                'pending' => (float) $pending->sum('net_amount'),
                'fees' => (float) $recorded->sum('fee_amount'),
            ],
        ]);
    }

    public function show(Request $request, DesignerEarning $earning, PaymentService $payments): View
    {
        $this->authorize('view', $earning);
        $earning->load(['project.homeowner', 'designer']);

        $receipts = $earning->status === DesignerEarning::STATUS_RECORDED
            ? ['earning-'.$earning->id => $payments->receiptForDesigner($earning)]
            : [];

        return view('designer.earnings.show', compact('earning', 'receipts'));
    }
}
