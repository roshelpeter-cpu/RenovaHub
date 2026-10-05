<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierReview extends Model
{
    protected $fillable = [
        'supplier_id',
        'author_name',
        'project_title',
        'body',
        'rating',
    ];

    protected function casts(): array
    {
        return ['rating' => 'decimal:1'];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
