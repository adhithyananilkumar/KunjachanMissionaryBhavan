<?php

return [
    // Number of days after marking Resolved before auto-closing.
    // Set to 0 to disable auto-close.
    'auto_close_days' => (int) env('TICKETS_AUTO_CLOSE_DAYS', 7),
];
