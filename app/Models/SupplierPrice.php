<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SupplierPrice extends Model
{
    public const STATUS_OFFERED = 'offered';

    public const STATUS_REVISION = 'revision_requested';

    public const STATUS_ACCEPTED = 'accepted';

    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'supplier_price_request_id',
        'supplier_id',
        'quoted_price',
        'lead_time_days',
        'delivery',
        'warranty',
        'valid_until',
        'message',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'quoted_price' => 'decimal:2',
            'lead_time_days' => 'integer',
            'valid_until' => 'date',
        ];
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(SupplierPriceRequest::class, 'supplier_price_request_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function order(): HasOne
    {
        return $this->hasOne(SupplierOrder::class);
    }

    public function money(): string
    {
        return 'LKR '.number_format((float) $this->quoted_price, 0);
    }

    public function canOrder(): bool
    {
        return $this->status === self::STATUS_OFFERED
            && ($this->valid_until === null || $this->valid_until->gte(now()->startOfDay()));
    }
}
