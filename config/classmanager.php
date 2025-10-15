<?php

return [
    'defaults' => [
        'app_name' => env('CLASS_APP_NAME', config('app.name', 'Laravel')),
        'weekly_cash_amount' => (int) env('CLASS_WEEKLY_CASH_AMOUNT', 10000),
        'github_url' => env('CLASS_GITHUB_URL', null),
        'footer_text' => env('CLASS_FOOTER_TEXT', 'Dikelola bersama oleh komunitas kelas.'),
    ],
];
