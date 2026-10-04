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
        'about',
        'location',
        'avatar_path',
        'years_experience',
        'completed_projects_count',
        'rating',
        'starting_price',
        'listed',
        'featured',
        'verified',
        'review_count',
        'tags',
        'cover_path',
        'client_satisfaction',
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
            'listed' => 'boolean',
            'featured' => 'boolean',
            'verified' => 'boolean',
            'review_count' => 'integer',
            'tags' => 'array',
            'client_satisfaction' => 'integer',
        ];
    }

    public function caseStudies(): HasMany
    {
        return $this->hasMany(ProfessionalProject::class)->orderBy('sort_order');
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

    public function services(): HasMany
    {
        return $this->hasMany(ProfessionalService::class);
    }

    public function favourites(): HasMany
    {
        return $this->hasMany(ProfessionalFavourite::class);
    }

    public function coverUrl(): ?string
    {
        return $this->cover_path ? asset($this->cover_path) : $this->avatarUrl();
    }

    /**
     * Contractors list as companies, so the card uses a site/project photo
     * rather than a portrait that belongs on a designer card.
     */
    public function listingImageUrl(): ?string
    {
        if ($this->professional_type === 'contractor') {
            return $this->coverUrl() ?: $this->avatarUrl();
        }

        return $this->avatarUrl() ?: $this->coverUrl();
    }

    public function aboutText(): string
    {
        return $this->about ?: (string) $this->bio;
    }

    /**
     * @return list<string>
     */
    public function tagList(): array
    {
        return $this->tags ?? [];
    }

    public function displayName(): string
    {
        return $this->business_name ?: $this->user->name;
    }
}
