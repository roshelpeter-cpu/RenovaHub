<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ledger row written after a verified project payment.
 * PayHere is not asked to split the charge; this records how RenovaHub allocates it.
 */
class PaymentAllocation extends Model
{
    public const DESIGNER = 'designer';

    public const MATERIALS = 'materials';

    public const CONSTRUCTION = 'construction';

    public const CONTRACTOR = 'contractor';

    public const PLATFORM = 'platform';

    protected $fillable = [
        'payment_id',
        'project_id',
        'bucket',
        'amount',
        'payee_user_id',
        'supplier_id',
        'construction_firm_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
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
}
