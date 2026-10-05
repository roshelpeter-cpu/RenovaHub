<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A set of supplier or construction-firm options waiting on the homeowner.
 * The contractor can submit the set. Only the homeowner can approve one option.
 */
class ProcurementProposal extends Model
{
    public const TYPE_SUPPLIER = 'supplier';

    public const TYPE_FIRM = 'construction_firm';

    public const STATUS_AWAITING = 'awaiting_homeowner';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_CLARIFICATION = 'clarification';

    protected $fillable = [
        'project_id',
        'contractor_id',
        'type',
        'status',
        'selected_supplier_price_id',
        'selected_construction_quotation_id',
        'homeowner_note',
        'submitted_at',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
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

    public function options(): HasMany
    {
        return $this->hasMany(ProcurementOption::class);
    }

    public function selectedPrice(): BelongsTo
    {
        return $this->belongsTo(SupplierPrice::class, 'selected_supplier_price_id');
    }

    public function selectedQuotation(): BelongsTo
    {
        return $this->belongsTo(ConstructionFirmQuotation::class, 'selected_construction_quotation_id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_CLARIFICATION => 'Clarification Requested',
            default => 'Awaiting Approval',
        };
    }
}
