<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierStartingPrice extends Model
{
    protected $fillable = [
        'supplier_id',
        'label',
        'amount',
        'unit',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function label(): string
    {
        $unit = $this->unit ? '/'.$this->unit : '';

        return 'From LKR '.number_format((float) $this->amount, 0).$unit;
    }
}
