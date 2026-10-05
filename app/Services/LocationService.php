<?php

namespace App\Services;

use App\Models\Project;

/**
 * Project location is entered and stored manually.
 * Address, city, province and postal code are not sent to an external API.
 */
class LocationService
{
    public function label(Project $project): string
    {
        $parts = array_values(array_filter([
            $project->address,
            $project->city,
            $project->province,
            $project->postal_code,
        ]));

        return $parts === [] ? 'Location not added yet' : implode(', ', $parts);
    }
}
