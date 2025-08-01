<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\AirQualityService;
use Carbon\Carbon;

class AqiAsiaController extends Controller
{
    public function getAllAsiaCitiesAirQuality(): JsonResponse
    {
        $targetCities = [
            ['country' => 'China', 'state' => 'Beijing', 'city' => 'Beijing'],
            ['country' => 'India', 'state' => 'Delhi', 'city' => 'New Delhi'],
            ['country' => 'Japan', 'state' => 'Tokyo', 'city' => 'Tokyo'],
            ['country' => 'South Korea', 'state' => 'Seoul', 'city' => 'Seoul'],
            ['country' => 'Indonesia', 'state' => 'Jakarta', 'city' => 'Jakarta'],
            ['country' => 'Thailand', 'state' => 'Bangkok', 'city' => 'Bangkok'],
            ['country' => 'Malaysia', 'state' => 'Kuala Lumpur', 'city' => 'Kuala Lumpur'],
            ['country' => 'Vietnam', 'state' => 'Hanoi', 'city' => 'Hanoi'],
            ['country' => 'Philippines', 'state' => 'Metro Manila', 'city' => 'Manila'],
            ['country' => 'Pakistan', 'state' => 'Punjab', 'city' => 'Lahore'],
            ['country' => 'Bangladesh', 'state' => 'Dhaka', 'city' => 'Dhaka'],
            ['country' => 'Cambodia', 'state' => 'Phnom Penh', 'city' => 'Phnom Penh'],
        ];

        $results = Cache::remember('air_quality_selected_cities', 1800, function () use ($targetCities) {
            $allData = [];

            foreach ($targetCities as $entry) {
                try {
                    sleep(1); // Avoid API rate limits

                    $data = app(AirQualityService::class)->fetchCityAirQuality(
                        $entry['country'],
                        $entry['state'],
                        $entry['city']
                    );

                    // If no valid response
                    if (!$data || !isset($data['data']['current']['pollution'])) {
                        Log::warning("No pollution data: {$entry['city']}, {$entry['state']} ({$entry['country']})");
                        continue;
                    }

                    $pollution = $data['data']['current']['pollution'];
                    $utcTime = $pollution['ts'] ?? null;

                    if (!$utcTime) {
                        Log::warning("Missing timestamp: {$entry['city']}, {$entry['state']} ({$entry['country']})");
                        continue;
                    }

                    $timezone = self::getTimezoneByCountry($entry['country']);

                    $localTime = Carbon::parse($utcTime)
                        ->setTimezone(new \DateTimeZone($timezone))
                        ->format('Y-m-d H:i:s');

                    $data['data']['current']['pollution']['ts_local'] = $localTime . ' (' . $timezone . ')';
                    $allData[] = $data['data'];
                } catch (\Exception $e) {
                    Log::warning("Error fetching: {$entry['city']}, {$entry['state']} ({$entry['country']}) — " . $e->getMessage());
                    continue;
                }
            }

            return $allData;
        });

        return response()->json([
            'status' => 'success',
            'cities_air_quality' => $results,
        ]);
    }

    private static function getTimezoneByCountry(string $country): string
    {
        return match ($country) {
            'China' => 'Asia/Shanghai',
            'India' => 'Asia/Kolkata',
            'Japan' => 'Asia/Tokyo',
            'South Korea' => 'Asia/Seoul',
            'Indonesia' => 'Asia/Jakarta',
            'Thailand' => 'Asia/Bangkok',
            'Malaysia' => 'Asia/Kuala_Lumpur',
            'Vietnam' => 'Asia/Ho_Chi_Minh',
            'Philippines' => 'Asia/Manila',
            'Pakistan' => 'Asia/Karachi',
            'Bangladesh' => 'Asia/Dhaka',
            'Cambodia' => 'Asia/Phnom_Penh',
            default => 'UTC',
        };
    }
}
