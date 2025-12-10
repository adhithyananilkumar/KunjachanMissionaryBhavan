<?php

namespace App\Services\Pdf;

use Barryvdh\DomPDF\Facade\Pdf as Dompdf;

class DompdfRenderer implements PdfRenderer
{
    public function render(string $view, array $data = []): string
    {
        $pdf = Dompdf::loadView($view, $data);

        return $pdf->output();
    }
}
