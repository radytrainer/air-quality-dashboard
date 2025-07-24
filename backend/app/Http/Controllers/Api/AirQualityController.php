<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AirQualityController extends Controller
{
    private string $openWeatherKey;
    private string $openAqKey;
    private string $openAqBaseUrl;

    public function __construct()
    {
        $this->openWeatherKey = config('services.openweather.api_key');
        $this->openAqKey = config('services.openaq.api_key');
        $this->openAqBaseUrl = config('services.openaq.base_url', 'https://api.openaq.org/v3');
    }

    /**
     * Get air quality by lat/lon using OpenWeather API
     */
public function getAirQuality(float $lat, float $lon): JsonResponse
{
    $response = Http::get('https://api.openweathermap.org/data/2.5/air_pollution', [
        'lat' => $lat,
        'lon' => $lon,
        'appid' => $this->openWeatherKey,
    ]);

    if (!$response->successful()) {
        return response()->json([
            'error' => 'Failed to fetch air quality data',
            'status' => $response->status(),
            'body' => $response->body(),
        ], $response->status());
    }

    $data = $response->json();

    // Extract AQI (1-5 scale) and components
    $aqi = $data['list'][0]['main']['aqi'] ?? null;
    $components = $data['list'][0]['components'] ?? [];

    return response()->json([
        'aqi' => $aqi,
        'components' => $components,
        'raw' => $data,
    ]);
}


    /**
     * Get air quality data for Phnom Penh using OpenWeather API
     */
    public function getPhnomPenhAirQuality(): JsonResponse
    {
        $lat = 11.5564;
        $lon = 104.9282;

        $response = Http::get('http://api.openweathermap.org/data/2.5/air_pollution', [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => $this->openWeatherKey,
        ]);

        if (!$response->successful()) {
            return response()->json([
                'error' => 'Failed to fetch air quality data',
                'status' => $response->status(),
                'body' => $response->body(),
            ], $response->status());
        }

        $data = $response->json();
        $firstRecord = $data['list'][0] ?? [];

        return response()->json([
            'city' => 'Phnom Penh',
            'coordinates' => compact('lat', 'lon'),
            'data' => $firstRecord,
        ]);
    }

    /**
     * Get list of countries from OpenAQ v3
     */
    public function getCountries(): JsonResponse
    {
        $response = Http::withHeaders([
            'X-API-Key' => $this->openAqKey,
        ])->get("{$this->openAqBaseUrl}/countries");

        if (!$response->successful()) {
            return response()->json([
                'error' => 'Failed to fetch countries',
                'status' => $response->status(),
                'body' => $response->body(),
            ], $response->status());
        }

        return response()->json($response->json()['results']);
    }

    /**
     * Get latest measurements for a country using OpenAQ v3 measurements endpoint
     */
    public function getLatestByCountry(string $countryCode, int $limit = 10): JsonResponse
    {
        $response = Http::withHeaders([
            'X-API-Key' => $this->openAqKey,
        ])->get("{$this->openAqBaseUrl}/measurements", [
            'country' => $countryCode,
            'order_by' => 'datetime',
            'sort' => 'desc',
            'limit' => $limit,
        ]);

        if (!$response->successful()) {
            return response()->json([
                'error' => 'Failed to fetch latest measurements',
                'status' => $response->status(),
                'body' => $response->body(),
            ], $response->status());
        }

        return response()->json($response->json()['results']);
    }

    /**
     * Get global recent air quality data (example limited by country)
     */
    public function getGlobalAirQuality(int $limit = 10): JsonResponse
    {
        // Fetch latest measurements globally, no country filter
        $response = Http::withHeaders([
            'X-API-Key' => $this->openAqKey,
        ])->get("{$this->openAqBaseUrl}/measurements", [
            'order_by' => 'datetime',
            'sort' => 'desc',
            'limit' => $limit,
        ]);

        if (!$response->successful()) {
            return response()->json([
                'error' => 'Failed to fetch global air quality data',
                'status' => $response->status(),
                'body' => $response->body(),
            ], $response->status());
        }

        return response()->json($response->json()['results']);
    }

    /**
     * Get counts of PM10 and PM2.5 measurements per country (limit to 20 countries)
     */
public function getAllCountriesMeasurementCounts(): JsonResponse
{
    set_time_limit(300); // allow longer execution time

    // Fetch locations (which include country codes)
    $locationsResponse = Http::withHeaders([
        'X-API-Key' => $this->openAqKey,
    ])->get("{$this->openAqBaseUrl}/locations", [
        'limit' => 1000, // fetch enough locations to get countries
    ]);

    if (!$locationsResponse->ok()) {
        return response()->json([
            'error' => 'Failed to fetch locations to extract countries',
            'status' => $locationsResponse->status(),
            'body' => $locationsResponse->body(),
        ], 500);
    }

    $locations = $locationsResponse->json()['results'];

    // Extract unique country codes from locations
    $countries = collect($locations)
        ->pluck('country')
        ->unique()
        ->values()
        ->all();

    $result = [];

    // Optionally limit the number of countries to avoid timeout
    $limitedCountries = array_slice($countries, 0, 20);

    foreach ($limitedCountries as $code) {
        $pm10Count = $this->getMeasurementCount('pm10', $code);
        $pm25Count = $this->getMeasurementCount('pm25', $code);

        $result[] = [
            'country' => $code,
            'pm10_count' => $pm10Count,
            'pm25_count' => $pm25Count,
        ];
    }

    // Get worldwide counts (no country filter)
    $worldwidePm10 = $this->getMeasurementCount('pm10');
    $worldwidePm25 = $this->getMeasurementCount('pm25');

    return response()->json([
        'worldwide' => [
            'pm10_count' => $worldwidePm10,
            'pm25_count' => $worldwidePm25,
        ],
        'countries' => $result,
    ]);
}


    /**
     * Helper: fetch measurement count for parameter and optional country
     */
    private function getMeasurementCount(string $parameter, ?string $country = null): int
    {
        $query = [
            'parameter' => $parameter,
            'limit' => 1, // minimal fetch to get meta.found count
        ];

        if ($country !== null) {
            $query['country'] = $country;
        }

        $response = Http::withHeaders([
            'X-API-Key' => $this->openAqKey,
        ])->get("{$this->openAqBaseUrl}/measurements", $query);

        if ($response->ok()) {
            $data = $response->json();
            return $data['meta']['found'] ?? 0;
        }

        Log::error('OpenAQ API request failed', [
            'parameter' => $parameter,
            'country' => $country,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return 0;
    }

    /**
     * Get locations from OpenAQ (limit 100)
     */
    public function getLocations(): JsonResponse
{
    $response = Http::get("https://api.openaq.org/v3/locations", [
        'limit' => 100,
    ]);

    if (!$response->successful()) {
        return response()->json([
            'error' => 'Failed to fetch locations',
            'status' => $response->status(),
            'body' => $response->body(),
        ], $response->status());
    }

    return response()->json($response->json()['results']);
}


public function getIqAirData(Request $request): JsonResponse
{
    $lat = $request->query('lat');
    $lon = $request->query('lon');

    if (!$lat || !$lon) {
        return response()->json(['error' => 'Missing lat or lon'], 400);
    }

    $apiKey = config('services.iqair.api_key');
    $baseUrl = config('services.iqair.base_url');

    $response = Http::get("$baseUrl/nearest_city", [
        'lat' => $lat,
        'lon' => $lon,
        'key' => $apiKey,
    ]);

    if (!$response->successful()) {
        return response()->json([
            'error' => 'Failed to fetch IQAir data',
            'status' => $response->status(),
            'body' => $response->body(),
        ], $response->status());
    }

    return response()->json($response->json());
}
}
