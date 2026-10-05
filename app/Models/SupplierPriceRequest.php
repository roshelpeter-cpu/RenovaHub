<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierPriceRequest extends Model
{
    public const STATUS_SENT = 'sent';

    public const STATUS_QUOTED = 'quoted';

    public const STATUS_REVISION = 'revision_requested';

    public const STATUS_ORDERED = 'ordered';

    protected $fillable = [
        'project_id',
        'contractor_id',
        'supplier_id',
        'material',
        'product',
        'quantity',
        'unit',
        'required_by',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'required_by' => 'date',
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

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(SupplierPrice::class);
    }

    public function latestPrice(): ?SupplierPrice
    {
        $prices = $this->relationLoaded('prices') ? $this->prices : $this->prices()->get();

        return $prices->sortByDesc('id')->first();
    }

    public function quantityLabel(): string
    {
        $quantity = rtrim(rtrim(number_format((float) $this->quantity, 2), '0'), '.');

        return $quantity.' '.$this->unit;
    }
}
