<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlertMessageHistory;
use App\Models\AlertSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminHealthAlertController extends Controller
{
    // Fetch dashboard metadata
    public function fetchMeta()
    {
        $lastFetchTime = Cache::get('last_api_fetch_time', 'Never');

        $cacheKeys = [
            'aqi_map_data' => Cache::has('aqi_map_data') ? 'active' : 'missing',
            'data_countries' => Cache::has('data_countries') ? 'active' : 'missing',
        ];

        $logPath = storage_path('logs/laravel.log');
        $logLines = [];

        if (file_exists($logPath)) {
            $lines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $lastLines = array_slice($lines, -10);

            foreach ($lastLines as $line) {
                $clean = preg_replace('/\s+/', ' ', trim($line));
                if (!empty($clean)) {
                    $logLines[] = $clean;
                }
            }
        }

        return response()->json([
            'last_fetch_time' => $lastFetchTime,
            'cache_keys' => $cacheKeys,
            'logs' => $logLines,
        ]);
    }

    // Fetch and cache AQI data (manual or automatic)
    public function fetchAndCacheAqiData()
    {
        // Example: replace with your real API call
        $aqiData = [
            'pm25' => 35,
            'pm10' => 50,
            'co'   => 0.8,
            'no2'  => 25
        ];

        // Cache individual AQI values
        Cache::put('aqi_pm25', $aqiData['pm25'], now()->addMinutes(30));
        Cache::put('aqi_pm10', $aqiData['pm10'], now()->addMinutes(30));
        Cache::put('aqi_co', $aqiData['co'], now()->addMinutes(30));
        Cache::put('aqi_no2', $aqiData['no2'], now()->addMinutes(30));

        // Cache map & countries data
        Cache::put('aqi_map_data', $aqiData, now()->addMinutes(30));
        Cache::put('data_countries', ['Cambodia', 'Vietnam', 'Thailand'], now()->addMinutes(30));

        // Update last fetch timestamp
        Cache::put('last_api_fetch_time', now()->toDateTimeString());

        return response()->json(['message' => 'AQI data cached successfully']);
    }

    // Return current AQI from cache
    public function currentAqi()
    {
        $aqi = [
            'pm25' => Cache::get('aqi_pm25', 0),
            'pm10' => Cache::get('aqi_pm10', 0),
            'co'   => Cache::get('aqi_co', 0),
            'no2'  => Cache::get('aqi_no2', 0),
        ];

        return response()->json($aqi);
    }

    // Alert configuration methods
    public function getAlertConfig()
    {
        $config = AlertSetting::first() ?: AlertSetting::create();
        return response()->json($config);
    }

    public function updateAlertConfig(Request $request)
    {
        $config = AlertSetting::first() ?: AlertSetting::create();
        $config->update([
            'pm25_threshold' => $request->pm25_threshold,
            'pm10_threshold' => $request->pm10_threshold,
            'co_threshold' => $request->co_threshold,
            'no2_threshold' => $request->no2_threshold,
            'enable_alert' => $request->enable_alert,
            'alert_message' => $request->alert_message,
        ]);

        return response()->json(['message' => 'Settings updated successfully', 'data' => $config]);
    }

    // Alert message history
    public function alertHistory()
    {
        $history = AlertMessageHistory::orderBy('created_at', 'desc')->limit(50)->get();
        return response()->json($history);
    }

    public function storeAlertHistory(Request $request)
    {
        $request->validate(['message' => 'required|string']);
        $msg = AlertMessageHistory::create(['message' => $request->message]);
        return response()->json(['message' => 'Saved', 'data' => $msg]);
    }
}
