<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;

class AqiCompareController extends Controller
{
    public function getGlobalAQI()
    {
        set_time_limit(0);

        $token = env('WAQI_API_TOKEN');
        $bounds = '-85,-180,85,180';
        $mapUrl = "https://api.waqi.info/map/bounds/?latlng=$bounds&token=$token";

        // Get stations list
        $stations = Http::get($mapUrl)->json('data') ?? [];

        // Limit to first 120 stations to avoid too many requests
        $stations = array_slice($stations, 0, 120);

        // Remove duplicates
        $uniqueStations = [];
        foreach ($stations as $station) {
            $cityKey = strtolower($station['station']['name']);
            if (!isset($uniqueStations[$cityKey])) {
                $uniqueStations[$cityKey] = $station;
            }
        }

        // Parallel requests
        $responses = Http::pool(function (Pool $pool) use ($uniqueStations, $token) {
            $reqs = [];
            foreach ($uniqueStations as $station) {
                $lat = $station['lat'];
                $lon = $station['lon'];
                $reqs[] = $pool->get("https://api.waqi.info/feed/geo:$lat;$lon/?token=$token");
            }
            return $reqs;
        });

        $result = [];
        foreach ($responses as $response) {
            if (!$response->ok()) continue;

            $data = $response->json();
            if (($data['status'] ?? null) !== 'ok') continue;

            $iaqi = $data['data']['iaqi'] ?? [];

            $result[] = [
                
                'city'        => $data['data']['city']['name'] ?? null,
                'country'     => $data['data']['city']['name'] ?? 'Unknown',
                'temperature' => $iaqi['t']['v'] ?? null,
                'humidity'    => $iaqi['h']['v'] ?? null,
                'wind'        => $iaqi['w']['v'] ?? null,
                'uv'          => $iaqi['uv']['v'] ?? null,
            ];
        }

        return response()->json([
            'status' => 'ok',
            'count'  => count($result),
            'data'   => $result,
        ]);
    }
}
