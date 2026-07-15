<?php

return [
    'name' => env('PWA_NAME', env('SEO_SITE_NAME', 'FURUGI')),
    'short_name' => env('PWA_SHORT_NAME', 'FURUGI'),
    'description' => env('PWA_DESCRIPTION', env('SEO_DESCRIPTION', '古着販売向け在庫管理システム')),
    'theme_color' => env('PWA_THEME_COLOR', '#0f172a'),
    'background_color' => env('PWA_BACKGROUND_COLOR', '#f8fafc'),
    'display' => env('PWA_DISPLAY', 'standalone'),
    'start_url' => env('PWA_START_URL', '/'),
    'scope' => env('PWA_SCOPE', '/'),
    'cache_name' => env('PWA_CACHE_NAME', 'furugi-pwa-v1'),
];
