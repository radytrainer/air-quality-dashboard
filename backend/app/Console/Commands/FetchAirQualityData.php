<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AirQualityService;
use Illuminate\Support\Facades\Cache;

class FetchAirQualityData extends Command
{
    protected $signature = 'fetch:airquality';
    protected $description = 'Fetch and cache global air quality data';

    protected AirQualityService $airQualityService;

    public function __construct(AirQualityService $airQualityService)
    {
        parent::__construct();
        $this->airQualityService = $airQualityService;
    }

    public function handle(): void
    {
        $this->info('🌍 Fetching global AQI station data...');

        try {
            $stations = $this->airQualityService->fetchAllGlobalStations();
            $results = [];

            foreach ($stations as $station) {
                try {
                    sleep(1); // WAQI rate limit safety
                    $details = $this->airQualityService->fetchStationDetails($station['uid']);

                    $cityInfo = $details['data']['city'] ?? [];
                    $country = $cityInfo['country'] ?? 'Unknown';
                    $flagCode = strtolower($country);

                    $details['data']['timestamp_local'] = now()->toDateTimeString();
                    $details['data']['country'] = $country;
                    $details['data']['flag'] = "https://flagcdn.com/w160/{$flagCode}.png";

                    $results[] = $details['data'];
                } catch (\Throwable $e) {
                    $this->warn("❌ UID {$station['uid']} failed: " . $e->getMessage());
                    continue;
                }
            }

            Cache::put('air_quality_all_countries', $results, now()->addMinutes(15));
            $this->info('✅ Global AQI data cached successfully.');
        } catch (\Throwable $e) {
            $this->error('❌ Fetch failed: ' . $e->getMessage());
        }
    }
}
