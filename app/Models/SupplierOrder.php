<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SupplierOrder extends Model
{
    public const STATUS_PRICE_RECEIVED = 'price_received';

    public const STATUS_PAYMENT_PENDING = 'payment_pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_DISPATCHED = 'dispatched';

    public const STATUS_DELIVERED = 'delivered';

    /**
     * Payment Received is the paid state. The contractor never writes that
     * step; only a verified provider confirmation may move the order there.
     *
     * @var list<string>
     */
    public const PIPELINE = [
        self::STATUS_PRICE_RECEIVED,
        self::STATUS_PAYMENT_PENDING,
        self::STATUS_PAID,
        self::STATUS_PROCESSING,
        self::STATUS_DISPATCHED,
        self::STATUS_DELIVERED,
    ];

    protected $fillable = [
        'number',
        'project_id',
        'contractor_id',
        'supplier_id',
        'supplier_price_id',
        'supplier_price_request_id',
        'item',
        'quantity',
        'unit',
        'amount',
        'delivery_address',
        'required_delivery_date',
        'notes',
        'status',
        'ordered_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'amount' => 'decimal:2',
            'required_delivery_date' => 'date',
            'ordered_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contractor_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function price(): BelongsTo
    {
        return $this->belongsTo(SupplierPrice::class, 'supplier_price_id');
    }

    public function priceRequest(): BelongsTo
    {
        return $this->belongsTo(SupplierPriceRequest::class, 'supplier_price_request_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function earning(): HasOne
    {
        return $this->hasOne(ContractorEarning::class);
    }

    public function money(): string
    {
        return 'LKR '.number_format((float) $this->amount, 0);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PRICE_RECEIVED => 'Price Received',
            self::STATUS_PAYMENT_PENDING => 'Payment Pending',
            self::STATUS_PAID => 'Payment Received',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_DISPATCHED => 'Dispatched',
            self::STATUS_DELIVERED => 'Delivered',
            default => 'Payment Pending',
        };
    }

    public function pipelineIndex(): int
    {
        $index = array_search($this->status, self::PIPELINE, true);

        return $index === false ? 1 : (int) $index;
    }

    /**
     * After payment is confirmed the contractor may advance fulfilment only.
     * Paid itself is absent so a form post cannot mark the order paid.
     *
     * @return list<string>
     */
    public function contractorTransitions(): array
    {
        return match ($this->status) {
            self::STATUS_PAID => [self::STATUS_PROCESSING],
            self::STATUS_PROCESSING => [self::STATUS_DISPATCHED],
            self::STATUS_DISPATCHED => [self::STATUS_DELIVERED],
            default => [],
        };
    }
}
