<?php

namespace App\Services\Pdf;

use Barryvdh\DomPDF\Facade\Pdf as Dompdf;

class DompdfRenderer implements PdfRenderer
{
    public function render(string $view, array $data = []): string
    {
        if (!extension_loaded('gd') && !extension_loaded('imagick')) {
            $ini = function_exists('php_ini_loaded_file') ? (php_ini_loaded_file() ?: '(none)') : '(unknown)';
            $sapi = PHP_SAPI;
            $binary = defined('PHP_BINARY') ? PHP_BINARY : '(unknown)';

            throw new \RuntimeException(
                'PDF generation requires the PHP GD extension (ext-gd). ' .
                'Enable it in your php.ini (or install Imagick) and restart the PHP/web server. ' .
                "Runtime: sapi={$sapi}, php=" . PHP_VERSION . "; php_binary={$binary}; php_ini={$ini}"
            );
        }

        $pdf = Dompdf::loadView($view, $data);

        return $pdf->output();
    }
}
