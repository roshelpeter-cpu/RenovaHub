<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_CLARIFICATION = 'clarification_required';

    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'project_id',
        'contractor_id',
        'number',
        'description',
        'category',
        'materials',
        'labour',
        'additional_costs',
        'discount',
        'subtotal',
        'total',
        'valid_until',
        'status',
        'approved_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'materials' => 'decimal:2',
            'labour' => 'decimal:2',
            'additional_costs' => 'decimal:2',
            'discount' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
            'valid_until' => 'date',
            'approved_at' => 'datetime',
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

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function reviewLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_CLARIFICATION => 'In Review',
            default => 'Pending',
        };
    }

    public function statusLabel(): string
    {
        if (is_string($this->notes) && $this->notes !== '') {
            return $this->notes;
        }

        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_CLARIFICATION => 'Clarification Required',
            self::STATUS_EXPIRED => 'Expired',
            default => 'Pending',
        };
    }

    public function money(mixed $amount): string
    {
        return 'LKR '.number_format((float) $amount, 2);
    }

    /**
     * Contractor-facing labels follow draft, submitted, then homeowner review.
     * The contractor cannot move a quotation to approved.
     */
    public function contractorStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_PENDING => 'In Review',
            self::STATUS_CLARIFICATION => 'Clarification Requested',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_EXPIRED => 'Expired',
            default => 'Draft',
        };
    }
}
