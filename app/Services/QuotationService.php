<?php

namespace App\Services;

use App\Events\QuotationDecided;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class QuotationService
{
    public function __construct(private NotificationService $notifications) {}

    /**
     * Totals are calculated here so a tampered line total cannot be posted by the browser.
     */
    public function recalculate(Quotation $quotation): Quotation
    {
        $materials = (float) $quotation->materials;
        $labour = (float) $quotation->labour;
        $additional = (float) $quotation->additional_costs;
        $discount = (float) $quotation->discount;
        $subtotal = $materials + $labour + $additional;

        $quotation->update([
            'subtotal' => $subtotal,
            'total' => max(0, $subtotal - $discount),
        ]);

        return $quotation->refresh();
    }

    public function decide(Quotation $quotation, User $homeowner, string $status, ?string $note = null): Quotation
    {
        return DB::transaction(function () use ($quotation, $homeowner, $status, $note) {
            $quotation->update([
                'status' => $status,
                'notes' => $note ?? $quotation->notes,
            ]);

            if ($status === Quotation::STATUS_APPROVED) {
                $quotation->project->update(['current_budget' => $quotation->total]);
            }

            $this->notifications->notify(
                $homeowner,
                'Quotation '.$quotation->number.' updated',
                'The quotation is now '.$quotation->statusLabel().'.',
                'quotations',
                route('homeowner.quotations.show', [$quotation->project, $quotation]),
            );

            QuotationDecided::dispatch($quotation->fresh(), $status);

            return $quotation->fresh();
        });
    }
}
