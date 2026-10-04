<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_PAID = 'paid';

    public const STATUS_FAILED = 'failed';

    public const STATUS_REFUNDED = 'refunded';

    public const FEE_PERCENT = 2;

    protected $fillable = [
        'project_id',
        'quotation_id',
        'reference',
        'amount',
        'renovation_amount',
        'platform_fee',
        'fee_percent',
        'currency',
        'method',
        'status',
        'provider_reference',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'renovation_amount' => 'decimal:2',
            'platform_fee' => 'decimal:2',
            'fee_percent' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function statusLabel(): string
    {
        return ucfirst($this->status);
    }

    public function money(): string
    {
        return $this->formatMoney($this->homeownerTotal());
    }

    public function renovationAmount(): float
    {
        return (float) ($this->renovation_amount ?? $this->amount);
    }

    public function platformFee(): float
    {
        return (float) ($this->platform_fee ?? 0);
    }

    /**
     * The stored amount remains the renovation value used by project totals.
     * The homeowner pays that amount plus the platform fee.
     */
    public function homeownerTotal(): float
    {
        return $this->renovationAmount() + $this->platformFee();
    }

    public function formatMoney(float $amount): string
    {
        return ($this->currency ?: 'LKR').' '.number_format($amount, 0);
    }
}
