<?php

return [
    'page_views' => [
        'enabled' => env('ANALYTICS_PAGE_VIEWS_ENABLED', true),
        'debug' => env('ANALYTICS_PAGE_VIEWS_DEBUG', false),
        'cookie_name' => 'pv_sid',
        'cookie_minutes' => 60 * 24 * 365,
        'excluded_environments' => ['testing'],
        'tracked_routes' => [
            'home' => 'Home',
            'about' => 'About',
            'contact' => 'Contact',
            'lahan.index' => 'Lahan Listing',
            'lahan.show' => 'Detail Lahan',
        ],
        'bot_user_agent_patterns' => [
            'bot',
            'crawler',
            'spider',
            'slurp',
            'bingpreview',
            'facebookexternalhit',
            'whatsapp',
            'telegrambot',
            'discordbot',
        ],
    ],
];
