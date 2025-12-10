<?php

return [
    'default_renderer' => 'dompdf',

    'renderers' => [
        'dompdf' => App\Services\Pdf\DompdfRenderer::class,
    ],

    'templates' => [
        'inmate_profile' => [
            'view' => 'pdf.inmates.profile',
            'description' => 'Inmate summary profile report',
            'filename' => 'inmate_{admission_number}_profile',
            'filename_params' => ['admission_number'],
        ],
    ],
];
