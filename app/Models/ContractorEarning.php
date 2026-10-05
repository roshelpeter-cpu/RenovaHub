<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractorEarning extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_RECORDED = 'recorded';

    protected $fillable = [
        'project_id',
        'contractor_id',
        'payment_id',
        'supplier_order_id',
        'label',
        'gross_amount',
        'fee_percent',
        'fee_amount',
        'net_amount',
        'status',
        'reference',
        'recorded_on',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'decimal:2',
            'fee_percent' => 'decimal:2',
            'fee_amount' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'recorded_on' => 'date',
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

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function supplierOrder(): BelongsTo
    {
        return $this->belongsTo(SupplierOrder::class);
    }

    /**
     * The fee percentage comes from configuration and is stored on the row.
     * Blade must print the stored amount, not recalculate a hardcoded 2%.
     */
    public static function percent(): float
    {
        return (float) config('renovahub.platform_fee_percent', 2);
    }

    public static function feeFor(float $gross, ?float $percent = null): float
    {
        $percent ??= self::percent();

        return round($gross * ($percent / 100), 2);
    }

    public static function netFor(float $gross, ?float $percent = null): float
    {
        return round($gross - self::feeFor($gross, $percent), 2);
    }

    public function money(float $amount): string
    {
        return 'LKR '.number_format($amount, 0);
    }

    public function statusLabel(): string
    {
        return $this->status === self::STATUS_RECORDED ? 'Paid' : 'Pending';
    }
}
