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

        $projects = request()->user()->projects()->orderBy('name')->get();
        $selected = request()->integer('project');

        if ($selected > 0 && ! $projects->contains('id', $selected)) {
            abort(404);
        }

        $payments = Payment::query()
            ->whereIn('project_id', $projects->pluck('id'))
            ->when($selected > 0, fn ($query) => $query->where('project_id', $selected))
            ->with(['project', 'quotation', 'supplierOrder.supplier'])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('homeowner.payments.index', [
            'payments' => $payments,
            'projects' => $projects,
            'selected' => $selected,
            'summary' => $this->summary($projects->pluck('id')),
        ]);
    }

    /**
     * Payments stay on the global page. A project URL only selects that project.
     */
    public function project(Project $project): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('view', $project);

        return redirect()->route('homeowner.payments.index', ['project' => $project->id]);
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
        Gate::authorize('pay', $payment);
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
        $fees = (float) Payment::query()->whereIn('project_id', $projectIds)->sum('platform_fee');

        return [
            'value' => $paid + $pendingAmount,
            'paid' => $paid,
            'outstanding' => $pendingAmount,
            'fees' => $fees,
            'pending' => Payment::query()->whereIn('project_id', $projectIds)->where('status', Payment::STATUS_PENDING)->count(),
        ];
    }
}
