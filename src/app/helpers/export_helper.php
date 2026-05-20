<?php
function download_excel(mixed $writer, string $filename) : never
{
    clear_buffer();
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
    header('Cache-Control: max-age=0');
    
    $writer->save('php://output');
    exit;
}

function download_pdf(mixed $dompdf, string $filename) : never
{
    clear_buffer();
    $dompdf->stream($filename, ["Attachment" => true]);
    exit;
}

function clear_buffer() : void
{
    if (ob_get_length()) {
        ob_end_clean();
    }
}

function pdf_logo_data_uri(string $filename = 'isotype.png'): ?string
{
    $path = PUBLIC_PATH . 'assets/img/' . basename($filename);
    if (!is_readable($path)) {
        return null;
    }

    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $mime = match ($ext) {
        'jpg', 'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        default => 'image/png',
    };

    return 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($path));
}

function pdf_report_header_html(string $title, string $meta = ''): string
{
    $metaText = $meta !== ''
        ? htmlspecialchars($meta, ENT_QUOTES, 'UTF-8')
        : 'Generado el ' . date('d/m/Y H:i') . ' · Gestión de Guardias';

    $logoHtml = '';
    $logoDataUri = pdf_logo_data_uri('isotype.png');
    if ($logoDataUri !== null) {
        $logoHtml = '<img src="' . $logoDataUri . '" alt="Logo" style="height:48px; width:auto;" />';
    }

    $titleEsc = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

    return '
        <div class="container-fluid p-0 mb-3">
            <div class="row align-items-start">
                <div class="col-9 text-start">
                    <h1 class="m-0 text-primary fs-4 fw-bold">' . $titleEsc . '</h1>
                    <p class="text-secondary small mt-1 mb-0" style="font-size: 9px;">' . $metaText . '</p>
                </div>
                <div class="col-3 text-end">
                    ' . $logoHtml . '
                </div>
            </div>
        </div>';
}
