<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

class AirQualityController extends Controller
{
    /**
     * Get air quality data from OpenWeather API by latitude and longitude.
     *
     * @param  float  $lat
     * @param  float  $lon
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAirQuality($lat, $lon): JsonResponse
    {
        $apiKey = config('services.openweather.api_key');

        $response = Http::get('http://api.openweathermap.org/data/2.5/air_pollution', [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => $apiKey,
        ]);

        if (!$response->successful()) {
            return response()->json([
                'error' => 'Failed to fetch air quality data',
                'status' => $response->status(),
                'body' => $response->body(),
            ], $response->status());
        }

        return response()->json(['data' => $response->json()]);
    }
    public function getPhnomPenhAirQuality(): JsonResponse
{
    $apiKey = config('services.openweather.api_key');

    $lat = 11.5564; // Phnom Penh latitude
    $lon = 104.9282; // Phnom Penh longitude

    $response = Http::get('http://api.openweathermap.org/data/2.5/air_pollution', [
        'lat' => $lat,
        'lon' => $lon,
        'appid' => $apiKey,
    ]);

    if (!$response->successful()) {
        return response()->json([
            'error' => 'Failed to fetch air quality data',
            'status' => $response->status(),
            'body' => $response->body(),
        ], $response->status());
    }

    return response()->json([
        'city' => 'Phnom Penh',
        'coordinates' => compact('lat', 'lon'),
        'data' => $response['list'][0] ?? [],
    ]);
}
 public function getCountries(): JsonResponse
    {
        $baseUrl = config('services.openaq.base_url');
        $apiKey = config('services.openaq.api_key');

        $response = Http::withHeaders([
            'X-API-Key' => $apiKey
        ])->get("$baseUrl/countries");

        if (!$response->successful()) {
            return response()->json([
                'error' => 'Failed to fetch countries',
                'status' => $response->status(),
                'body' => $response->body()
            ], $response->status());
        }

        return response()->json($response->json()['results']);
    }

    public function getLatestByCountry(string $code): JsonResponse
    {
        $baseUrl = config('services.openaq.base_url');
        $apiKey = config('services.openaq.api_key');

        $response = Http::withHeaders([
            'X-API-Key' => $apiKey
        ])->get("$baseUrl/latest", [
            'country' => $code,
            'limit' => 100,
        ]);

        if (!$response->successful()) {
            return response()->json([
                'error' => 'Failed to fetch latest data',
                'status' => $response->status(),
                'body' => $response->body()
            ], $response->status());
        }

        return response()->json($response->json()['results']);
    }


}
