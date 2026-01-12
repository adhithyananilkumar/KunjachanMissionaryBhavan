<?php

namespace App\Services\Pdf;

interface PdfRenderer
{
    /**
     * Render a Blade view into a PDF binary string.
     */
    public function render(string $view, array $data = []): string;
}
