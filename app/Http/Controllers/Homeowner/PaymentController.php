<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Homeowner\ConfirmProjectPaymentRequest;
use App\Models\Payment;
use App\Models\Project;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(PaymentService $payments): View
    {
        Gate::authorize('viewAny', Project::class);

        $projectIds = request()->user()->projects()->pluck('id');

        $featured = Payment::query()
            ->whereIn('project_id', $projectIds)
            ->whereNotNull('budget_submission_id')
            ->with('project')
            ->latest()
            ->get()
            ->unique('project_id')
            ->values();

        $history = Payment::query()
            ->whereIn('project_id', $projectIds)
            ->with('project')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $all = Payment::query()->whereIn('project_id', $projectIds)->get();
        $paid = $all->where('status', Payment::STATUS_PAID);
        $pending = $all->where('status', Payment::STATUS_PENDING);

        return view('homeowner.payments.index', [
            'featured' => $featured,
            'history' => $history,
            'receipts' => $this->receipts($payments, $featured->concat($history->getCollection())),
            'summary' => [
                'paid' => (float) $paid->sum(fn (Payment $payment) => $payment->homeownerTotal()),
                'pending' => (float) $pending->sum(fn (Payment $payment) => $payment->homeownerTotal()),
                'successful' => $paid->count(),
            ],
        ]);
    }

    /**
     * Payments stay on the global page. A project URL only selects that project.
     */
    public function project(Project $project): RedirectResponse
    {
        Gate::authorize('view', $project);

        return redirect()->route('homeowner.payments.index', ['project' => $project->id]);
    }

    public function show(Project $project, Payment $payment, PaymentService $payments): View
    {
        Gate::authorize('view', $payment);
        abort_unless($payment->project_id === $project->id, 404);

        $receipts = $payment->status === Payment::STATUS_PAID
            ? ['payment-'.$payment->id => $payments->receiptForPayment($payment)]
            : [];

        return view('homeowner.payments.show', [
            'project' => $project,
            'payment' => $payment,
            'receipts' => $receipts,
        ]);
    }

    public function pay(Project $project, Payment $payment, PaymentService $payments): View
    {
        Gate::authorize('pay', $payment);
        abort_unless($payment->project_id === $project->id, 404);

        return view('homeowner.payments.pay', [
            'project' => $project,
            'payment' => $payment,
            'checkout' => $payments->checkout($payment),
        ]);
    }

    public function confirm(ConfirmProjectPaymentRequest $request, Project $project, Payment $payment, PaymentService $payments): RedirectResponse
    {
        if ($payment->status !== Payment::STATUS_PAID) {
            $payments->confirmSandbox($payment, $request->string('token')->toString());
        }

        return redirect()->route('homeowner.payments.result', [$project, $payment]);
    }

    public function result(Project $project, Payment $payment, PaymentService $payments): View
    {
        Gate::authorize('view', $payment);
        abort_unless($payment->project_id === $project->id, 404);

        $payment = $payment->fresh();

        return view('homeowner.payments.result', [
            'project' => $project,
            'payment' => $payment,
            'receipts' => $payment->status === Payment::STATUS_PAID
                ? ['payment-'.$payment->id => $payments->receiptForPayment($payment)]
                : [],
        ]);
    }

    public function returned(Payment $payment, PaymentService $payments): View
    {
        Gate::authorize('view', $payment);

        $payment = $payment->fresh();

        return view('homeowner.payments.result', [
            'project' => $payment->project,
            'payment' => $payment,
            'receipts' => $payment->status === Payment::STATUS_PAID
                ? ['payment-'.$payment->id => $payments->receiptForPayment($payment)]
                : [],
        ]);
    }

    public function cancelled(Payment $payment): RedirectResponse
    {
        Gate::authorize('view', $payment);

        return redirect()
            ->route('homeowner.payments.pay', [$payment->project, $payment])
            ->with('cancelled', true);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Payment>  $payments
     * @return array<string, array<string, mixed>>
     */
    private function receipts(PaymentService $service, $payments): array
    {
        $receipts = [];

        foreach ($payments as $payment) {
            if ($payment->status === Payment::STATUS_PAID) {
                $receipts['payment-'.$payment->id] = $service->receiptForPayment($payment);
            }
        }

        return $receipts;
    }
}
