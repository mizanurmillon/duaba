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

    'stuart' => [
        'client_id' => env('STUART_CLIENT_ID'),
        'client_secret' => env('STUART_CLIENT_SECRET'),
        // 'base_uri' => env('STUART_BASE_URI', 'https://api.stuart.com/'),
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'google'   => [
        'client_id' => '188277747840-e3f3v9cd1l11uhifhm5m6mp63no8kplb.apps.googleusercontent.com',
    ],


    'apple' => [
        'client_id'     => env('APPLE_CLIENT_ID'),     // e.g., com.moto.master
        'client_secret' => env('APPLE_CLIENT_SECRET'),
        'redirect'      => env('APPLE_REDIRECT_URI'),
        'team_id'       => env('APPLE_TEAM_ID'),
        'key_id'        => env('APPLE_KEY_ID'),
        'private_key'   => env('APPLE_PRIVATE_KEY_PATH'), // Path to .p8 file
    ],

];
