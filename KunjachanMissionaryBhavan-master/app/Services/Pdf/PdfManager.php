<?php

namespace App\Services\Pdf;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PdfManager
{
    public function __construct(
        protected PdfRenderer $renderer,
        protected Filesystem $storage,
    ) {
    }

    /**
     * Download a PDF built from a named template.
     */
    public function downloadTemplate(string $templateKey, array $data = [], ?string $filename = null): StreamedResponse
    {
        $config = config("pdf.templates.{$templateKey}");
        abort_unless($config, 404, 'Unknown PDF template');

        $view = Arr::get($config, 'view');
        $resolvedName = $filename ?: $this->makeFilename(
            $templateKey,
            Arr::get($config, 'filename_params', []),
            $data
        );

        $binary = $this->renderer->render($view, $data);

        return response()->streamDownload(function () use ($binary) {
            echo $binary;
        }, $resolvedName, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Build a filename using a simple token pattern.
     * Pattern example in config: "inmate_{admission_number}_profile.pdf".
     */
    public function makeFilename(string $templateKey, array $tokens = [], array $data = []): string
    {
        $config = config("pdf.templates.{$templateKey}");
        $pattern = Arr::get($config, 'filename', $templateKey . '.pdf');

        $replacements = [];
        foreach ($tokens as $token) {
            $value = data_get($data, $token) ?? data_get($data, "inmate.{$token}") ?? '';
            $value = Str::slug((string) $value, '_');
            $replacements['{' . $token . '}'] = $value;
        }

        $name = strtr($pattern, $replacements);

        if (! Str::endsWith($name, '.pdf')) {
            $name .= '.pdf';
        }

        return $name;
    }
}
