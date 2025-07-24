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

class AqiAfricanController extends Controller
{
    public function getAllAfricanCitiesAirQuality(): JsonResponse
{
    $targetCities = [
        ['country' => 'Nigeria', 'state' => 'Lagos', 'city' => 'Lagos'],
        ['country' => 'South Africa', 'state' => 'Gauteng', 'city' => 'Johannesburg'],
        ['country' => 'Egypt', 'state' => 'Cairo', 'city' => 'Cairo'],
        ['country' => 'Kenya', 'state' => 'Nairobi County', 'city' => 'Nairobi'],
        ['country' => 'Ethiopia', 'state' => 'Addis Ababa', 'city' => 'Addis Ababa'],
        ['country' => 'Ghana', 'state' => 'Greater Accra', 'city' => 'Accra'],
        ['country' => 'Morocco', 'state' => 'Rabat-Salé-Kénitra', 'city' => 'Rabat'],
        ['country' => 'Algeria', 'state' => 'Algiers', 'city' => 'Algiers'],
        ['country' => 'Tanzania', 'state' => 'Dar es Salaam', 'city' => 'Dar es Salaam'],
        ['country' => 'Uganda', 'state' => 'Central', 'city' => 'Kampala'],
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

                $timezone = self::getTimezoneByCountry($entry['country']);

                $localTime = \Carbon\Carbon::parse($utcTime)
                    ->setTimezone(new \DateTimeZone($timezone))
                    ->format('Y-m-d H:i:s');

                $data['data']['current']['pollution']['ts_local'] = $localTime . ' (' . $timezone . ')';
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

private static function getTimezoneByCountry(string $country): string
{
    return match ($country) {
        'Nigeria' => 'Africa/Lagos',
        'South Africa' => 'Africa/Johannesburg',
        'Egypt' => 'Africa/Cairo',
        'Kenya' => 'Africa/Nairobi',
        'Ethiopia' => 'Africa/Addis_Ababa',
        'Ghana' => 'Africa/Accra',
        'Morocco' => 'Africa/Casablanca',
        'Algeria' => 'Africa/Algiers',
        'Tanzania' => 'Africa/Dar_es_Salaam',
        'Uganda' => 'Africa/Kampala',
        default => 'UTC',
    };
}

}
