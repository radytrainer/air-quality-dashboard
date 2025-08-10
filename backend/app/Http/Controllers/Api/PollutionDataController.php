<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Client\Pool;

class PollutionDataController extends Controller
{
    public function getAqiData()
    {
        // Cache for 10 minutes to reduce WAQI API calls and avoid rate limits
        return Cache::remember('waqi_data', 600, function () {
            ini_set('max_execution_time', 300); // 5 minutes max execution

            $token = env('WAQI_API_TOKEN');
            $bounds = '-85,-180,85,180';
            $mapUrl = "https://api.waqi.info/map/bounds/?latlng=$bounds&token=$token";

            $mapResponse = Http::withoutVerifying()->get($mapUrl);

            if (!$mapResponse->successful() || $mapResponse['status'] !== 'ok') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to load station list',
                ], 500);
            }

            $stations = collect($mapResponse['data'])
                ->filter(fn($s) => isset($s['uid']))
                ->take(120);

            // Build URLs to fetch station details
            $urls = $stations->map(fn($station) => "https://api.waqi.info/feed/@{$station['uid']}/?token=$token");

            // Make parallel HTTP requests with SSL verification disabled per request
            $responses = Http::pool(fn (Pool $pool) =>
                $urls->map(fn ($url) => $pool->withOptions(['verify' => false])->timeout(10)->get($url))->all()
            );

            $results = [];

            foreach ($responses as $response) {
                if ($response->successful() && $response['status'] === 'ok') {
                    $data = $response['data'];
                    $iaqi = $data['iaqi'] ?? [];

                    $name = $data['city']['name'] ?? 'Unknown';
                    $safeName = mb_convert_encoding($name, 'UTF-8', 'UTF-8');
                    $cityParts = explode(',', $safeName);
                    $country = trim(end($cityParts));
                    $code = strtolower(substr(preg_replace('/[^a-zA-Z]/', '', $country), 0, 2)) ?: 'xx';

                    $results[] = [
                        'name' => $safeName,
                        'lat' => $data['city']['geo'][0] ?? null,
                        'lon' => $data['city']['geo'][1] ?? null,
                        'aqi' => $data['aqi'] ?? 'N/A',
                        'pm10' => $iaqi['pm10']['v'] ?? null,
                        'pm25' => $iaqi['pm25']['v'] ?? null,
                        'co' => $iaqi['co']['v'] ?? null,
                        'no2' => $iaqi['no2']['v'] ?? null,
                        'so2' => $iaqi['so2']['v'] ?? null,
                        'o3' => $iaqi['o3']['v'] ?? null,
                        'flag' => "https://flagcdn.com/w160/{$code}.png",
                    ];
                }
            }

            return response()->json([
                'status' => 'ok',
                'data' => $results,
            ]);
        });
    }
}
