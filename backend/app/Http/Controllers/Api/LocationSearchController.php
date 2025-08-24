<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LocationSearchController extends Controller
{
    /**
     * Search locations by city name, country name, or location name
     * Supports dynamic search for cities, states, and countries
     */
    public function search(Request $request)
    {
        $query = $request->query('q', '');
        
        // Minimum 2 characters for search
        if (strlen($query) < 2) {
            return response()->json([
                'status' => 'ok',
                'data' => []
            ]);
        }

        // Get fresh AQI data from existing controller
        $controller = new PollutionDataController();
        $aqiResponse = $controller->getAqiData();
        $aqiData = $aqiResponse->getData(true);

        if ($aqiData['status'] !== 'ok' || !isset($aqiData['data'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to fetch location data'
            ], 500);
        }

        // Process and structure all location data
        $allLocations = collect($aqiData['data'])
            ->map(function ($location) {
                $parts = explode(',', $location['name']);
                $city = trim($parts[0]);
                $country = count($parts) > 1 ? trim(end($parts)) : 'Unknown';
                
                // Clean country name (remove parentheses content)
                $cleanCountry = preg_replace('/\s*\([^)]*\)/', '', $country);
                
                return [
                    'name' => $city,
                    'full_name' => $location['name'],
                    'country' => $cleanCountry,
                    'lat' => $location['lat'],
                    'lon' => $location['lon'],
                    'aqi' => $location['aqi'],
                ];
            });

        // Filter locations that match search query
        $locations = $allLocations
            ->filter(function ($location) use ($query) {
                // Search in city name, country name, or full location name
                return stripos($location['name'], $query) !== false || 
                       stripos($location['country'], $query) !== false || 
                       stripos($location['full_name'], $query) !== false;
            })
            ->take(20) // Limit to 20 results
            ->map(function ($location) use ($query) {
                // Determine search match type for better UI display
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
