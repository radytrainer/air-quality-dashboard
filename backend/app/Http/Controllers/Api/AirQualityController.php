<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class AirQualityController extends Controller
{
    public function getPhnomPenhAirQualityOpenWeather()
    {
        $apiKey = env('OPENWEATHER_API_KEY');
        $lat = 11.562108;
        $lon = 104.888535;

        // API URLs
        $airQualityUrl = "http://api.openweathermap.org/data/2.5/air_pollution?lat={$lat}&lon={$lon}&appid={$apiKey}";
        $weatherUrl    = "http://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$apiKey}&units=metric";

        try {
            // Fetch Air Pollution data
            $aqResponse = Http::get($airQualityUrl);
            // Fetch Weather data
            $weatherResponse = Http::get($weatherUrl);

            if ($aqResponse->failed() || $weatherResponse->failed()) {
                return response()->json([
                    'error' => 'Failed to fetch data from OpenWeather',
                    'details' => [
                        'air_quality' => $aqResponse->json(),
                        'weather' => $weatherResponse->json()
                    ]
                ], 500);
            }

            $aqData = $aqResponse->json();
            $weatherData = $weatherResponse->json();

            // Extract AQI (1–5 scale) and pollutants
            $aqiIndex = $aqData['list'][0]['main']['aqi'] ?? null;
            $components = $aqData['list'][0]['components'] ?? [];

            // Map AQI index to readable status
            $aqiStatus = match ($aqiIndex) {
                1 => 'Good',
                2 => 'Fair',
                3 => 'Moderate',
                4 => 'Poor',
                5 => 'Very Poor',
                default => 'Unknown',
            };

            // Extract weather details
            $temp = $weatherData['main']['temp'] ?? null;
            $humidity = $weatherData['main']['humidity'] ?? null;
            $pressure = $weatherData['main']['pressure'] ?? null;
            $windSpeed = $weatherData['wind']['speed'] ?? null;

            // Return merged data
            return response()->json([
                "AQI" => $aqiIndex, // OpenWeather AQI scale (1–5)
                "Status" => $aqiStatus,
                "PM2_5" => $components['pm2_5'] ?? null,
                "PM10" => $components['pm10'] ?? null,
                "NO2" => $components['no2'] ?? null,
                "CO" => $components['co'] ?? null,
                "O3" => $components['o3'] ?? null,
                "Temp_C" => $temp,
                "Humidity_percent" => $humidity,
                "Pressure_hPa" => $pressure,
                "Wind_m_s" => $windSpeed
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Request failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
