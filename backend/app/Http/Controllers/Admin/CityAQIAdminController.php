<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AirQualityService;
use Illuminate\Http\Request;

class CityAQIAdminController extends Controller
{
    protected AirQualityService $aqiService;

    public function __construct(AirQualityService $aqiService)
    {
        $this->aqiService = $aqiService;
    }

    public function index(Request $request)
{
    $pollutant = $request->get('pollutant', 'aqi');
    $search = $request->get('search');
    $limit = (int) $request->get('limit', 605);

    $stations = collect($this->aqiService->fetchAllGlobalStations());

    if ($search) {
        $stations = $stations->filter(function ($s) use ($search) {
            return str_contains(strtolower($s['station']['name'] ?? ''), strtolower($search));
        });
    }

    $results = $stations->map(function ($s) use ($pollutant) {
        $value = null;
        if ($pollutant === 'aqi') {
            $value = is_numeric($s['aqi']) ? (int) $s['aqi'] : null;
        } elseif (isset($s['iaqi'][$pollutant]['v']) && is_numeric($s['iaqi'][$pollutant]['v'])) {
            $value = (float) $s['iaqi'][$pollutant]['v'];
        }

        return [
            'name' => $s['station']['name'] ?? 'Unknown',
            'lat' => $s['lat'] ?? null,
            'lon' => $s['lon'] ?? null,
            'value' => $value,
            'pollutant' => $pollutant,
            'level' => $value !== null ? $this->getAQILevel($value) : 'Unknown',
        ];
    })->filter(fn ($s) => $s['value'] !== null)
      ->values()
      ->take($limit);

    return response()->json([
        'status' => 'ok',
        'data' => $results,
    ]);
}

private function getAQILevel($value)
{
    if ($value <= 50) return 'Good';
    if ($value <= 100) return 'Moderate';
    if ($value <= 150) return 'Unhealthy for Sensitive Groups';
    if ($value <= 200) return 'Unhealthy';
    if ($value <= 300) return 'Very Unhealthy';
    return 'Hazardous';
}

}
