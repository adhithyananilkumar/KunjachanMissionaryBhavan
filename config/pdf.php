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
<<<<<<< HEAD
        'payment_receipt' => [
            'view' => 'pdf.payments.receipt',
            'description' => 'Single inmate payment receipt',
            'filename' => 'inmate_payment_receipt',
        ],
        'payments_report' => [
            'view' => 'pdf.payments.report',
            'description' => 'Payments summary or detailed report',
            'filename' => 'inmate_payments_report',
        ],
=======
>>>>>>> 3e03daa29128f97355c96e657850f19885d91155
    ],
];
