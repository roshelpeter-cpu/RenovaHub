<?php

namespace App\Http\Controllers\Designer;

use App\Http\Controllers\Controller;
use App\Models\DesignerEarning;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EarningController extends Controller
{
    public function index(Request $request): View
    {
        $earnings = DesignerEarning::query()
            ->where('designer_id', $request->user()->id)
            ->with('project')
            ->latest('recorded_on')
            ->get();

        $recorded = $earnings->where('status', DesignerEarning::STATUS_RECORDED);
        $pending = $earnings->where('status', DesignerEarning::STATUS_PENDING);

        return view('designer.earnings.index', [
            'earnings' => $earnings,
            'totals' => [
                'gross' => (float) $recorded->sum('gross_amount'),
                'month' => (float) $recorded->filter(fn (DesignerEarning $row) => $row->recorded_on?->isSameMonth(now()))->sum('gross_amount'),
                'pending' => (float) $pending->sum('gross_amount'),
                'fees' => (float) $recorded->sum('fee_amount'),
            ],
        ]);
    }

    public function show(Request $request, DesignerEarning $earning): View
    {
        $this->authorize('view', $earning);
        $earning->load('project');

        return view('designer.earnings.show', compact('earning'));
    }
}
