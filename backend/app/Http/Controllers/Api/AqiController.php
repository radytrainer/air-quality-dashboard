<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AqiController extends Controller
{
    public function getGlobalAqi()
    {
        $cacheKey = 'global_aqi_data';

        $data = Cache::remember($cacheKey, 300, function () {
            $token = '9c81a4f2fcf022539c917fdefba185ff9369865d';
            $bounds = '-85,-180,85,180';
            $url = "https://api.waqi.info/map/bounds/?latlng={$bounds}&token={$token}";

            $certPath = storage_path('certs/cacert.pem');

            $response = Http::withOptions([
                'verify' => $certPath,
            ])->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        });

        if (!$data || $data['status'] !== 'ok') {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch AQI data from upstream',
            ], 500);
        }

        return response()->json([
            'status' => 'ok',
            'data' => $data['data'],
        ]);
    }
}