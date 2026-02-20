<?php

return [
    // IMPORTANT: PHP SSE streams can tie up PHP-FPM/Apache workers.
    // Keep this disabled unless your server is sized/configured for long-lived connections.
    'sse_enabled' => (bool) env('NOTIFICATIONS_SSE_ENABLED', false),

    // Keep streams short; EventSource will reconnect automatically.
    'sse_duration_seconds' => (int) env('NOTIFICATIONS_SSE_DURATION', 15),

    // Poll interval (server-side sleep) between checks.
    'sse_sleep_ms' => (int) env('NOTIFICATIONS_SSE_SLEEP_MS', 1500),
];