<?php

namespace App\Http\Controllers;

use App\Models\AirQualityData;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AirQualityController extends Controller
{
    protected $openAQClient;
    
    public function __construct()
    {
        $this->openAQClient = new Client([
            'base_uri' => 'https://api.openaq.org/v2/',
            'headers' => ['Accept' => 'application/json'],
            'timeout' => 15,
        ]);
    }
    
    /**
     * Fetch worldwide air quality data
     */
    public function fetchWorldwideData(Request $request)
    {
        $cacheKey = 'air_quality_worldwide_' . md5(json_encode($request->all()));
        
        $data = Cache::remember($cacheKey, now()->addHours(1), function() use ($request) {
            try {
                $response = $this->openAQClient->get('latest', [
                    'query' => [
                        'limit' => $request->limit ?? 100,
                        'page' => $request->page ?? 1,
                        'sort' => 'desc',
                        'order_by' => 'lastUpdated',
                    ]
                ]);
                
                $result = json_decode($response->getBody(), true);
                
                // Cache individual measurements
                $this->cacheMeasurements($result['results']);
                
                return $result['results'];
            } catch (\Exception $e) {
                // Fallback to cached data if API fails
                return AirQualityData::query()
                    ->orderBy('last_updated', 'desc')
                    ->limit($request->limit ?? 100)
                    ->get()
                    ->map(function($item) {
                        return [
                            'location' => $item->location,
                            'city' => $item->city,
                            'country' => $item->country,
                            'parameter' => $item->parameter,
                            'value' => $item->value,
                            'unit' => $item->unit,
                            'lastUpdated' => $item->last_updated,
                            'source' => 'cache'
                        ];
                    })
                    ->toArray();
            }
        });
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    
    /**
     * Fetch data by location
     */
    public function fetchByLocation(Request $request)
    {
        $request->validate([
            'location' => 'required|string'
        ]);
        
        $cacheKey = 'air_quality_location_' . md5($request->location);
        
        $data = Cache::remember($cacheKey, now()->addMinutes(30), function() use ($request) {
            try {
                $response = $this->openAQClient->get('latest/' . urlencode($request->location));
                
                $result = json_decode($response->getBody(), true);
                
                // Cache individual measurements
                $this->cacheMeasurements($result['results']);
                
                return $result['results'];
            } catch (\Exception $e) {
                // Fallback to cached data if API fails
                return AirQualityData::query()
                    ->where('location', 'like', '%' . $request->location . '%')
                    ->orWhere('city', 'like', '%' . $request->location . '%')
                    ->orWhere('country', 'like', '%' . $request->location . '%')
                    ->orderBy('last_updated', 'desc')
                    ->limit(100)
                    ->get()
                    ->map(function($item) {
                        return [
                            'location' => $item->location,
                            'city' => $item->city,
                            'country' => $item->country,
                            'parameter' => $item->parameter,
                            'value' => $item->value,
                            'unit' => $item->unit,
                            'lastUpdated' => $item->last_updated,
                            'source' => 'cache'
                        ];
                    })
                    ->toArray();
            }
        });
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    
    /**
     * Get available parameters
     */
    public function getParameters()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'pm25' => 'PM2.5 - Fine particulate matter',
                'pm10' => 'PM10 - Coarse particulate matter',
                'no2' => 'Nitrogen Dioxide',
                'so2' => 'Sulfur Dioxide',
                'o3' => 'Ozone',
                'co' => 'Carbon Monoxide'
            ]
        ]);
    }
    
    /**
     * Get countries list
     */
    public function getCountries()
    {
        $cacheKey = 'air_quality_countries';
        
        $data = Cache::remember($cacheKey, now()->addDays(1), function() {
            try {
                $response = $this->openAQClient->get('countries', [
                    'query' => ['limit' => 200]
                ]);
                
                $result = json_decode($response->getBody(), true);
                
                return collect($result['results'])
                    ->pluck('name', 'code')
                    ->toArray();
            } catch (\Exception $e) {
                // Fallback to distinct countries from cached data
                return AirQualityData::query()
                    ->distinct()
                    ->pluck('country', 'country')
                    ->toArray();
            }
        });
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    
    /**
     * Cache measurements in database
     */
    protected function cacheMeasurements(array $measurements)
    {
        foreach ($measurements as $measurement) {
            AirQualityData::updateOrCreate(
                [
                    'location_id' => $measurement['location'],
                    'parameter' => $measurement['parameter'],
                    'last_updated' => $measurement['lastUpdated'],
                ],
                [
                    'location' => $measurement['location'],
                    'city' => $measurement['city'] ?? null,
                    'country' => $measurement['country'],
                    'value' => $measurement['value'],
                    'unit' => $measurement['unit'],
                    'raw_data' => json_encode($measurement),
                ]
            );
        }
    }
}