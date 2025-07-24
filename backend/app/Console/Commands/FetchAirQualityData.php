<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AirQualityService;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class FetchAirQualityData extends Command
{
    protected $signature = 'fetch:airquality';
    protected $description = 'Fetch and cache air quality data for all cities in Cambodia';

    protected AirQualityService $airQualityService;

    public function __construct(AirQualityService $airQualityService)
    {
        parent::__construct();
        $this->airQualityService = $airQualityService;
    }

    public function handle(): void
    {
        $this->info('Fetching air quality data for Cambodia...');

        $country = 'Cambodia';
        $results = [];

        try {
            $states = $this->airQualityService->fetchStates($country);

            foreach ($states as $state) {
                $stateName = $state['state'];
                sleep(1); // avoid rate limit
                $cities = $this->airQualityService->fetchCities($country, $stateName);

                foreach ($cities as $cityData) {
                    $cityName = $cityData['city'];
                    sleep(1); // avoid rate limit

                    try {
                        $data = $this->airQualityService->fetchCityAirQuality($country, $stateName, $cityName);
                        $pollution = $data['data']['current']['pollution'];

                        $utcTime = $pollution['ts'] ?? null;
                        $localTime = $utcTime
                            ? Carbon::parse($utcTime)->timezone('Asia/Phnom_Penh')->format('Y-m-d H:i:s')
                            : null;

                        $data['data']['current']['pollution']['ts_local'] = $localTime . ' (Asia/Phnom_Penh)';

                        $results[] = $data['data'];
                    } catch (\Exception $e) {
                        $this->warn("City failed: $cityName — " . $e->getMessage());
                        continue;
                    }
                }
            }

            Cache::put('air_quality_all_cities', $results, now()->addMinutes(15));
            $this->info('✅ All cities cached successfully.');
        } catch (\Exception $e) {
            $this->error('❌ Failed to fetch data: ' . $e->getMessage());
        }
    }
}
