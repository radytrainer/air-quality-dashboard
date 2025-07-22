<?php
// app/Transformers/AirQualityTransformer.php
namespace App\Transformers;

class AirQualityTransformer
{
    public static function transform(array $data): array
    {
        return [
            'location' => $data['location']['name'] ?? null,
            'country' => $data['location']['country'] ?? null,
            'coordinates' => [
                'latitude' => $data['location']['coordinates']['latitude'] ?? null,
                'longitude' => $data['location']['coordinates']['longitude'] ?? null,
            ],
            'airQualityIndex' => (float) ($data['measurements']['aqi'] ?? 0),
            'pollutants' => [
                'pm2_5' => (float) ($data['measurements']['pm2_5'] ?? null),
                'pm10' => (float) ($data['measurements']['pm10'] ?? null),
                'o3' => (float) ($data['measurements']['o3'] ?? null),
            ],
            'lastUpdated' => now()->toISOString(),
        ];
    }
}
