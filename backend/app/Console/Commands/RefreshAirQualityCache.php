<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RefreshAirQualityCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-air-quality-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Refreshing air quality cache...");

    app(AirQualityController::class)->getAllCitiesAirQuality();
    
    $this->info("✅ Refreshed successfully.");
    }
}
