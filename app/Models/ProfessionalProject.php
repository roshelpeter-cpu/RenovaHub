<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ProfessionalProject extends Model
{
    protected $fillable = [
        'professional_profile_id',
        'slug',
        'title',
        'category',
        'location',
        'property_type',
        'project_type',
        'size_sq_ft',
        'budget',
        'completed_on',
        'summary',
        'overview',
        'highlights',
        'process',
        'mood_board',
        'materials',
        'client_name',
        'client_location',
        'client_rating',
        'client_body',
        'hero_image',
        'featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'size_sq_ft' => 'integer',
            'budget' => 'decimal:2',
            'completed_on' => 'date',
            'highlights' => 'array',
            'process' => 'array',
            'mood_board' => 'array',
            'materials' => 'array',
            'client_rating' => 'decimal:1',
            'featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ProfessionalProject $project): void {
            if ($project->slug === null || $project->slug === '') {
                $project->slug = Str::slug($project->title);
            }
        });
    }

    public function professionalProfile(): BelongsTo
    {
        return $this->belongsTo(ProfessionalProfile::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProfessionalProjectImage::class)->orderBy('sort_order');
    }

    public function heroUrl(): string
    {
        return asset($this->hero_image);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Hero plus gallery rows, de-duplicated, so a project always has
     * at least the five stored photographs when they exist.
     *
     * @return \Illuminate\Support\Collection<int, string>
     */
    public function galleryPaths(): \Illuminate\Support\Collection
    {
        return collect([$this->hero_image])
            ->merge($this->images->pluck('path'))
            ->filter()
            ->unique()
            ->values();
    }
}
