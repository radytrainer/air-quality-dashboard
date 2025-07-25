<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\AirQualityService;
use Carbon\Carbon;

class AqiEuropeController extends Controller
{
    public function getAllCitiesAirQuality(): JsonResponse
{
    $targetCities = [
        ['country' => 'France', 'state' => 'Île-de-France', 'city' => 'Paris'],
        ['country' => 'Spain', 'state' => 'Madrid', 'city' => 'Madrid'],
        ['country' => 'Italy', 'state' => 'Lazio', 'city' => 'Rome'],
        ['country' => 'Turkey', 'state' => 'Istanbul', 'city' => 'Istanbul'],
        ['country' => 'United Kingdom', 'state' => 'England', 'city' => 'London'],
        ['country' => 'Germany', 'state' => 'Berlin', 'city' => 'Berlin'],
        ['country' => 'Portugal', 'state' => 'Lisbon', 'city' => 'Lisbon'],
        ['country' => 'Greece', 'state' => 'Attica', 'city' => 'Athens'],
    ];

    $results = Cache::remember('air_quality_selected_cities', 1800, function () use ($targetCities) {
        $allData = [];

        foreach ($targetCities as $entry) {
            try {
                sleep(1); // Delay to avoid API rate limits

                $data = app(AirQualityService::class)->fetchCityAirQuality(
                    $entry['country'],
                    $entry['state'],
                    $entry['city']
                );

                $pollution = $data['data']['current']['pollution'];
                $utcTime = $pollution['ts'];

                $localTime = \Carbon\Carbon::parse($utcTime)
                    ->timezone('Europe/London') // Default to London time; adjusted below
                    ->setTimezone(new \DateTimeZone(self::getTimezoneByCountry($entry['country'])))
                    ->format('Y-m-d H:i:s');

                $data['data']['current']['pollution']['ts_local'] = $localTime . ' (' . self::getTimezoneByCountry($entry['country']) . ')';
                $allData[] = $data['data'];
            } catch (\Exception $e) {
                \Log::warning("Failed: {$entry['city']}, {$entry['state']} — " . $e->getMessage());
                continue;
            }
        }

        return $allData;
    });

    return response()->json([
        'status' => 'success',
        'cities_air_quality' => $results,
    ]);
}

// Optional helper method inside the controller
private static function getTimezoneByCountry(string $country): string
{
    return match ($country) {
        'France' => 'Europe/Paris',
        'Spain' => 'Europe/Madrid',
        'Italy' => 'Europe/Rome',
        'Turkey' => 'Europe/Istanbul',
        'United Kingdom' => 'Europe/London',
        'Germany' => 'Europe/Berlin',
        'Portugal' => 'Europe/Lisbon',
        'Greece' => 'Europe/Athens',
        default => 'UTC',
    };
}

}
