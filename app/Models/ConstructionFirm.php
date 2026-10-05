<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConstructionFirm extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'city',
        'location',
        'specialisation',
        'about',
        'rating',
        'review_count',
        'years_experience',
        'completed_projects',
        'starting_price',
        'portfolio',
        'highlights',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'decimal:1',
            'starting_price' => 'decimal:2',
            'portfolio' => 'array',
            'highlights' => 'array',
        ];
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(ConstructionFirmQuotation::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ConstructionAssignment::class);
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: [];

        return strtoupper(collect($parts)->take(2)->map(fn ($part) => substr($part, 0, 1))->implode(''));
    }

    public function money(): string
    {
        return 'LKR '.number_format((float) $this->starting_price, 0);
    }
}
