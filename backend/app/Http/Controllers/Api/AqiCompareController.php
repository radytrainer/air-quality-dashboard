<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AqiCompareController extends Controller
{
    public function compare(Request $request)
    {
        $city1 = $request->query('city1');
        $city2 = $request->query('city2');

        if (!$city1 || !$city2) {
            return response()->json(['error' => 'Both city1 and city2 are required'], 400);
        }

        if (strtolower($city1) === strtolower($city2)) {
            return response()->json(['error' => 'Please choose two different cities'], 422);
        }

        $token = config('services.waqi.token');

        $city1Response = Http::get("https://api.waqi.info/feed/" . urlencode($city1) . "/?token=$token");
        $city2Response = Http::get("https://api.waqi.info/feed/" . urlencode($city2) . "/?token=$token");

        if ($city1Response->failed() || $city2Response->failed()) {
            return response()->json(['error' => 'Failed to fetch data from WAQI'], 500);
        }

        $data1 = $city1Response->json();
        $data2 = $city2Response->json();

        if ($data1['status'] !== 'ok' || $data2['status'] !== 'ok') {
            return response()->json(['error' => 'One or both cities not found or invalid'], 404);
        }

        return response()->json([
            'city1' => $this->extractCityData($data1),
            'city2' => $this->extractCityData($data2),
        ]);
    }

    private function extractCityData($apiData)
    {
        $aqi = $apiData['data']['aqi'] ?? null;

        return [
            'name' => $apiData['data']['city']['name'],
            'aqi' => $aqi,
            'level' => $this->getAqiLevel($aqi),
            'dominant_pollutant' => $apiData['data']['dominentpol'] ?? 'N/A',
            'iaqi' => $apiData['data']['iaqi'] ?? [],
            'timezone' => $apiData['data']['time']['tz'] ?? 'UTC',
        ];
    }

    private function getAqiLevel($aqi)
    {
        if ($aqi <= 50) return 'Good';
        if ($aqi <= 100) return 'Moderate';
        if ($aqi <= 150) return 'Unhealthy for Sensitive Groups';
        if ($aqi <= 200) return 'Unhealthy';
        if ($aqi <= 300) return 'Very Unhealthy';
        return 'Hazardous';
    }
    public function allCitiesFromWaqi()
{
    set_time_limit(0); // Prevent timeout for large data fetch

    $token = config('services.waqi.token'); // From your .env
    $bounds = '-85,-180,85,180'; // Full world bounds

    $url = "https://api.waqi.info/map/bounds/?latlng=$bounds&token=$token";

    $response = Http::get($url);

    if ($response->failed()) {
        return response()->json(['error' => 'Failed to fetch city data from WAQI'], 500);
    }

    $data = $response->json();

    if ($data['status'] !== 'ok') {
        return response()->json(['error' => 'Invalid response from WAQI API'], 500);
    }

    // Filter and format station data (which includes city names)
    $cities = collect($data['data'])
        ->map(function ($station) {
            return [
                'name' => $station['station']['name'],
                'lat' => $station['lat'],
                'lon' => $station['lon'],
                'aqi' => $station['aqi']
            ];
        })
        ->unique('name') // Remove duplicates by name
        ->values();

    return response()->json($cities);
}
}