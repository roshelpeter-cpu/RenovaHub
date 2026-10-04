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
}
