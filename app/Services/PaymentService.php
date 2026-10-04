<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Project;

/**
 * The payment provider is intentionally not called.
 * This service is the only place a gateway should be attached later.
 */
class PaymentService
{
    public function placeholder(Payment $payment): string
    {
        return 'Payment gateway integration will be connected here. No payment has been sent.';
    }

    public function nextReference(Project $project): string
    {
        $count = Payment::query()->count() + 1;

        return 'RH-PAY-'.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
