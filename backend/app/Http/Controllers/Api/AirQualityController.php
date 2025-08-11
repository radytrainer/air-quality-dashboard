<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;

class AirQualityController extends Controller
{
    private string $openAqKey;
    private string $openAqBaseUrl;

    public function __construct()
    {
        $this->openAqKey = config('services.openaq.api_key') ?? env('OPENAQ_API_KEY', '');
        $this->openAqBaseUrl = config('services.openaq.base_url', 'https://api.openaq.org/v3');
    }

    /**
     * Fetch cities in Cambodia from OpenAQ
     */
    public function getCambodiaCities(): JsonResponse
    {
        $response = Http::withHeaders([
            'X-API-Key' => $this->openAqKey,
        ])->get("{$this->openAqBaseUrl}/cities", [
            'country' => 'KH', // Cambodia ISO code
            'limit' => 50,
        ]);

        if (!$response->successful()) {
            return response()->json([
                'error' => 'Failed to fetch cities',
                'status' => $response->status(),
                'body' => $response->body(),
            ], $response->status());
        }

        return response()->json($response->json()['results']);
    }

    /**
     * Fetch latest air quality data for Phnom Penh by country and filter locally
     */
    public function getPhnomPenhAirQuality(): JsonResponse
    {
        $response = Http::withHeaders([
            'X-API-Key' => $this->openAqKey,
        ])->get("{$this->openAqBaseUrl}/latest", [
            'country' => 'KH',
            'limit' => 100,
        ]);

        if (!$response->successful()) {
            return response()->json([
                'error' => 'Failed to fetch air quality data for Cambodia',
                'status' => $response->status(),
                'body' => $response->body(),
            ], $response->status());
        }

        $results = collect($response->json()['results']);

        // Filter results where city or location contains 'Phnom Penh' (case-insensitive)
        $phnomPenhData = $results->filter(function ($item) {
            $city = $item['city'] ?? '';
            $location = $item['location'] ?? '';
            return stripos($city, 'phnom penh') !== false
                || stripos($location, 'phnom penh') !== false;
        })->values();

        if ($phnomPenhData->isEmpty()) {
            return response()->json([
                'error' => 'No air quality data found for Phnom Penh',
                'status' => 404,
            ], 404);
        }

        return response()->json($phnomPenhData);
    }
}
