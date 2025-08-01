<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class OpenAQController extends Controller
{
    public function fetchWorldwideData()
    {
        $response = Http::withHeaders([
            'X-API-Key' => env('OPENAQ_API_KEY'),
        ])->get('https://api.openaq.org/v3/measurements', [
            'limit' => 10,
            'page' => 1,
            'sort' => 'desc',
            'country' => 'US',     // Required - worldwide is not allowed anymore
            'parameter' => 'pm25'
        ]);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json([
            'status' => $response->status(),
            'error' => $response->body()
        ], $response->status());
    }
}
