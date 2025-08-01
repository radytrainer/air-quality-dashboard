<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AirQualityController extends Controller
{
    private $token;

    public function __construct()
    {
        $this->token = config('services.waqi.token');
        if (!$this->token) {
            Log::error('WAQI_TOKEN is missing or not loaded from config.');
        }
    }

    public function getMapBounds(Request $request)
    {
        if (!$this->token) {
            return response()->json(['error' => 'WAQI token is missing'], 500);
        }

        $pollutant = $request->query('pollutant', 'aqi');
        $validPollutants = ['aqi', 'pm25', 'pm10', 'o3', 'no2', 'so2', 'co'];

        if (!in_array($pollutant, $validPollutants)) {
            return response()->json(['error' => 'Invalid pollutant parameter'], 400);
        }

        $bounds = $request->query('bounds', '-85,-180,85,180');

        // Call WAQI API for stations in bounds
        $response = Http::get("https://api.waqi.info/map/bounds/", [
            'latlng' => $bounds,
            'token' => $this->token,
        ]);

        if (!$response->successful()) {
            Log::error('WAQI API request failed', [
                'url' => $response->effectiveUri(),
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return response()->json(['error' => 'WAQI API request failed'], 500);
        }

        $data = $response->json();

        if ($data['status'] !== 'ok' || !isset($data['data'])) {
            Log::error('WAQI API response error', ['response' => $data]);
            return response()->json(['error' => 'WAQI API response error'], 500);
        }

        // Filter and map stations to only include the selected pollutant value
        $stations = [];
        foreach ($data['data'] as $station) {
            // Skip if coordinates or station name missing
            if (!isset($station['lat'], $station['lon'], $station['station']['name'])) {
                continue;
            }

            // Get pollutant value
            $value = null;

            if ($pollutant === 'aqi') {
                $value = $station['aqi'] ?? null;
            } else {
                // Other pollutants are inside 'iaqi' object, e.g. iaqi.pm25.v
                $keyMap = [
                    'pm25' => 'pm25',
                    'pm10' => 'pm10',
                    'o3' => 'o3',
                    'no2' => 'no2',
                    'so2' => 'so2',
                    'co' => 'co',
                ];

                $iaqiKey = $keyMap[$pollutant] ?? null;

                if ($iaqiKey && isset($station['iaqi'][$iaqiKey]['v'])) {
                    $value = $station['iaqi'][$iaqiKey]['v'];
                }
            }

            // Only include stations with numeric pollutant value
            if ($value === null || !is_numeric($value)) {
                continue;
            }

            $stations[] = [
                'uid' => $station['uid'] ?? null,
                'station' => $station['station']['name'],
                'lat' => $station['lat'],
                'lon' => $station['lon'],
                'value' => $value,
                'pollutant' => $pollutant,
            ];
        }

        return response()->json($stations);
    }

    public function getStationDetails($uid)
    {
        $url = "https://api.waqi.info/feed/@{$uid}/?token={$this->token}";
        $response = Http::get($url);

        if ($response->successful()) {
            $data = $response->json();
            if ($data['status'] === 'ok') {
                return collect($data['data']['iaqi'] ?? [])->mapWithKeys(function ($item, $key) {
                    return [$key => $item['v']];
                })->all();
            }
        }

        return [];
    }

    public function getColor($type, $value)
    {
        if (!is_numeric($value)) return '#999999';

        switch ($type) {
            case 'aqi':
                return match (true) {
                    $value <= 50 => '#009966',
                    $value <= 100 => '#ffde33',
                    $value <= 150 => '#ff9933',
                    $value <= 200 => '#cc0033',
                    $value <= 300 => '#660099',
                    default => '#7e0023',
                };
            case 'pm25':
                return match (true) {
                    $value <= 12 => '#009966',
                    $value <= 35.4 => '#ffde33',
                    $value <= 55.4 => '#ff9933',
                    $value <= 150.4 => '#cc0033',
                    $value <= 250.4 => '#660099',
                    default => '#7e0023',
                };
            case 'pm10':
                return match (true) {
                    $value <= 54 => '#009966',
                    $value <= 154 => '#ffde33',
                    $value <= 254 => '#ff9933',
                    $value <= 354 => '#cc0033',
                    $value <= 424 => '#660099',
                    default => '#7e0023',
                };
            default:
                return '#999999';
        }
    }

    public function getHealthNote($type, $value)
    {
        if (!is_numeric($value)) return 'Data unavailable';

        switch ($type) {
            case 'aqi':
                return match (true) {
                    $value <= 50 => 'Good',
                    $value <= 100 => 'Moderate',
                    $value <= 150 => 'Unhealthy for Sensitive Groups',
                    $value <= 200 => 'Unhealthy',
                    $value <= 300 => 'Very Unhealthy',
                    default => 'Hazardous',
                };
            case 'pm25':
                return match (true) {
                    $value <= 12 => 'Good',
                    $value <= 35.4 => 'Moderate',
                    $value <= 55.4 => 'Unhealthy for Sensitive Groups',
                    $value <= 150.4 => 'Unhealthy',
                    $value <= 250.4 => 'Very Unhealthy',
                    default => 'Hazardous',
                };
            case 'pm10':
                return match (true) {
                    $value <= 54 => 'Good',
                    $value <= 154 => 'Moderate',
                    $value <= 254 => 'Unhealthy for Sensitive Groups',
                    $value <= 354 => 'Unhealthy',
                    $value <= 424 => 'Very Unhealthy',
                    default => 'Hazardous',
                };
            default:
                return 'Unknown';
        }
    }
}
