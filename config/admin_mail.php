<?php

return [
    'from_address' => env('ADMIN_MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS')),
    'from_name' => env('ADMIN_MAIL_FROM_NAME', env('MAIL_FROM_NAME', env('APP_NAME', 'FURUPRO'))),
    'max_subject_length' => (int) env('ADMIN_MAIL_MAX_SUBJECT_LENGTH', 120),
    'max_body_length' => (int) env('ADMIN_MAIL_MAX_BODY_LENGTH', 5000),
    'chunk_size' => (int) env('ADMIN_MAIL_CHUNK_SIZE', 100),
];
