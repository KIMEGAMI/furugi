<?php

return [
    'login_max_attempts' => (int) env('LOGIN_MAX_ATTEMPTS', 5),
    'login_ip_max_attempts' => (int) env('LOGIN_IP_MAX_ATTEMPTS', 20),
    'login_decay_seconds' => (int) env('LOGIN_DECAY_SECONDS', 60),
    'suspicious_login_notifications' => (bool) env('SUSPICIOUS_LOGIN_NOTIFICATIONS', true),
];
