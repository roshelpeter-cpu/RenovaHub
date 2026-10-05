<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A construction firm price offered to the contractor.
 * Status moves to approved only when the homeowner accepts the proposal that contains it.
 */
class ConstructionFirmQuotation extends Model
{
    public const STATUS_RECEIVED = 'received';

    public const STATUS_PROPOSED = 'proposed';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_CLARIFICATION = 'clarification';

    protected $fillable = [
        'project_id',
        'contractor_id',
        'construction_firm_id',
        'price',
        'duration_days',
        'start_date',
        'completion_date',
        'scope',
        'terms',
        'notes',
        'warranty',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'duration_days' => 'integer',
            'start_date' => 'date',
            'completion_date' => 'date',
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

    public function firm(): BelongsTo
    {
        return $this->belongsTo(ConstructionFirm::class, 'construction_firm_id');
    }

    public function money(): string
    {
        return 'LKR '.number_format((float) $this->price, 0);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_PROPOSED => 'Awaiting Approval',
            self::STATUS_CLARIFICATION => 'Clarification Requested',
            default => 'Received',
        };
    }
}
