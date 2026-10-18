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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'crs' => [
        'url' => env('CRS_API_URL', 'https://competitiverelocationcrm.com'),
        'token' => env('CRS_API_TOKEN'),
        'timeout' => (int) env('CRS_API_TIMEOUT', 3),
        'cache_seconds' => (int) env('CRS_RATES_CACHE', 3600),
    ],

    'photon' => [
        'url' => env('PHOTON_URL', 'https://photon.komoot.io'),
        'timeout' => (int) env('PHOTON_TIMEOUT', 4),
        'cache_seconds' => (int) env('PHOTON_CACHE', 3600),
    ],

];
