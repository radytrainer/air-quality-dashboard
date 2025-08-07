<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlertMessageHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\AlertSetting;

class AdminHealthAlertController extends Controller
{
    public function fetchMeta()
    {
        $lastFetchTime = Cache::get('last_api_fetch_time', 'Never');
        $cacheKeys = [
            'aqi_map_data' => Cache::has('aqi_map_data') ? 'active' : 'missing',
            'data_countries' => Cache::has('data_countries') ? 'active' : 'missing',
        ];

        $logPath = storage_path('logs/laravel.log');
        $logLines = file_exists($logPath) ? array_slice(file($logPath), -10) : [];

        return response()->json([
            'last_fetch_time' => $lastFetchTime,
            'cache_keys' => $cacheKeys,
            'logs' => $logLines,
        ]);
    }

    public function getAlertConfig()
    {
        $config = AlertSetting::first();
        if (!$config) {
            $config = AlertSetting::create(); // default
        }

        return response()->json($config);
    }

    public function updateAlertConfig(Request $request)
    {
        $config = AlertSetting::first();
        if (!$config) {
            $config = AlertSetting::create();
        }

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

     public function alertHistory()
    {
        $history = AlertMessageHistory::orderBy('created_at', 'desc')->limit(50)->get();
        return response()->json($history);
    }

    public function storeAlertHistory(Request $request)
    {
        $request->validate(['message' => 'required|string']);
        $msg = AlertMessageHistory::create([
            'message' => $request->message,
        ]);
        return response()->json(['message' => 'Saved', 'data' => $msg]);
    }

    public function currentAqi()
    {
        // Pull latest AQI data (mock or from cache)
        $aqi = [
            'pm25' => Cache::get('aqi_pm25', 0),
            'pm10' => Cache::get('aqi_pm10', 0),
            'co' => Cache::get('aqi_co', 0),
            'no2' => Cache::get('aqi_no2', 0),
        ];
        return response()->json($aqi);
    }
}
