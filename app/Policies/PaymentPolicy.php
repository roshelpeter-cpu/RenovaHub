<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function view(User $user, Payment $payment): bool
    {
        return $user->can('view', $payment->project);
    }

    /**
     * Checkout stays on open projects. Historical payments remain viewable.
     */
    public function pay(User $user, Payment $payment): bool
    {
        return $this->view($user, $payment)
            && $payment->status === Payment::STATUS_PENDING
            && ! $payment->project->isClosedRecord();
    }
}
