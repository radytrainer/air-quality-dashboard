<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiAnalyController extends Controller
{
    public function fetchFilteredData(Request $request)
    {
        $token = env('WAQI_API_TOKEN');
        $country = strtolower($request->query('country'));
        $pollutant = strtolower($request->query('pollutant')); // Optional single pollutant to focus on

        // Step 1: Fetch stations globally
        $response = Http::get('https://api.waqi.info/map/bounds/', [
            'latlng' => '-85,-180,85,180',
            'token' => $token,
        ]);

        if (!$response->successful() || $response['status'] !== 'ok') {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch data from WAQI API.',
            ], 500);
        }

        $stations = collect($response['data']);

        // Step 2: Loosely filter by country if provided
        if ($country) {
            $stations = $stations->filter(function ($station) use ($country) {
                $stationName = strtolower($station['station']['name'] ?? '');
                return str_contains($stationName, $country);
            });
        }

        // Step 3: Define the pollutant keys you want to extract
        $pollutantsToFetch = ['pm25', 'pm10', 'o3', 'no2', 'so2', 'co'];

        // Step 4: Map data
        $result = $stations->map(function ($station) use ($pollutant, $pollutantsToFetch) {
            $iaqi = $station['iaqi'] ?? [];
            $extractedPollutants = [];

            foreach ($pollutantsToFetch as $key) {
                $extractedPollutants[$key] = $iaqi[$key]['v'] ?? null;
            }

            return [
                'name' => $station['station']['name'] ?? '',
                'lat' => $station['lat'] ?? null,
                'lon' => $station['lon'] ?? null,
                'aqi' => $station['aqi'] ?? null,
                'pollutants' => $extractedPollutants,
                'selected_value' => $pollutant && isset($iaqi[$pollutant]['v']) 
                    ? $iaqi[$pollutant]['v'] 
                    : null,
            ];
        })->filter(function ($station) use ($pollutant) {
            // Only return stations with a value for the selected pollutant
            return !$pollutant || ($pollutant && $station['selected_value'] !== null);
        })->values();

        return response()->json([
            'status' => 'ok',
            'data' => $result,
        ]);
    }
}
