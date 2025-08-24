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

        $allLocations = collect($aqiData['data'])
            ->map(function ($location) {
                $parts = explode(',', $location['name']);
                $city = trim($parts[0]);
                $country = count($parts) > 1 ? trim(end($parts)) : 'Unknown';
                
                // Extract clean country name (remove extra info in parentheses)
                $cleanCountry = preg_replace('/\s*\([^)]*\)/', '', $country);
                
                return [
                    'name' => $city,
                    'full_name' => $location['name'],
                    'country' => $cleanCountry,
                    'lat' => $location['lat'],
                    'lon' => $location['lon'],
                    'aqi' => $location['aqi'],
                    'raw_name' => $location['name'],
                ];
            });

        // Filter locations that match the query
        $locations = $allLocations
            ->filter(function ($location) use ($query) {
                // Search in city name, country name, or full name
                return stripos($location['name'], $query) !== false || 
                       stripos($location['country'], $query) !== false || 
                       stripos($location['full_name'], $query) !== false;
            })
            ->take(20)
            ->map(function ($location) use ($query) {
                // Add search type information
                $type = 'general';
                if (stripos($location['name'], $query) === 0) {
                    $type = 'city';
                } elseif (stripos($location['country'], $query) === 0) {
                    $type = 'country';
                } elseif (stripos($location['name'], $query) !== false) {
                    $type = 'city_partial';
                } elseif (stripos($location['country'], $query) !== false) {
                    $type = 'country_partial';
                }
                
                $location['type'] = $type;
                unset($location['raw_name']);
                return $location;
            })
            ->values();

        return response()->json([
            'status' => 'ok',
            'data' => $locations,
            'total_available' => count($aqiData['data'])
        ]);
    }


}
