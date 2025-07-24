<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\AirQualityService;
use Carbon\Carbon;

class AirQualityController extends Controller
{
    private string $openWeatherKey;
    private string $openAqKey;
    private string $openAqBaseUrl;
    protected AirQualityService $airQualityService;

    public function __construct(AirQualityService $airQualityService)
    {
        $this->openWeatherKey = config('services.openweather.api_key');
        $this->openAqKey = config('services.openaq.api_key');
        $this->openAqBaseUrl = config('services.openaq.base_url', 'https://api.openaq.org/v3');
        $this->airQualityService = $airQualityService;
    }

    public function getAirQuality(float $lat, float $lon): JsonResponse
    {
        $response = Http::get('https://api.openweathermap.org/data/2.5/air_pollution', [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => $this->openWeatherKey,
        ]);

        if (!$response->successful()) {
            Log::error('OpenWeather API failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'error' => 'Failed to fetch air quality data',
                'status' => $response->status(),
                'body' => $response->body(),
            ], $response->status());
        }

        $data = $response->json();
        $aqi = $data['list'][0]['main']['aqi'] ?? null;
        $components = $data['list'][0]['components'] ?? [];

        return response()->json([
            'aqi' => $aqi,
            'components' => $components,
            'raw' => $data,
        ]);
    }

    public function getPhnomPenhAirQuality(): JsonResponse
    {
        return $this->getAirQuality(11.5564, 104.9282);
    }

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

    public function getGlobalAirQuality(int $limit = 10): JsonResponse
    {
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

    public function getAllCountriesMeasurementCounts(): JsonResponse
    {
        set_time_limit(300);

        $locationsResponse = Http::withHeaders([
            'X-API-Key' => $this->openAqKey,
        ])->get("{$this->openAqBaseUrl}/locations", [
            'limit' => 1000,
        ]);

        if (!$locationsResponse->ok()) {
            return response()->json([
                'error' => 'Failed to fetch locations to extract countries',
                'status' => $locationsResponse->status(),
                'body' => $locationsResponse->body(),
            ], 500);
        }

        $locations = $locationsResponse->json()['results'];
        $countries = collect($locations)->pluck('country')->unique()->values()->all();
        $limitedCountries = array_slice($countries, 0, 20);
        $result = [];

        foreach ($limitedCountries as $code) {
            $pm10Count = $this->getMeasurementCount('pm10', $code);
            $pm25Count = $this->getMeasurementCount('pm25', $code);

            $result[] = [
                'country' => $code,
                'pm10_count' => $pm10Count,
                'pm25_count' => $pm25Count,
            ];
        }

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

    private function getMeasurementCount(string $parameter, ?string $country = null): int
    {
        $query = [
            'parameter' => $parameter,
            'limit' => 1,
        ];

        if ($country) {
            $query['country'] = $country;
        }

        $response = Http::withHeaders([
            'X-API-Key' => $this->openAqKey,
        ])->get("{$this->openAqBaseUrl}/measurements", $query);

        if ($response->ok()) {
            return $response->json()['meta']['found'] ?? 0;
        }

        Log::error('OpenAQ API failed', [
            'parameter' => $parameter,
            'country' => $country,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return 0;
    }

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
public function getAllCitiesAirQuality(): JsonResponse
{
    $country = 'Cambodia';
    $targetCities = [
        ['state' => 'Phnom Penh', 'city' => 'Phnom Penh'],
        ['state' => 'Siem Reap', 'city' => 'Siem Reap'],
    ];

    $results = Cache::remember('air_quality_selected_cities', 1800, function () use ($targetCities, $country) {
        $allData = [];

        foreach ($targetCities as $entry) {
            try {
                sleep(1); // Add delay between calls

                $data = app(AirQualityService::class)->fetchCityAirQuality(
                    $country,
                    $entry['state'],
                    $entry['city']
                );

                $pollution = $data['data']['current']['pollution'];
                $utcTime = $pollution['ts'];

                $localTime = \Carbon\Carbon::parse($utcTime)
                    ->timezone('Asia/Phnom_Penh')
                    ->format('Y-m-d H:i:s');

                $data['data']['current']['pollution']['ts_local'] = $localTime . ' (Asia/Phnom_Penh)';
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
        'country' => $country,
        'cities_air_quality' => $results,
    ]);
}



}
