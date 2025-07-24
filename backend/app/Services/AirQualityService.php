<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AirQualityService
{
    public function fetchCityAirQuality(string $country, string $state, string $city): array
    {
        $response = Http::get('http://api.airvisual.com/v2/city', [
            'country' => $country,
            'state' => $state,
            'city' => $city,
            'key' => config('services.iqair.api_key'),
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception("Failed to fetch air quality for $city, $state: " . $response->body());
    }
}
