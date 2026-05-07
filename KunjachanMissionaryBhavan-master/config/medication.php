<?php
return [
    // Windows are [start, end] 24h times. System Admin can later map these to UI.
    'windows' => [
        'morning' => [env('MED_WINDOW_MORNING_START','07:30'), env('MED_WINDOW_MORNING_END','10:30')],
        'noon'    => [env('MED_WINDOW_NOON_START','12:00'),   env('MED_WINDOW_NOON_END','14:00')],
        'night'   => [env('MED_WINDOW_NIGHT_START','19:00'),  env('MED_WINDOW_NIGHT_END','22:00')],
    ],
];
