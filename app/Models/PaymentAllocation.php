<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One share of a project payment: supplier, construction firm, contractor, designer, or platform fee.
 * PayHere is not asked to split the charge. RenovaHub records each share here.
 */
class PaymentAllocation extends Model
{
    public const DESIGNER = 'designer';

    public const MATERIALS = 'materials';

    public const CONSTRUCTION = 'construction';

    public const CONTRACTOR = 'contractor';

    public const PLATFORM = 'platform';

    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    protected $fillable = [
        'payment_id',
        'project_id',
        'bucket',
        'amount',
        'status',
        'reference',
        'label',
        'detail',
        'platform_fee',
        'net_amount',
        'payee_user_id',
        'payer_id',
        'supplier_id',
        'construction_firm_id',
        'provider',
        'provider_reference',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'platform_fee' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function firm(): BelongsTo
    {
        return $this->belongsTo(ConstructionFirm::class, 'construction_firm_id');
    }

    public function payee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payee_user_id');
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function statusLabel(): string
    {
        return $this->isPaid() ? 'Paid' : 'Pending';
    }

    public function gross(): float
    {
        return (float) $this->amount;
    }

    public function fee(): float
    {
        return (float) $this->platform_fee;
    }

    public function net(): float
    {
        return (float) ($this->net_amount ?? $this->amount);
    }
}
