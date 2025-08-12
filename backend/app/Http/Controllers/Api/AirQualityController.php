<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class AirQualityController extends Controller
{
public function getPhnomPenhAirQualityOpenWeather()
{
    $apiKey = env('OPENWEATHER_API_KEY');
    $lat = 11.562108;
    $lon = 104.888535;
    $url = "http://api.openweathermap.org/data/2.5/air_pollution?lat={$lat}&lon={$lon}&appid={$apiKey}";

    try {
        $response = \Illuminate\Support\Facades\Http::get($url);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json([
            'error' => 'Failed to fetch data from OpenWeather',
            'details' => $response->json()
        ], $response->status());

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Request failed',
            'message' => $e->getMessage()
        ], 500);
    }
}
}
