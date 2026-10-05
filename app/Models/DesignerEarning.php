<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesignerEarning extends Model
{
    /**
     * RenovaHub keeps a professional service fee. This is not a salary deduction,
     * and the designer cannot change the percentage from the browser.
     */
    public const FEE_PERCENT = 2;

    public const STATUS_PENDING = 'pending';

    public const STATUS_RECORDED = 'recorded';

    protected $fillable = [
        'project_id',
        'designer_id',
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

    public function designer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'designer_id');
    }

    public static function feeFor(float $gross): float
    {
        return round($gross * self::FEE_PERCENT / 100, 2);
    }

    public static function netFor(float $gross): float
    {
        return round($gross - self::feeFor($gross), 2);
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
