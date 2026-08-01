<?php

declare(strict_types=1);

return [
    'rfq_operations_email' => env('RFQ_OPERATIONS_EMAIL'),
    'rfq_sla' => [
        'first_response_hours' => (int) env('RFQ_FIRST_RESPONSE_HOURS', 4),
        'stale_contact_hours' => (int) env('RFQ_STALE_CONTACT_HOURS', 48),
    ],
];
