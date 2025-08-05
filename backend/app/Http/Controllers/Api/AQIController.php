<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AirQualityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AQIController extends Controller
{
    protected AirQualityService $aqiService;

    public function __construct(AirQualityService $aqiService)
    {
        $this->aqiService = $aqiService;
    }

    public function getGlobalAQI(Request $request): JsonResponse
    {
        try {
            $data = $this->aqiService->fetchAllGlobalStations();

            // Optional: limit number of stations returned
            $limitedData = collect($data)->take(100)->values();

            return response()->json([
                'status' => 'ok',
                'count' => $limitedData->count(),
                'data' => $limitedData,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
