<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Document' }}</title>
    <style>
        @page {
            size: A4;
            margin: 48mm 12mm 16mm 12mm;
        }

        /* Global Reset & Base */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: sans-serif;
            font-size: 9pt;
            color: #1f2933;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* Typography */
        h1 {
            font-size: 13pt;
            font-weight: 600;
            color: #111;
            margin: 0 0 4pt;
        }

        h2 {
            font-size: 11pt;
            font-weight: 600;
            color: #333;
            margin: 10pt 0 4pt;
        }

        p {
            margin: 0 0 6pt;
        }

        .small {
            font-size: 7.5pt;
            color: #6b7280;
        }

        .text-right {
            text-align: right;
        }

        /* Layout Utilities */
        .mb-1 {
            margin-bottom: 4pt;
        }

        .mb-3 {
            margin-bottom: 12pt;
        }

        .mt-2 {
            margin-top: 8pt;
        }

        /* Section Styling */
        .section-title {
            font-size: 8pt;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #4b2b24;
            border-bottom: 1pt solid #ddd;
            padding-bottom: 2pt;
            margin: 15pt 0 8pt;
            page-break-after: avoid;
        }

        /* Fixed Header & Footer */
        .page-header {
            position: fixed;
            top: -36mm;
            left: 0;
            right: 0;
            height: 32mm;
            font-size: 9pt;
            border-bottom: 1pt solid #ccc;
            padding-bottom: 5pt;
        }

        .page-footer {
            position: fixed;
            bottom: -10mm;
            left: 0;
            right: 0;
            height: 10mm;
            font-size: 8pt;
            color: #888;
            border-top: 1pt solid #eee;
            padding-top: 4pt;
        }

        /* Content Wrapper */
        .content {
            display: block;
            width: 100%;
        }

        /* Data Tables (Replaces fragile divs) */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            /* Ensures strict column widths */
            margin-bottom: 10pt;
            page-break-inside: auto;
        }

        .data-table tr {
            page-break-inside: avoid;
            /* Prevents rows splitting across pages */
        }

        .data-table td {
            padding: 4pt 0;
            vertical-align: top;
            border-bottom: 0.5pt solid #f0f0f0;
            word-wrap: break-word;
            /* Prevents overflow */
        }

        .data-table td.label {
            width: 30%;
            color: #555;
            padding-right: 10pt;
        }

        .data-table td.value {
            width: 70%;
            color: #000;
            font-weight: 400;
        }

        .signature-block {
            margin-top: 30pt;
            page-break-inside: avoid;
        }

        .signature-line {
            margin-top: 30pt;
            border-top: 1pt solid #000;
            width: 200pt;
        }
    </style>
</head>

<body>
    @php($generatedAt = $generatedAt ?? now())
    @php($generatedBy = $generatedBy ?? (auth()->user()->name ?? 'System'))

    <div class="page-header">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <!-- Combined logo + title cell so title hugs the logo -->
                <td style="vertical-align: middle; padding: 0;">
                    <table style="border-collapse: collapse;">
                        <tr>
                            <td style="width: 26mm; padding: 0; vertical-align: middle;">
                                <img src="{{ public_path('assets/kunjachanMissionaryLogo.png') }}" alt="Logo"
                                    style="height: 21mm; width: auto; display: block; margin-right: -9mm;">
                            </td>
                            <td style="vertical-align: middle; padding-left: 0;">
                                <div
                                    style="font-weight: 600; font-size: 12pt; text-transform: uppercase; color: #4b2b24; line-height: 1.1; display:inline-block; transform: translateX(-8mm);">
                                    Kunjachan Missionary<br>Bhavan
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>

                <!-- Contact: Fixed width to maintain alignment -->
                <td
                    style="width: 40%; vertical-align: middle; text-align: right; font-size: 8pt; color: #444; line-height: 1.3;">
                    Ramapuram, Idiyanal P.O, Kottayam<br>
                    Kerala - 686576<br>
                    E-mail: kunjachanmissionary@gmail.com<br>
                    Ph: 04822 260435, 260835 | Mob: 9048864128
                </td>
            </tr>
        </table>
    </div>

    <div class="page-footer">
        <table style="width:100%;border-collapse:collapse;">
            <tr>
                <td style="text-align:left;">
                    Generated on: {{ $generatedAt->format('Y-m-d H:i') }} · Generated by: {{ $generatedBy }}
                </td>
                <td style="text-align:right;">
                    <!-- Page numbers will be rendered by Dompdf via page script below -->
                </td>
            </tr>
        </table>
    </div>

    <main class="content">
        @yield('content')
    </main>
    
    {{-- Dompdf page script to render page numbers (replaces literal placeholders) --}}
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->get_font('Helvetica', 'normal');
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $y = $pdf->get_height() - 18;
            $x = $pdf->get_width() - 112;
            $pdf->page_text($x, $y, $text, $font, 8, array(0,0,0));
        }
    </script>
</body>

</html>