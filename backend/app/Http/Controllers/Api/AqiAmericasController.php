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

class AqiAmericasController extends Controller
{
    public function getAlliAmericasCitiesAirQuality(): JsonResponse
{
    $targetCities = [
        ['country' => 'Canada', 'state' => 'Ontario', 'city' => 'Toronto'],
        ['country' => 'United States', 'state' => 'California', 'city' => 'Los Angeles'],
        ['country' => 'Brazil', 'state' => 'São Paulo', 'city' => 'São Paulo'],
        ['country' => 'Argentina', 'state' => 'Buenos Aires', 'city' => 'Buenos Aires'],
        ['country' => 'Peru', 'state' => 'Lima', 'city' => 'Lima'],
        ['country' => 'Colombia', 'state' => 'Bogotá', 'city' => 'Bogotá'],
        ['country' => 'Mexico', 'state' => 'Mexico City', 'city' => 'Mexico City'],
        ['country' => 'Paraguay', 'state' => 'Asunción', 'city' => 'Asunción'],
        ['country' => 'Panama', 'state' => 'Panama', 'city' => 'Panama City'],
        ['country' => 'Venezuela', 'state' => 'Distrito Capital', 'city' => 'Caracas'],
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
        'Canada' => 'America/Toronto',
        'United States' => 'America/Los_Angeles',
        'Brazil' => 'America/Sao_Paulo',
        'Argentina' => 'America/Argentina/Buenos_Aires',
        'Peru' => 'America/Lima',
        'Colombia' => 'America/Bogota',
        'Mexico' => 'America/Mexico_City',
        'Paraguay' => 'America/Asuncion',
        'Panama' => 'America/Panama',
        'Venezuela' => 'America/Caracas',
        default => 'UTC',
    };
}

}
