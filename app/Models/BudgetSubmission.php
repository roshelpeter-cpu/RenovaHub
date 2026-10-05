<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The single project budget the homeowner approves before the one project payment.
 */
class BudgetSubmission extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_AWAITING = 'awaiting_homeowner';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'project_id',
        'contractor_id',
        'designer_fee',
        'materials',
        'construction',
        'contractor_fee',
        'changes',
        'platform_fee',
        'fee_percent',
        'total',
        'status',
        'payment_id',
        'submitted_at',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'designer_fee' => 'decimal:2',
            'materials' => 'decimal:2',
            'construction' => 'decimal:2',
            'contractor_fee' => 'decimal:2',
            'changes' => 'decimal:2',
            'platform_fee' => 'decimal:2',
            'fee_percent' => 'decimal:2',
            'total' => 'decimal:2',
            'submitted_at' => 'datetime',
            'decided_at' => 'datetime',
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

    /**
     * @return list<array{key: string, label: string, amount: float}>
     */
    public function lines(): array
    {
        return [
            ['key' => 'designer', 'label' => 'Designer Fee', 'amount' => (float) $this->designer_fee],
            ['key' => 'materials', 'label' => 'Materials', 'amount' => (float) $this->materials],
            ['key' => 'construction', 'label' => 'Construction & Labour', 'amount' => (float) $this->construction],
            ['key' => 'contractor', 'label' => 'Contractor Fee', 'amount' => (float) $this->contractor_fee],
            ['key' => 'changes', 'label' => 'Additional Changes', 'amount' => (float) $this->changes],
            ['key' => 'platform', 'label' => 'RenovaHub Platform Service Fee', 'amount' => (float) $this->platform_fee],
        ];
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_AWAITING => 'Awaiting Approval',
            default => 'Draft',
        };
    }
}
