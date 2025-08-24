<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LocationSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->query('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'status' => 'ok',
                'data' => []
            ]);
        }

        // Get fresh data from PollutionDataController
        $controller = new PollutionDataController();
        $aqiResponse = $controller->getAqiData();
        $aqiData = $aqiResponse->getData(true);

        if ($aqiData['status'] !== 'ok' || !isset($aqiData['data'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to fetch location data',
                'debug' => $aqiData
            ], 500);
        }

        $locations = collect($aqiData['data'])
            ->filter(function ($location) use ($query) {
                $name = $location['name'] ?? '';
                return stripos($name, $query) !== false;
            })
            ->take(15)
            ->map(function ($location) {
                $parts = explode(',', $location['name']);
                $city = trim($parts[0]);
                $country = count($parts) > 1 ? trim(end($parts)) : 'Unknown';
                
                return [
                    'name' => $city,
                    'full_name' => $location['name'],
                    'country' => $country,
                    'lat' => $location['lat'],
                    'lon' => $location['lon'],
                    'aqi' => $location['aqi'],
                ];
            })
            ->values();

        return response()->json([
            'status' => 'ok',
            'data' => $locations,
            'total_available' => count($aqiData['data'])
        ]);
    }
}
