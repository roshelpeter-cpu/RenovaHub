<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function view(User $user, Payment $payment): bool
    {
        return $user->can('view', $payment->project) || $user->can('construct', $payment->project);
    }

    /**
     * Checkout stays with the homeowner. The contractor can see the status
     * but cannot mark a payment paid from the browser.
     */
    public function pay(User $user, Payment $payment): bool
    {
        return $user->isHomeowner()
            && (int) $payment->project->user_id === (int) $user->id
            && $payment->status === Payment::STATUS_PENDING
            && ! $payment->project->isClosedRecord();
    }
}
