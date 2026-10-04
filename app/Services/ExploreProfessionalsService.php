<?php

namespace App\Services;

use App\Models\ProfessionalFavourite;
use App\Models\ProfessionalProfile;
use App\Models\User;

class ExploreProfessionalsService
{
    /**
     * Directory listings are limited to profiles marked listed.
     * Team-picker professionals stay available on the wizard without flooding Explore.
     *
     * @return array<string, mixed>
     */
    public function directory(string $type, string $search, string $location, string $projectType, string $priceRange, string $sort, User $homeowner): array
    {
        $role = $type === 'contractor' ? 'contractor' : 'designer';

        $query = ProfessionalProfile::query()
            ->where('listed', true)
            ->where('professional_type', $role)
            ->with(['user', 'portfolioItems', 'caseStudies']);

        if ($search !== '') {
            $term = '%'.addcslashes($search, '%_\\').'%';
            $query->where(function ($inner) use ($term) {
                $inner->where('business_name', 'like', $term)
                    ->orWhere('title', 'like', $term)
                    ->orWhere('specialization', 'like', $term)
                    ->orWhere('bio', 'like', $term)
                    ->orWhere('location', 'like', $term)
                    ->orWhereHas('user', fn ($user) => $user->where('name', 'like', $term));
            });
        }

        if ($location !== '') {
            $query->where('location', $location);
        }

        if ($projectType !== '') {
            $query->where(function ($inner) use ($projectType) {
                $inner->whereJsonContains('tags', $projectType)
                    ->orWhere('specialization', 'like', '%'.$projectType.'%');
            });
        }

        if ($priceRange === 'under_200k') {
            $query->where('starting_price', '<', 200000);
        } elseif ($priceRange === '200_500k') {
            $query->whereBetween('starting_price', [200000, 500000]);
        } elseif ($priceRange === 'over_500k') {
            $query->where('starting_price', '>', 500000);
        }

        $sorted = match ($sort) {
            'rating' => $query->orderByDesc('rating'),
            'reviews' => $query->orderByDesc('review_count'),
            'name' => $query->orderBy('business_name')->orderBy('id'),
            default => $query->orderByDesc('featured')->orderBy('id'),
        };

        $profiles = $sorted->get();
        $favouriteIds = ProfessionalFavourite::query()
            ->where('user_id', $homeowner->id)
            ->pluck('professional_profile_id');

        $profiles->each(fn (ProfessionalProfile $profile) => $profile->setRelation('user', $profile->user));

        return [
            'featured' => $profiles->where('featured', true)->take(4)->values(),
            'all' => $profiles->where('featured', false)->values(),
            'favouriteIds' => $favouriteIds,
            'locations' => ProfessionalProfile::query()->where('listed', true)->whereNotNull('location')->distinct()->orderBy('location')->pluck('location'),
        ];
    }

    /**
     * Favourites belong to the authenticated homeowner. The profile id is
     * loaded from the database rather than trusted as a public user id.
     */
    public function toggleFavourite(User $homeowner, int $profileId): void
    {
        abort_unless($homeowner->isHomeowner(), 403);

        $profile = ProfessionalProfile::query()->where('listed', true)->findOrFail($profileId);

        $existing = ProfessionalFavourite::query()
            ->where('user_id', $homeowner->id)
            ->where('professional_profile_id', $profile->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return;
        }

        ProfessionalFavourite::query()->create([
            'user_id' => $homeowner->id,
            'professional_profile_id' => $profile->id,
        ]);
    }
}
