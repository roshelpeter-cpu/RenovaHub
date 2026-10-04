<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProfessionalProfile extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'professional_type',
        'business_name',
        'title',
        'specialization',
        'bio',
        'location',
        'avatar_path',
        'years_experience',
        'completed_projects_count',
        'rating',
        'starting_price',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'years_experience' => 'integer',
            'completed_projects_count' => 'integer',
            'rating' => 'decimal:1',
            'starting_price' => 'decimal:2',
        ];
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProfessionalReview::class);
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar_path ? asset($this->avatar_path) : null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function portfolioItems(): HasMany
    {
        return $this->hasMany(PortfolioItem::class);
    }

    public function displayName(): string
    {
        return $this->business_name ?: $this->user->name;
    }
}
