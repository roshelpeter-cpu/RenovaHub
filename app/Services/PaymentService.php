<?php

namespace App\Services;

use App\Models\BudgetSubmission;
use App\Models\ContractorEarning;
use App\Models\DesignerEarning;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\ProcurementProposal;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\SupplierOrder;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * The payment provider is intentionally not called.
 * This service is the only place a gateway should be attached later.
 */
class PaymentService
{
    /**
     * The merchant secret stays in configuration and is never returned here.
     * This text is the only checkout notice until PayHere is connected.
     */
    public function placeholder(Payment $payment): string
    {
        $mode = config('services.payhere.sandbox') ? 'sandbox' : 'live';

        return 'PayHere '.$mode.' checkout will be connected after hosting. No payment has been sent for '.$payment->reference.'.';
    }

    /**
     * An approved quotation opens a pending payment. The row records the
     * renovation amount and the platform fee. It is not a PayHere transaction.
     */
    public function openForQuotation(Quotation $quotation): Payment
    {
        $existing = $quotation->payments()->first();

        if ($existing) {
            return $existing;
        }

        $renovation = round((float) $quotation->total, 2);
        $percent = (float) Payment::FEE_PERCENT;
        $fee = round($renovation * ($percent / 100), 2);

        return $quotation->project->payments()->create([
            'quotation_id' => $quotation->id,
            'reference' => $this->nextReference($quotation->project),
            'amount' => $renovation,
            'renovation_amount' => $renovation,
            'platform_fee' => $fee,
            'net_amount' => round($renovation + $fee, 2),
            'fee_percent' => $percent,
            'currency' => 'LKR',
            'status' => Payment::STATUS_PENDING,
            'notes' => 'Opened from '.$quotation->number.'. PayHere has not been called.',
        ]);
    }

    public function nextReference(Project $project): string
    {
        $prefix = 'RH-'.now()->format('Ymd').'-';

        $taken = Payment::query()->where('reference', 'like', $prefix.'%')->pluck('reference')
            ->merge(PaymentAllocation::query()->where('reference', 'like', $prefix.'%')->pluck('reference'))
            ->merge(DesignerEarning::query()->where('reference', 'like', $prefix.'%')->pluck('reference'))
            ->merge(ContractorEarning::query()->where('reference', 'like', $prefix.'%')->pluck('reference'));

        $sequence = $taken
            ->map(fn ($reference) => (int) substr((string) $reference, -4))
            ->max() ?? 0;

        do {
            $sequence++;
            $reference = $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
        } while (
            Payment::query()->where('reference', $reference)->exists()
            || PaymentAllocation::query()->where('reference', $reference)->exists()
            || DesignerEarning::query()->where('reference', $reference)->exists()
        );

        return $reference;
    }

    /**
     * A supplier order creates a pending homeowner payment request.
     * The order must remain unpaid until the payment provider confirms
     * the transaction, preventing the contractor from manually marking
     * an order as paid.
     */
    public function openForSupplierOrder(SupplierOrder $order): Payment
    {
        $existing = $order->payment;

        if ($existing) {
            return $existing;
        }

        $amount = round((float) $order->amount, 2);
        $percent = ContractorEarning::percent();
        $fee = ContractorEarning::feeFor($amount, $percent);

        return $order->project->payments()->create([
            'supplier_order_id' => $order->id,
            'payer_id' => $order->project->user_id,
            'payee_id' => $order->contractor_id,
            'reference' => $this->nextReference($order->project),
            'amount' => $amount,
            'renovation_amount' => $amount,
            'platform_fee' => $fee,
            'net_amount' => $amount,
            'fee_percent' => $percent,
            'currency' => $order->project->currency ?: 'LKR',
            'provider' => 'payhere',
            'status' => Payment::STATUS_PENDING,
            'notes' => 'Opened from supplier order '.$order->number.'. PayHere has not been called.',
        ]);
    }

    /**
     * An approved final budget opens the one homeowner payment for the project.
     * The charge stays pending until PayHere confirms it.
     */
    public function openForBudget(BudgetSubmission $submission): Payment
    {
        if ($submission->payment_id) {
            return $submission->payment;
        }

        $existing = Payment::query()->where('budget_submission_id', $submission->id)->first();

        if ($existing) {
            return $existing;
        }

        $payment = $submission->project->payments()->create([
            'budget_submission_id' => $submission->id,
            'payer_id' => $submission->project->user_id,
            'payee_id' => $submission->contractor_id,
            'reference' => $this->nextReference($submission->project),
            'amount' => $submission->total,
            'renovation_amount' => round((float) $submission->total - (float) $submission->platform_fee, 2),
            'platform_fee' => $submission->platform_fee,
            'net_amount' => $submission->total,
            'fee_percent' => $submission->fee_percent,
            'currency' => $submission->project->currency ?: 'LKR',
            'provider' => 'payhere',
            'status' => Payment::STATUS_PENDING,
            'notes' => 'Single project payment for the approved final budget.',
        ]);

        $this->ensureDisbursements($payment);

        return $payment;
    }

    public function payHereReady(): bool
    {
        return filled(config('services.payhere.merchant_id'))
            && filled(config('services.payhere.merchant_secret'));
    }

    /**
     * @return array{mode: string, action?: string, fields?: array<string, string>, token?: string}
     */
    public function checkout(Payment $payment): array
    {
        if ($this->payHereReady()) {
            return ['mode' => 'payhere', ...$this->payHereCheckout($payment)];
        }

        return [
            'mode' => 'sandbox',
            'token' => $this->sandboxToken($payment),
        ];
    }

    public function sandboxToken(Payment $payment): string
    {
        return hash_hmac('sha256', $payment->id.'|'.$payment->reference, (string) config('app.key'));
    }

    /**
     * Local sandbox confirmation. Opening the checkout page does not call this.
     */
    public function confirmSandbox(Payment $payment, string $token): Payment
    {
        abort_unless(hash_equals($this->sandboxToken($payment), $token), 422);

        if ($payment->status === Payment::STATUS_PAID) {
            return $payment;
        }

        return $this->confirmVerified($payment, 'sandbox-'.now()->format('YmdHis').'-'.$payment->id);
    }

    /**
     * PayHere server notification. Status is updated only after the signature matches.
     */
    public function verifyPayHereNotify(array $payload): Payment
    {
        abort_unless($this->payHereReady(), 404);

        $payment = Payment::query()->where('reference', (string) ($payload['order_id'] ?? ''))->firstOrFail();
        abort_unless($this->payHereSignatureValid($payload), 422);

        $statusCode = (int) ($payload['status_code'] ?? 0);

        if ($statusCode !== 2) {
            if ($payment->status === Payment::STATUS_PENDING && in_array($statusCode, [-1, -2, -3], true)) {
                $payment->update([
                    'status' => Payment::STATUS_FAILED,
                    'provider' => 'payhere',
                ]);
            }

            return $payment->fresh();
        }

        $expected = number_format($payment->homeownerTotal(), 2, '.', '');
        $received = number_format((float) ($payload['payhere_amount'] ?? 0), 2, '.', '');
        abort_unless(hash_equals($expected, $received), 422);

        if ($payment->status === Payment::STATUS_PAID) {
            return $payment;
        }

        return $this->confirmVerified($payment, (string) ($payload['payment_id'] ?? ''));
    }

    /**
     * Only a verified provider callback or a signed sandbox confirmation should call this.
     * There is no route that accepts a status field from the browser.
     */
    public function confirmVerified(Payment $payment, string $providerReference): Payment
    {
        return DB::transaction(function () use ($payment, $providerReference) {
            abort_if($payment->status === Payment::STATUS_PAID, 422);
            abort_if($providerReference === '', 422);

            $payment->update([
                'status' => Payment::STATUS_PAID,
                'provider' => 'payhere',
                'provider_reference' => $providerReference,
                'paid_at' => now(),
                'method' => $payment->method ?: 'payhere',
                'net_amount' => $payment->net_amount ?? $payment->homeownerTotal(),
            ]);

            $order = $payment->supplierOrder;

            if ($order && $order->status === SupplierOrder::STATUS_PAYMENT_PENDING) {
                $order->update(['status' => SupplierOrder::STATUS_PAID]);
            }

            $earning = $order?->earning;

            if ($earning && $earning->status === ContractorEarning::STATUS_PENDING) {
                $earning->update([
                    'status' => ContractorEarning::STATUS_RECORDED,
                    'recorded_on' => now()->toDateString(),
                ]);
            }

            if ($payment->budgetSubmission) {
                $fresh = $payment->fresh();
                $this->ensureDisbursements($fresh);
                $this->settleProfessionals($fresh);
                app(\App\Services\Contractor\ProjectBudgetService::class)->recordEarning($fresh);
                $this->recordDesignerEarning($fresh);
            }

            return $payment->fresh();
        });
    }

    /**
     * Contractor confirmation for a supplier or construction-firm share.
     * The contractor share has no pay action.
     */
    public function confirmDisbursement(User $contractor, PaymentAllocation $allocation): PaymentAllocation
    {
        return DB::transaction(function () use ($contractor, $allocation) {
            $allocation = PaymentAllocation::query()->whereKey($allocation->id)->lockForUpdate()->firstOrFail();

            abort_unless((int) $allocation->project->contractor_id === (int) $contractor->id, 403);
            abort_unless(in_array($allocation->bucket, [PaymentAllocation::MATERIALS, PaymentAllocation::CONSTRUCTION], true), 403);
            abort_if($allocation->status !== PaymentAllocation::STATUS_PENDING, 422);

            $allocation->update([
                'status' => PaymentAllocation::STATUS_PAID,
                'payer_id' => $contractor->id,
                'paid_at' => now(),
                'provider' => 'renovahub',
                'provider_reference' => 'RH-DISB-'.$allocation->id.'-'.now()->format('YmdHis'),
                'reference' => $allocation->reference ?: $this->nextReference($allocation->project),
                'net_amount' => $allocation->net_amount ?? $allocation->amount,
                'platform_fee' => $allocation->platform_fee ?? 0,
            ]);

            return $allocation->fresh(['supplier', 'firm', 'payee', 'project.homeowner', 'project.contractor']);
        });
    }

    public function ensureDisbursements(Payment $payment): void
    {
        $submission = $payment->budgetSubmission;

        if ($submission === null) {
            return;
        }

        $project = $submission->project()->with([
            'contractor',
            'designer',
            'procurementProposals.selectedPrice.supplier',
            'procurementProposals.selectedPrice.request',
            'constructionAssignment.firm',
            'constructionAssignment.quotation',
        ])->first();

        if ($project === null) {
            return;
        }

        $percent = (float) ($submission->fee_percent ?: config('renovahub.platform_fee_percent', 2));
        $proposal = $project->procurementProposals->first(
            fn ($row) => $row->type === ProcurementProposal::TYPE_SUPPLIER && $row->status === ProcurementProposal::STATUS_APPROVED
        );
        $price = $proposal?->selectedPrice;
        $assignment = $project->constructionAssignment;

        $rows = [
            $this->share(PaymentAllocation::MATERIALS, (float) $submission->materials, [
                'supplier_id' => $price?->supplier_id,
                'label' => $price?->supplier?->name ?: 'Supplier',
                'detail' => $price?->request?->product ?: 'Project materials',
                'platform_fee' => 0,
                'net_amount' => round((float) $submission->materials, 2),
            ]),
            $this->share(PaymentAllocation::CONSTRUCTION, (float) $submission->construction, [
                'construction_firm_id' => $assignment?->construction_firm_id,
                'label' => $assignment?->firm?->name ?: 'Construction firm',
                'detail' => $assignment?->quotation?->scope ?: 'Construction and Labour',
                'platform_fee' => 0,
                'net_amount' => round((float) $submission->construction, 2),
            ]),
            $this->share(PaymentAllocation::CONTRACTOR, (float) $submission->contractor_fee, [
                'payee_user_id' => $submission->contractor_id,
                'label' => $project->contractor?->name ?: 'Contractor',
                'detail' => 'Contractor',
                'platform_fee' => ContractorEarning::feeFor((float) $submission->contractor_fee, $percent),
                'net_amount' => ContractorEarning::netFor((float) $submission->contractor_fee, $percent),
            ]),
            $this->share(PaymentAllocation::DESIGNER, (float) $submission->designer_fee, [
                'payee_user_id' => $project->designer_id,
                'label' => $project->designer?->name ?: 'Designer',
                'detail' => 'Designer',
                'platform_fee' => DesignerEarning::feeFor((float) $submission->designer_fee),
                'net_amount' => DesignerEarning::netFor((float) $submission->designer_fee),
            ]),
            $this->share(PaymentAllocation::PLATFORM, (float) $submission->platform_fee, [
                'label' => 'RenovaHub',
                'detail' => 'RenovaHub Service Charge',
                'platform_fee' => 0,
                'net_amount' => round((float) $submission->platform_fee, 2),
            ]),
        ];

        foreach ($rows as $row) {
            if ($row === null) {
                continue;
            }

            $this->upsertAllocation($payment, $project, $row);
        }
    }

    /**
     * @return Collection<int, Project>
     */
    public function contractorProjects(User $contractor): Collection
    {
        return Project::query()
            ->where('contractor_id', $contractor->id)
            ->whereHas('payments', fn ($query) => $query->whereNotNull('budget_submission_id'))
            ->with([
                'homeowner',
                'payments' => fn ($query) => $query->whereNotNull('budget_submission_id')->latest(),
            ])
            ->orderBy('name')
            ->get();
    }

    public function projectPayment(Project $project): ?Payment
    {
        return $project->payments()
            ->whereNotNull('budget_submission_id')
            ->latest()
            ->first();
    }

    /**
     * @return array{payment: ?Payment, sections: list<array<string, mixed>>}
     */
    public function breakdown(Project $project): array
    {
        $payment = $this->projectPayment($project);

        if ($payment) {
            $this->ensureDisbursements($payment);
            $payment->load([
                'allocations.supplier',
                'allocations.firm',
                'allocations.payee',
                'project.homeowner',
                'project.contractor',
            ]);
        }

        return [
            'payment' => $payment,
            'sections' => $this->sections($project, $payment),
        ];
    }

    /**
     * @param  array{payment: ?Payment, sections: list<array<string, mixed>>}  $breakdown
     * @return array<string, array<string, mixed>>
     */
    public function receiptsForBreakdown(array $breakdown): array
    {
        $receipts = [];

        foreach ($breakdown['sections'] as $section) {
            $allocation = $section['allocation'] ?? null;

            if ($allocation instanceof PaymentAllocation && $allocation->isPaid()) {
                $receipts['allocation-'.$allocation->id] = $this->receiptForAllocation($allocation);
            }
        }

        return $receipts;
    }

    /**
     * @return array<string, mixed>
     */
    public function receiptForPayment(Payment $payment): array
    {
        $payment->loadMissing(['project.homeowner', 'payer']);
        $homeowner = $payment->payer?->name ?: $payment->project?->homeowner?->name ?: 'Homeowner';
        $currency = $payment->currency ?: 'LKR';

        return $this->receipt(
            $this->money($payment->homeownerTotal(), $currency),
            $payment->paid_at ?? $payment->updated_at,
            [
                ['label' => 'Sender Details', 'lines' => [$homeowner, 'Homeowner']],
                ['label' => 'Recipient Details', 'lines' => ['RenovaHub', $payment->project?->name ?: 'Project']],
                ['label' => 'Transaction Type', 'lines' => ['Project Payment']],
                ['label' => 'Transaction No.', 'lines' => [$payment->reference]],
                ['label' => 'Project', 'lines' => [$payment->project?->name ?: 'Project']],
                ['label' => 'Amount', 'lines' => [$this->money($payment->renovationAmount(), $currency)]],
                ['label' => 'RenovaHub Service Charge', 'lines' => [$this->money($payment->platformFee(), $currency)]],
                ['label' => 'Total Paid', 'lines' => [$this->money($payment->homeownerTotal(), $currency)]],
                ['label' => 'Payment Status', 'lines' => ['Successful']],
            ],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function receiptForAllocation(PaymentAllocation $allocation): array
    {
        $allocation->loadMissing(['project.homeowner', 'project.contractor', 'supplier', 'firm', 'payee', 'payer']);
        $project = $allocation->project;
        $currency = 'LKR';
        $homeowner = $project?->homeowner?->name ?: 'Homeowner';
        $contractor = $project?->contractor?->name ?: 'Contractor';

        if ($allocation->bucket === PaymentAllocation::CONTRACTOR) {
            return $this->receipt(
                $this->money($allocation->net(), $currency),
                $allocation->paid_at,
                [
                    ['label' => 'Recipient Details', 'lines' => [$allocation->payee?->name ?: $allocation->label ?: $contractor, 'Contractor']],
                    ['label' => 'Sender Details', 'lines' => [$homeowner, 'RenovaHub']],
                    ['label' => 'Transaction Type', 'lines' => ['Contractor Project Payment']],
                    ['label' => 'Transaction No.', 'lines' => [$allocation->reference ?: '—']],
                    ['label' => 'Project', 'lines' => [$project?->name ?: 'Project']],
                    ['label' => 'Gross Amount', 'lines' => [$this->money($allocation->gross(), $currency)]],
                    ['label' => 'RenovaHub Service Charge', 'lines' => [$this->money($allocation->fee(), $currency)]],
                    ['label' => 'Net Amount', 'lines' => [$this->money($allocation->net(), $currency)]],
                    ['label' => 'Payment Status', 'lines' => ['Successful']],
                ],
            );
        }

        if ($allocation->bucket === PaymentAllocation::CONSTRUCTION) {
            $rows = [
                ['label' => 'Recipient Details', 'lines' => [$allocation->firm?->name ?: $allocation->label ?: 'Construction firm', 'Construction Firm']],
                ['label' => 'Sender Details', 'lines' => [$contractor, 'Contractor']],
                ['label' => 'Transaction Type', 'lines' => ['Construction Firm Payment']],
                ['label' => 'Transaction No.', 'lines' => [$allocation->reference ?: '—']],
                ['label' => 'Project', 'lines' => [$project?->name ?: 'Project']],
                ['label' => 'Scope', 'lines' => [$allocation->detail ?: 'Construction and Labour']],
                ['label' => 'Amount', 'lines' => [$this->money($allocation->gross(), $currency)]],
                ['label' => 'Payment Status', 'lines' => ['Successful']],
            ];
        } else {
            $rows = [
                ['label' => 'Recipient Details', 'lines' => [$allocation->supplier?->name ?: $allocation->label ?: 'Supplier', 'Supplier']],
                ['label' => 'Sender Details', 'lines' => [$contractor, 'Contractor']],
                ['label' => 'Transaction Type', 'lines' => ['Supplier Payment']],
                ['label' => 'Transaction No.', 'lines' => [$allocation->reference ?: '—']],
                ['label' => 'Project', 'lines' => [$project?->name ?: 'Project']],
                ['label' => 'Material', 'lines' => [$allocation->detail ?: 'Project materials']],
                ['label' => 'Amount', 'lines' => [$this->money($allocation->gross(), $currency)]],
                ['label' => 'Payment Status', 'lines' => ['Successful']],
            ];
        }

        return $this->receipt($this->money($allocation->gross(), $currency), $allocation->paid_at, $rows);
    }

    /**
     * @return array<string, mixed>
     */
    public function receiptForDesigner(DesignerEarning $earning): array
    {
        $earning->loadMissing(['project.homeowner', 'designer']);
        $stamp = $earning->created_at;

        if ($earning->recorded_on) {
            $stamp = $earning->recorded_on->copy()->setTimeFrom($earning->created_at ?? now());
        }

        return $this->receipt(
            $this->money((float) $earning->net_amount),
            $stamp,
            [
                ['label' => 'Recipient Details', 'lines' => [$earning->designer?->name ?: 'Designer', 'Designer']],
                ['label' => 'Sender Details', 'lines' => [$earning->project?->homeowner?->name ?: 'Homeowner', 'RenovaHub']],
                ['label' => 'Transaction Type', 'lines' => ['Designer Project Payment']],
                ['label' => 'Transaction No.', 'lines' => [$earning->reference]],
                ['label' => 'Project', 'lines' => [$earning->project?->name ?: 'Project']],
                ['label' => 'Gross Designer Fee', 'lines' => [$this->money((float) $earning->gross_amount)]],
                ['label' => 'RenovaHub Service Charge', 'lines' => [$this->money((float) $earning->fee_amount)]],
                ['label' => 'Net Designer Amount', 'lines' => [$this->money((float) $earning->net_amount)]],
                ['label' => 'Payment Status', 'lines' => ['Successful']],
            ],
        );
    }

    public function money(float $amount, string $currency = 'LKR'): string
    {
        return $currency.' '.number_format($amount, 2);
    }

    public function moneyWhole(float $amount, string $currency = 'LKR'): string
    {
        return $currency.' '.number_format($amount, 0);
    }

    /**
     * @param  array<string, mixed>  $extra
     * @return ?array<string, mixed>
     */
    private function share(string $bucket, float $amount, array $extra): ?array
    {
        if ($amount <= 0) {
            return null;
        }

        return array_merge([
            'bucket' => $bucket,
            'amount' => round($amount, 2),
            'supplier_id' => null,
            'construction_firm_id' => null,
            'payee_user_id' => null,
        ], $extra);
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function upsertAllocation(Payment $payment, Project $project, array $row): void
    {
        $existing = $payment->allocations()->where('bucket', $row['bucket'])->first();

        if ($existing) {
            if ($existing->status === PaymentAllocation::STATUS_PAID) {
                return;
            }

            $existing->update([
                'amount' => $row['amount'],
                'platform_fee' => $row['platform_fee'],
                'net_amount' => $row['net_amount'],
                'supplier_id' => $existing->supplier_id ?: $row['supplier_id'],
                'construction_firm_id' => $existing->construction_firm_id ?: $row['construction_firm_id'],
                'payee_user_id' => $existing->payee_user_id ?: $row['payee_user_id'],
                'label' => $existing->label ?: $row['label'],
                'detail' => $existing->detail ?: $row['detail'],
                'reference' => $existing->reference ?: $this->nextReference($project),
            ]);

            return;
        }

        $payment->allocations()->create([
            'project_id' => $project->id,
            'bucket' => $row['bucket'],
            'amount' => $row['amount'],
            'status' => PaymentAllocation::STATUS_PENDING,
            'reference' => $this->nextReference($project),
            'label' => $row['label'],
            'detail' => $row['detail'],
            'platform_fee' => $row['platform_fee'],
            'net_amount' => $row['net_amount'],
            'payee_user_id' => $row['payee_user_id'],
            'supplier_id' => $row['supplier_id'],
            'construction_firm_id' => $row['construction_firm_id'],
        ]);
    }

    private function settleProfessionals(Payment $payment): void
    {
        $payment->loadMissing('allocations');

        foreach ($payment->allocations as $allocation) {
            if (! in_array($allocation->bucket, [PaymentAllocation::CONTRACTOR, PaymentAllocation::DESIGNER], true)) {
                continue;
            }

            if ($allocation->status === PaymentAllocation::STATUS_PAID) {
                continue;
            }

            $allocation->update([
                'status' => PaymentAllocation::STATUS_PAID,
                'paid_at' => now(),
                'provider' => 'payhere',
                'provider_reference' => $payment->provider_reference,
                'reference' => $allocation->reference ?: $this->nextReference($payment->project),
            ]);
        }
    }

    private function recordDesignerEarning(Payment $payment): void
    {
        $submission = $payment->budgetSubmission;
        $project = $payment->project;

        if ($submission === null || $project?->designer_id === null) {
            return;
        }

        $gross = (float) $submission->designer_fee;

        if ($gross <= 0) {
            return;
        }

        DesignerEarning::query()->updateOrCreate(
            [
                'project_id' => $project->id,
                'designer_id' => $project->designer_id,
                'reference' => $payment->reference,
            ],
            [
                'label' => 'Designer fee · '.$project->name,
                'gross_amount' => $gross,
                'fee_percent' => DesignerEarning::FEE_PERCENT,
                'fee_amount' => DesignerEarning::feeFor($gross),
                'net_amount' => DesignerEarning::netFor($gross),
                'status' => DesignerEarning::STATUS_RECORDED,
                'recorded_on' => now()->toDateString(),
                'notes' => 'RenovaHub service charge on the designer portion of the verified project payment.',
            ],
        );
    }

    /**
     * @return array{action: string, fields: array<string, string>}
     */
    private function payHereCheckout(Payment $payment): array
    {
        $payment->loadMissing(['project', 'payer']);
        $merchantId = (string) config('services.payhere.merchant_id');
        $secret = strtoupper(md5((string) config('services.payhere.merchant_secret')));
        $amount = number_format($payment->homeownerTotal(), 2, '.', '');
        $currency = $payment->currency ?: 'LKR';
        $person = $payment->payer ?: $payment->project?->homeowner;
        $parts = preg_split('/\s+/', trim((string) ($person?->name ?: 'Home Owner'))) ?: [];
        $hash = strtoupper(md5($merchantId.$payment->reference.$amount.$currency.$secret));

        return [
            'action' => config('services.payhere.sandbox')
                ? 'https://sandbox.payhere.lk/pay/checkout'
                : 'https://www.payhere.lk/pay/checkout',
            'fields' => [
                'merchant_id' => $merchantId,
                'return_url' => route('homeowner.payments.payhere.return', $payment),
                'cancel_url' => route('homeowner.payments.payhere.cancel', $payment),
                'notify_url' => route('payments.payhere.notify'),
                'order_id' => $payment->reference,
                'items' => 'RenovaHub project payment · '.($payment->project?->name ?: 'Project'),
                'currency' => $currency,
                'amount' => $amount,
                'first_name' => $parts[0] ?? 'Home',
                'last_name' => $parts[1] ?? 'Owner',
                'email' => (string) ($person?->email ?: 'payments@renovahub.test'),
                'phone' => (string) ($person?->phone ?: '0770000000'),
                'address' => (string) ($payment->project?->address ?: 'Colombo'),
                'city' => (string) ($payment->project?->city ?: 'Colombo'),
                'country' => 'Sri Lanka',
                'hash' => $hash,
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function payHereSignatureValid(array $payload): bool
    {
        $secret = strtoupper(md5((string) config('services.payhere.merchant_secret')));
        $local = strtoupper(md5(
            (string) config('services.payhere.merchant_id').
            ($payload['order_id'] ?? '').
            ($payload['payhere_amount'] ?? '').
            ($payload['payhere_currency'] ?? '').
            ($payload['status_code'] ?? '').
            $secret
        ));

        return hash_equals($local, strtoupper((string) ($payload['md5sig'] ?? '')));
    }

    /**
     * @param  list<array{label: string, lines: list<string>}>  $rows
     * @return array<string, mixed>
     */
    private function receipt(string $amount, mixed $moment, array $rows): array
    {
        $stamp = $moment ? Carbon::parse($moment) : now();

        return [
            'amount' => $amount,
            'status' => 'Successful',
            'datetime' => $stamp->format('M jS, Y H:i:s'),
            'rows' => $rows,
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function sections(Project $project, ?Payment $payment): array
    {
        $allocations = $payment?->allocations ?? collect();
        $currency = $payment?->currency ?: 'LKR';

        $supplier = $allocations->firstWhere('bucket', PaymentAllocation::MATERIALS);
        $firm = $allocations->firstWhere('bucket', PaymentAllocation::CONSTRUCTION);
        $contractor = $allocations->firstWhere('bucket', PaymentAllocation::CONTRACTOR);

        return [
            $this->section('supplier', 'Supplier Payment', $supplier, [
                ['label' => 'Supplier', 'value' => $supplier?->supplier?->name ?: $supplier?->label ?: 'Supplier'],
                ['label' => 'Material', 'value' => $supplier?->detail ?: 'Project materials'],
                ['label' => 'Amount', 'value' => $supplier ? $this->moneyWhole($supplier->gross(), $currency) : '—'],
            ], $project, false),
            $this->section('construction', 'Construction Firm Payment', $firm, [
                ['label' => 'Firm', 'value' => $firm?->firm?->name ?: $firm?->label ?: 'Construction firm'],
                ['label' => 'Scope', 'value' => $firm?->detail ?: 'Construction and Labour'],
                ['label' => 'Amount', 'value' => $firm ? $this->moneyWhole($firm->gross(), $currency) : '—'],
            ], $project, false),
            $this->section('contractor', 'Contractor Payment', $contractor, [
                ['label' => 'Contractor', 'value' => $contractor?->payee?->name ?: $contractor?->label ?: ($project->contractor?->name ?: 'Contractor')],
                ['label' => 'Gross Contractor Fee', 'value' => $contractor ? $this->moneyWhole($contractor->gross(), $currency) : '—'],
                ['label' => 'RenovaHub Service Charge', 'value' => $contractor ? $this->moneyWhole($contractor->fee(), $currency) : '—'],
                ['label' => 'Net Contractor Earnings', 'value' => $contractor ? $this->moneyWhole($contractor->net(), $currency) : '—'],
            ], $project, true),
        ];
    }

    /**
     * @param  list<array{label: string, value: string}>  $rows
     * @return array<string, mixed>
     */
    private function section(string $key, string $heading, ?PaymentAllocation $allocation, array $rows, Project $project, bool $ownEarning): array
    {
        $paid = $allocation?->isPaid() === true;
        $action = 'none';

        if ($allocation && $paid) {
            $action = 'view';
        } elseif ($allocation && ! $ownEarning && $allocation->status === PaymentAllocation::STATUS_PENDING) {
            $action = 'pay';
        }

        $rows[] = ['label' => 'Status', 'value' => $allocation?->statusLabel() ?: 'Pending'];

        return [
            'key' => $key,
            'heading' => $heading,
            'rows' => $rows,
            'action' => $action,
            'allocation' => $allocation,
            'receipt_id' => $paid ? 'allocation-'.$allocation->id : null,
            'confirm' => [
                'party' => $rows[0]['value'] ?? '',
                'party_label' => $rows[0]['label'] ?? '',
                'project' => $project->name,
                'detail_label' => $rows[1]['label'] ?? 'Detail',
                'detail' => $rows[1]['value'] ?? '',
                'amount' => $allocation ? $this->money($allocation->gross()) : '',
                'reference' => $allocation?->reference ?: '',
            ],
        ];
    }
}
