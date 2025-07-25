<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],
 'openweather' => [
    'api_key' => 'e206621a38fb37832cc3cb734fb477ae',
],
'openaq' => [
    'base_url' => 'https://api.openaq.org',
    'api_key' => '8570a81988caeddc953c8f0520e01574154d07440e2af72da906871f613545ff', 
],
'iqair' => [
    'api_key' => '2acd56eb-8186-40aa-b9e9-29b2c4e87f47',
    'base_url' => 'https://api.airvisual.com/v2',
],
    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

];
