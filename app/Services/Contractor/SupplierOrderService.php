<?php

namespace App\Services\Contractor;

use App\Models\ContractorEarning;
use App\Models\SupplierOrder;
use App\Models\SupplierPrice;
use App\Models\SupplierPriceRequest;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;

class SupplierOrderService
{
    public function __construct(
        private PaymentService $payments,
        private NotificationService $notifications,
        private ActivityLogService $activity,
    ) {}

    /**
     * Placing an order turns an offered supplier price into a purchase and a
     * pending homeowner payment. The order stays unpaid until PayHere confirms.
     */
    public function place(User $contractor, SupplierPrice $price, ?string $address, ?string $notes): SupplierOrder
    {
        return DB::transaction(function () use ($contractor, $price, $address, $notes) {
            $request = $price->request()->lockForUpdate()->firstOrFail();

            abort_unless($price->canOrder(), 422);
            abort_unless((int) $request->contractor_id === (int) $contractor->id, 403);

            $order = SupplierOrder::query()->create([
                'number' => $this->nextNumber(),
                'project_id' => $request->project_id,
                'contractor_id' => $contractor->id,
                'supplier_id' => $request->supplier_id,
                'supplier_price_id' => $price->id,
                'supplier_price_request_id' => $request->id,
                'item' => $request->product,
                'quantity' => $request->quantity,
                'unit' => $request->unit,
                'amount' => $price->quoted_price,
                'delivery_address' => $address ?: $request->project->address,
                'required_delivery_date' => $request->required_by,
                'notes' => $notes,
                'status' => SupplierOrder::STATUS_PAYMENT_PENDING,
                'ordered_at' => now(),
            ]);

            $price->update(['status' => SupplierPrice::STATUS_ACCEPTED]);
            $request->update(['status' => SupplierPriceRequest::STATUS_ORDERED]);

            $payment = $this->payments->openForSupplierOrder($order);

            ContractorEarning::query()->create([
                'project_id' => $order->project_id,
                'contractor_id' => $contractor->id,
                'payment_id' => $payment->id,
                'supplier_order_id' => $order->id,
                'label' => $order->item,
                'gross_amount' => $payment->renovationAmount(),
                'fee_percent' => $payment->fee_percent,
                'fee_amount' => $payment->platformFee(),
                'net_amount' => ContractorEarning::netFor($payment->renovationAmount(), (float) $payment->fee_percent),
                'status' => ContractorEarning::STATUS_PENDING,
                'reference' => $payment->reference,
                'notes' => 'Platform service fee is withheld when the homeowner payment is verified. It is not a salary deduction.',
            ]);

            $homeowner = $order->project->homeowner;

            if ($homeowner) {
                $this->notifications->notify(
                    $homeowner,
                    'Payment requested for '.$order->item,
                    'A supplier order is waiting for payment. Nothing has been sent to PayHere yet.',
                    'payments',
                    route('homeowner.payments.show', [$order->project, $payment]),
                );
            }

            $this->activity->record($order->project, $contractor, 'supplier.order.placed', 'Supplier order placed for '.$order->item.'.');

            return $order->fresh(['payment', 'supplier', 'project']);
        });
    }

    /**
     * Fulfilment steps after payment. Paid is intentionally not accepted here.
     */
    public function advance(SupplierOrder $order, string $status): SupplierOrder
    {
        abort_unless(in_array($status, $order->contractorTransitions(), true), 403);

        $order->update(['status' => $status]);

        return $order->fresh();
    }

    private function nextNumber(): string
    {
        $count = SupplierOrder::query()->count() + 1;

        return 'SO-'.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
