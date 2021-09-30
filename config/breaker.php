<?php

$circuit = [
    // requires
    'type' => 'circuit',
    'server' => [
        'name' => 'saas',
        'uuid' => '00a5ca80-4fe9-11eb-875c-c3e67d5eac81'
    ],
    // optional
    'time_window' => 20, // default value 20s
    'failure_rate_threshold' => 15, // default value  15%
    'interval_to_half_open' => 10 // default value  10s
];

$google = [
    // requires
    'type' => 'google',
    'services' => ['im'],
    // optional
    'time_window' => 10, // default value 10s
    'buckets' => 40, // default value  40
    'k' => 1.5 // default value  10s
];

return $google || $circuit;