<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo_path',
        'hero_path',
        'location',
        'city',
        'service_area',
        'about',
        'years_experience',
        'completed_orders',
        'rating',
        'review_count',
        'why_choose',
        'portfolio_images',
    ];

    protected function casts(): array
    {
        return [
            'years_experience' => 'integer',
            'completed_orders' => 'integer',
            'rating' => 'decimal:1',
            'review_count' => 'integer',
            'why_choose' => 'array',
            'portfolio_images' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Supplier $supplier): void {
            if ($supplier->slug === null || $supplier->slug === '') {
                $supplier->slug = Str::slug($supplier->name);
            }
        });
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(SupplierCategory::class, 'supplier_category_supplier');
    }

    public function startingPrices(): HasMany
    {
        return $this->hasMany(SupplierStartingPrice::class)->orderBy('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(SupplierReview::class)->latest();
    }

    public function priceRequests(): HasMany
    {
        return $this->hasMany(SupplierPriceRequest::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(SupplierPrice::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(SupplierOrder::class);
    }

    public function heroUrl(): string
    {
        return asset($this->hero_path ?: 'images/renova/about-interior.jpg');
    }

    public function logoUrl(): ?string
    {
        return $this->logo_path ? asset($this->logo_path) : null;
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name)) ?: [];

        return strtoupper(collect($parts)->take(2)->map(fn ($part) => substr($part, 0, 1))->implode(''));
    }

    public function experienceLabel(): string
    {
        return $this->years_experience.'+ Years';
    }

    public function ordersLabel(): string
    {
        return $this->completed_orders.'+ Orders';
    }
}
