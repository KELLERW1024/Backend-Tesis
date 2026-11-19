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
    'openai' => [
        'key' => env('OPENAI_API_KEY'),
    ],
    'mercadopago' => [
        'access_token' => env('MP_ACCESS_TOKEN'),
        'public_key' => env('MP_PUBLIC_KEY'),
    ],
    'replicate' => [
        'token' => env('REPLICATE_API_TOKEN'),
    ],

    // Búsqueda de fuentes para las referencias ("Agregar fuente" → "Buscar")
    'sources' => [
        'daily_limit' => (int) env('SOURCES_DAILY_LIMIT', 20),
        'search_model' => env('SOURCES_SEARCH_MODEL', 'gpt-5-mini'),
        'official_domains' => [
            'gob.pe',
            'inei.gob.pe',
            'bcrp.gob.pe',
            'sunat.gob.pe',
            'produce.gob.pe',
            'minsa.gob.pe',
            'dge.gob.pe',
            'datosabiertos.gob.pe',
            'mincetur.gob.pe',
            'midagri.gob.pe',
        ],
    ],
];
