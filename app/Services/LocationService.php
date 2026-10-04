<?php

namespace App\Services;

/**
 * Google Places will resolve addresses after hosting.
 * Until a key exists, projects keep the city, address and coordinates already stored.
 */
class LocationService
{
    public function isConfigured(): bool
    {
        return filled(config('services.google.places_key'));
    }
}
