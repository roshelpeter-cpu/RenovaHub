<?php

namespace App\Models;

/**
 * Designer profiles share professional_profiles.
 * A second table would let the same person exist twice.
 */
class DesignerProfile extends ProfessionalProfile
{
    protected $table = 'professional_profiles';

    protected static function booted(): void
    {
        static::addGlobalScope('designer', function ($query) {
            $query->where('professional_type', 'designer');
        });

        static::creating(function (self $profile) {
            $profile->professional_type = 'designer';
        });
    }
}
