<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Project;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Project::class);

        $projectIds = request()->user()->projects()->select('id');
        $payments = Payment::query()->whereIn('project_id', $projectIds)->with('project')->latest()->paginate(12);

        return view('homeowner.payments.index', [
            'payments' => $payments,
            'project' => null,
            'summary' => $this->summary(request()->user()->projects()->pluck('id')),
        ]);
    }

    public function project(Project $project): View
    {
        Gate::authorize('view', $project);

        return view('homeowner.payments.index', [
            'payments' => $project->payments()->with('project')->latest()->paginate(12),
            'project' => $project,
            'summary' => $this->summary(collect([$project->id])),
        ]);
    }

    public function show(Project $project, Payment $payment): View
    {
        Gate::authorize('view', $payment);
        abort_unless($payment->project_id === $project->id, 404);

        return view('homeowner.payments.show', [
            'project' => $project,
            'payment' => $payment,
        ]);
    }

    /**
     * No provider is called. The screen only marks where that call will live.
     */
    public function pay(Project $project, Payment $payment, PaymentService $payments): View
    {
        Gate::authorize('view', $payment);
        abort_unless($payment->project_id === $project->id, 404);

        return view('homeowner.payments.pay', [
            'project' => $project,
            'payment' => $payment,
            'notice' => $payments->placeholder($payment),
        ]);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, int>|array<int, int>  $projectIds
     * @return array{value: float, paid: float, outstanding: float, pending: int}
     */
    private function summary($projectIds): array
    {
        $paid = (float) Payment::query()->whereIn('project_id', $projectIds)->where('status', Payment::STATUS_PAID)->sum('amount');
        $pendingAmount = (float) Payment::query()->whereIn('project_id', $projectIds)->where('status', Payment::STATUS_PENDING)->sum('amount');

        return [
            'value' => $paid + $pendingAmount,
            'paid' => $paid,
            'outstanding' => $pendingAmount,
            'pending' => Payment::query()->whereIn('project_id', $projectIds)->where('status', Payment::STATUS_PENDING)->count(),
        ];
    }
}
