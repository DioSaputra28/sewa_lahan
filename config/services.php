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

    'pakasir' => [
        'project_slug' => env('PAKASIR_PROJECT_SLUG'),
        'api_key' => env('PAKASIR_API_KEY'),
        'sandbox' => env('PAKASIR_SANDBOX', true),
        'base_url' => env('PAKASIR_BASE_URL', 'https://app.pakasir.com'),
        'methods' => [
            'qris' => ['label' => 'QRIS', 'group' => 'QRIS'],
            'cimb_niaga_va' => ['label' => 'CIMB Niaga VA', 'group' => 'Virtual Account'],
            'bni_va' => ['label' => 'BNI VA', 'group' => 'Virtual Account'],
            'sampoerna_va' => ['label' => 'Sampoerna VA', 'group' => 'Virtual Account'],
            'bnc_va' => ['label' => 'BNC VA', 'group' => 'Virtual Account'],
            'maybank_va' => ['label' => 'Maybank VA', 'group' => 'Virtual Account'],
            'permata_va' => ['label' => 'Permata VA', 'group' => 'Virtual Account'],
            'atm_bersama_va' => ['label' => 'ATM Bersama VA', 'group' => 'Virtual Account'],
            'artha_graha_va' => ['label' => 'Artha Graha VA', 'group' => 'Virtual Account'],
            'bri_va' => ['label' => 'BRI VA', 'group' => 'Virtual Account'],
            'paypal' => ['label' => 'Paypal', 'group' => 'Paypal'],
        ],
    ],

];
