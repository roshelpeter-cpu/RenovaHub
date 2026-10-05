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
        'supplier_order_id',
        'budget_submission_id',
        'payer_id',
        'payee_id',
        'reference',
        'amount',
        'renovation_amount',
        'platform_fee',
        'net_amount',
        'fee_percent',
        'currency',
        'method',
        'provider',
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
            'net_amount' => 'decimal:2',
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

    public function supplierOrder(): BelongsTo
    {
        return $this->belongsTo(SupplierOrder::class);
    }

    public function budgetSubmission(): BelongsTo
    {
        return $this->belongsTo(BudgetSubmission::class);
    }

    public function allocations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function payee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payee_id');
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
     * Quotation payments add the service fee on top of the renovation amount.
     * A supplier-order request bills the agreed supplier price. The same fee
     * is withheld on the contractor earning, so it is not charged twice.
     */
    public function homeownerTotal(): float
    {
        if ($this->supplier_order_id) {
            return $this->renovationAmount();
        }

        return $this->renovationAmount() + $this->platformFee();
    }

    public function formatMoney(float $amount): string
    {
        return ($this->currency ?: 'LKR').' '.number_format($amount, 0);
    }
}
