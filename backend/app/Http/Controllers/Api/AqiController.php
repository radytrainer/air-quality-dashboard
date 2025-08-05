<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AqiController extends Controller
{
    public function global()
    {
        set_time_limit(0); // Avoid timeout for long requests

        $token = env('WAQI_API_TOKEN');
        $bounds = '-85,-180,85,180'; // Covers entire globe
        $url = "https://api.waqi.info/map/bounds/?latlng=$bounds&token=$token";

        $response = Http::withoutVerifying()->get($url);

        if ($response->successful() && $response['status'] === 'ok') {
            $stations = collect($response['data']); // Don't limit to 50

            $data = $stations->map(function ($station) use ($token) {
                $name = $station['station']['name'] ?? 'Unknown';
                $safeName = mb_convert_encoding($name, 'UTF-8', 'UTF-8');

                $cityParts = explode(',', $safeName);
                $country = trim(end($cityParts));
                $code = strtolower(substr(preg_replace('/[^a-zA-Z]/', '', $country), 0, 2)) ?: 'xx';

                // Default pollutants
                $pollutants = [
                    'pm25' => null,
                    'pm10' => null,
                    'o3' => null,
                    'no2' => null,
                    'so2' => null,
                    'co' => null,
                ];

                $uid = $station['uid'] ?? null;

                if ($uid) {
                    $detailUrl = "https://api.waqi.info/feed/@$uid/?token=$token";

                    try {
                        $detailResponse = Http::withoutVerifying()->timeout(5)->get($detailUrl);

                        if ($detailResponse->successful() && $detailResponse['status'] === 'ok') {
                            $iaqi = $detailResponse['data']['iaqi'] ?? [];

                            foreach ($pollutants as $key => $_) {
                                if (isset($iaqi[$key]['v'])) {
                                    $pollutants[$key] = $iaqi[$key]['v'];
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error("Failed to fetch detail for UID $uid: " . $e->getMessage());
                    }
                }

                return [
                    'name' => $safeName,
                    'country' => $country,
                    'aqi' => $station['aqi'] ?? 'N/A',
                    'lat' => $station['lat'] ?? null,
                    'lon' => $station['lon'] ?? null,
                    'flag' => "https://flagcdn.com/w160/{$code}.png",
                    'pm25' => $pollutants['pm25'],
                    'pm10' => $pollutants['pm10'],
                    'o3'   => $pollutants['o3'],
                    'no2'  => $pollutants['no2'],
                    'so2'  => $pollutants['so2'],
                    'co'   => $pollutants['co'],
                ];
            });

            return response()->json([
                'status' => 'ok',
                'data' => $data->values(),
                'total' => $data->count()
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to fetch AQI data from WAQI API.',
        ], 500);
    }
}
