<?php

namespace App\Services;

use App\Models\ContractorEarning;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\SupplierOrder;
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
            'fee_percent' => $percent,
            'currency' => 'LKR',
            'status' => Payment::STATUS_PENDING,
            'notes' => 'Opened from '.$quotation->number.'. PayHere has not been called.',
        ]);
    }

    public function nextReference(Project $project): string
    {
        $count = Payment::query()->count() + 1;

        return 'RH-PAY-'.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
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
            'fee_percent' => $percent,
            'currency' => $order->project->currency ?: 'LKR',
            'provider' => 'payhere',
            'status' => Payment::STATUS_PENDING,
            'notes' => 'Opened from supplier order '.$order->number.'. PayHere has not been called.',
        ]);
    }

    /**
     * Only a verified provider callback should call this. There is no
     * contractor route that reaches it.
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

            return $payment->fresh();
        });
    }
}
