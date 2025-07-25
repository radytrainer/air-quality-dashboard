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

class AqiOceaniaController extends Controller
{
    public function getAllOceaniaCitiesAirQuality(): JsonResponse
{
    $targetCities = [
        ['country' => 'Australia', 'state' => 'New South Wales', 'city' => 'Sydney'],
        ['country' => 'New Zealand', 'state' => 'Auckland', 'city' => 'Auckland'],
        ['country' => 'Fiji', 'state' => 'Central Division', 'city' => 'Suva'],
        ['country' => 'Papua New Guinea', 'state' => 'National Capital District', 'city' => 'Port Moresby'],
        ['country' => 'Samoa', 'state' => 'Tuamasaga', 'city' => 'Apia'],
        ['country' => 'Tonga', 'state' => 'Tongatapu', 'city' => "Nuku'alofa"],
        ['country' => 'Solomon Islands', 'state' => 'Honiara City', 'city' => 'Honiara'],
        ['country' => 'Vanuatu', 'state' => 'Shefa Province', 'city' => 'Port Vila'],
        ['country' => 'Micronesia', 'state' => 'Pohnpei', 'city' => 'Palikir'],
        ['country' => 'Palau', 'state' => 'Melekeok', 'city' => 'Ngerulmud'],
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
        'Australia' => 'Australia/Sydney',
        'New Zealand' => 'Pacific/Auckland',
        'Fiji' => 'Pacific/Fiji',
        'Papua New Guinea' => 'Pacific/Port_Moresby',
        'Samoa' => 'Pacific/Apia',
        'Tonga' => 'Pacific/Tongatapu',
        'Solomon Islands' => 'Pacific/Guadalcanal',
        'Vanuatu' => 'Pacific/Efate',
        'Micronesia' => 'Pacific/Pohnpei',
        'Palau' => 'Pacific/Palau',
        default => 'UTC',
    };
}

}
