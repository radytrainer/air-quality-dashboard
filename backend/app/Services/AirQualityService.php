<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class AirQualityService
{
    protected ?string $token;

    public function __construct()
    {
        $this->token = config('services.waqi.token');
        // Do NOT throw here!
    }

    protected function getToken(): string
    {
        if (!$this->token) {
            throw new \RuntimeException('WAQI_API_TOKEN is not set in config/services.php or .env');
        }
        return $this->token;
    }

    public function fetchAllGlobalStations(): array
    {
        $response = Http::get('https://api.waqi.info/map/bounds/', [
            'token' => $this->getToken(),
            'latlng' => '-90,-180,90,180',
        ]);

        if ($response->failed()) {
            throw new \RuntimeException('Failed to fetch AQI data from WAQI API.');
        }

        $json = $response->json();

        if (isset($json['status']) && $json['status'] !== 'ok') {
            $message = $json['data'] ?? 'Unknown error';
            throw new \RuntimeException('WAQI API error: ' . $message);
        }

        return $json['data'] ?? [];
    }
}
