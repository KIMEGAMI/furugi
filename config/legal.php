<?php

return [
    'operator_name' => env('LEGAL_OPERATOR_NAME', '神田靖雄'),
    'responsible_person' => env('LEGAL_RESPONSIBLE_PERSON', env('LEGAL_OPERATOR_NAME', '神田靖雄')),
    'operator_address' => env('LEGAL_OPERATOR_ADDRESS'),
    'operator_phone' => env('LEGAL_OPERATOR_PHONE'),
    'contact_email' => env('LEGAL_CONTACT_EMAIL', env('CONTACT_TO_ADDRESS')),
];
