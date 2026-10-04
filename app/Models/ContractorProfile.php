<?php

namespace App\Models;

/**
 * Contractor profiles share professional_profiles.
 * professional_type is the discriminator, not a second table.
 */
class ContractorProfile extends ProfessionalProfile
{
    protected $table = 'professional_profiles';

    protected static function booted(): void
    {
        static::addGlobalScope('contractor', function ($query) {
            $query->where('professional_type', 'contractor');
        });

        static::creating(function (self $profile) {
            $profile->professional_type = 'contractor';
        });
    }
}
