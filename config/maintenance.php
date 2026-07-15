<?php

return [
    'enabled' => env('FURUGI_MAINTENANCE_ENABLED', false),
    'title' => env('FURUGI_MAINTENANCE_TITLE', 'メンテナンス中です'),
    'message' => env('FURUGI_MAINTENANCE_MESSAGE', '現在、サービス改善のため一時的にご利用いただけません。しばらく時間をおいてから再度アクセスしてください。'),
    'retry_after' => (int) env('FURUGI_MAINTENANCE_RETRY_AFTER', 1800),
    'allow_ips' => array_filter(array_map('trim', explode(',', env('FURUGI_MAINTENANCE_ALLOW_IPS', '')))),
    'except_paths' => [
        'up',
        'manifest.webmanifest',
        'service-worker.js',
        'login',
        'login/demo',
        'logout',
        'admin/maintenance',
        'stripe/webhook',
    ],
];
