<?php

return [

    'performance' => [
        'enabled' => (bool) env('PERFORMANCE_LOG_ENABLED', true),
        'threshold_ms' => (int) env('PERFORMANCE_LOG_THRESHOLD_MS', 100),
    ],

    'sql' => [
        'enabled' => (bool) env('SQL_LOG_ENABLED', true),
        'threshold_ms' => (int) env('SQL_SLOW_QUERY_THRESHOLD_MS', 100),
    ],

];
