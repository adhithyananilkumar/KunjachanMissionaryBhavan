<?php

return [

    // Show DomPDF warnings as exceptions (keep default behaviour)
    'show_warnings' => false,

    // Public path (use Laravel default)
    'public_path' => null,

    // Keep entity conversion behaviour
    'convert_entities' => true,

    'options' => [
        // Font directories (same as package defaults)
        'font_dir'   => storage_path('fonts'),
        'font_cache' => storage_path('fonts'),

        // Temp dir + chroot (security defaults)
        'temp_dir' => sys_get_temp_dir(),
        'chroot'   => realpath(base_path()),

        // Protocols & artifact validation
        'allowed_protocols' => [
            'data://'  => ['rules' => []],
            'file://'  => ['rules' => []],
            'http://'  => ['rules' => []],
            'https://' => ['rules' => []],
        ],
        'artifactPathValidation' => null,
        'log_output_file'        => null,

        // IMPORTANT SIZE OPTIMISATION: enable font subsetting
        // Only the glyphs actually used in the document are embedded.
        'enable_font_subsetting' => true,

        // Rendering backend (keep default)
        'pdf_backend' => 'CPDF',

        // Media, paper size & orientation
        'default_media_type'       => 'screen',
        'default_paper_size'       => 'a4',
        'default_paper_orientation'=> 'portrait',

        // Default font family
        // Use a core PDF font (Helvetica) instead of a large TTF like DejaVu.
        // This keeps the visual style (simple sans-serif) but drastically
        // reduces embedded font size.
        'default_font' => 'Helvetica',

        // Image & font DPI (slightly lower than 96 for smaller PDFs
        // without visible quality loss on A4 reports).
        'dpi' => 72,

        // PHP & JS in PDFs (keep JS but disable PHP for safety)
        'enable_php'        => false,
        'enable_javascript' => true,

        // Remote assets (keep disabled; we use local public_path assets)
        'enable_remote'        => false,
        'allowed_remote_hosts' => null,

        // Line-height tuning
        'font_height_ratio' => 1.1,

        // HTML5 parser (always on in dompdf 2.x, keep true)
        'enable_html5_parser' => true,
    ],

];
