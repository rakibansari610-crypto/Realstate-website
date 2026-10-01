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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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
    'reelly' => [
        'base_url'    => env('REELLY_BASE_URL', 'https://api-reelly.up.railway.app'),
        'api_key'     => env('REELLY_API_KEY'),
        'environment' => env('REELLY_ENVIRONMENT', 'test'),
        'timeout'     => (int) env('REELLY_TIMEOUT', 20),
    ],
    'leadrat' => [
    'base_url'   => env('LEADRAT_BASE_URL', 'https://connect.leadrat.com'),
    'tenant'     => env('LEADRAT_TENANT'),
    'api_key'    => env('LEADRAT_API_KEY'),
    'secret_key' => env('LEADRAT_SECRET_KEY'),
],

];
