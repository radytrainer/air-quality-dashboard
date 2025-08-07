<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class PollutionDataController extends Controller
{
    public function getAqiData()
{
    ini_set('max_execution_time', 180); // ⏱ Extend time if needed

    $token = env('WAQI_API_TOKEN');
    $bounds = '-85,-180,85,180';
    $url = "https://api.waqi.info/map/bounds/?latlng=$bounds&token=$token";

    $response = Http::withoutVerifying()->get($url);

    if ($response->successful() && $response['status'] === 'ok') {
        $stations = collect($response['data'])
            ->filter(fn($s) => isset($s['uid']))
            ->take(40); // 🔽 Reduce for speed

        $detailedData = [];

        foreach ($stations as $station) {
            $uid = $station['uid'];
            $stationUrl = "https://api.waqi.info/feed/@$uid/?token=$token";

            try {
                $stationResponse = Http::withoutVerifying()->timeout(5)->get($stationUrl);
            } catch (\Exception $e) {
                continue; // 🔁 Skip timeout or failed
            }

            if (
                $stationResponse->successful()
                && $stationResponse['status'] === 'ok'
                && isset($stationResponse['data'])
            ) {
                $info = $stationResponse['data'];
                $iaqi = $info['iaqi'] ?? [];

                $name = $info['city']['name'] ?? 'Unknown';
                $safeName = mb_convert_encoding($name, 'UTF-8', 'UTF-8');

                $cityParts = explode(',', $safeName);
                $country = trim(end($cityParts));
                $code = strtolower(substr(preg_replace('/[^a-zA-Z]/', '', $country), 0, 2)) ?: 'xx';

                $detailedData[] = [
                    'name' => $safeName,
                    'lat' => $info['city']['geo'][0] ?? null,
                    'lon' => $info['city']['geo'][1] ?? null,
                    'aqi' => $info['aqi'] ?? 'N/A',
                    'pm10' => $iaqi['pm10']['v'] ?? null,
                    'pm25' => $iaqi['pm25']['v'] ?? null,
                    'o3' => $iaqi['o3']['v'] ?? null,
                    'flag' => "https://flagcdn.com/w160/{$code}.png",
                ];
            }

            usleep(150000); // 150ms delay between requests
        }

        return response()->json([
            'status' => 'ok',
            'data' => $detailedData,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    return response()->json([
        'status' => 'error',
        'message' => 'Failed to fetch AQI from WAQI API.',
    ], 500);
}

}
