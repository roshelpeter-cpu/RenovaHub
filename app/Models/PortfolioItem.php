<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PortfolioItem extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'professional_profile_id',
        'title',
        'description',
        'image',
        'category',
        'location',
        'budget_min',
        'budget_max',
        'completion_year',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'completion_year' => 'integer',
            'budget_min' => 'decimal:2',
            'budget_max' => 'decimal:2',
        ];
    }

    public function professionalProfile(): BelongsTo
    {
        return $this->belongsTo(ProfessionalProfile::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PortfolioImage::class);
    }

    public function imageUrl(): string
    {
        return asset($this->image);
    }
}
