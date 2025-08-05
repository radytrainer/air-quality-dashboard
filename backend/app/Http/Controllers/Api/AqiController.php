<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AqiController extends Controller
{
    public function global()
    {
        $token = env('WAQI_API_TOKEN');
        $bounds = '-85,-180,85,180';
        $url = "https://api.waqi.info/map/bounds/?latlng=$bounds&token=$token";

        $response = Http::withoutVerifying()->get($url);

        if ($response->successful() && isset($response['status']) && $response['status'] === 'ok') {
            $data = collect($response['data'])->map(function ($station) {
                // Ensure name is present
                $name = $station['station']['name'] ?? 'Unknown';

                // Clean UTF-8 and fallback if still broken
                $safeName = mb_convert_encoding($name, 'UTF-8', 'UTF-8');

                $cityParts = explode(',', $safeName);
                $country = trim(end($cityParts));
                $code = strtolower(substr(preg_replace('/[^a-zA-Z]/', '', $country), 0, 2)) ?: 'xx';

                return [
                    'name' => $safeName,
                    'aqi' => $station['aqi'] ?? 'N/A',
                    'lat' => $station['lat'] ?? null,
                    'lon' => $station['lon'] ?? null,
                    'flag' => "https://flagcdn.com/w160/{$code}.png",
                ];
            });

            // ✅ Encode with JSON_UNESCAPED_UNICODE to avoid breaking UTF-8 characters
            return response()->json([
                'status' => 'ok',
                'data' => $data,
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to fetch AQI from WAQI API.',
        ], 500);
    }
}
