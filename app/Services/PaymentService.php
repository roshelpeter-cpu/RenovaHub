<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Project;
use App\Models\Quotation;

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
}
