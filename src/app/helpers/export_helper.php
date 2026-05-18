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

/**
 * Logo embebido en base64 para PDFs (Dompdf).
 */
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

/**
 * Cabecera HTML para informes PDF con título a la izquierda y logo a la derecha.
 */
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
        <table class="report-header" style="width:100%; border:none; border-collapse:collapse; margin-bottom:16px;">
            <tr>
                <td style="border:none; padding:0; vertical-align:top; width:75%;">
                    <h1 style="text-align:left; margin:0; color:#0F4C81; font-size:22px;">' . $titleEsc . '</h1>
                    <p class="meta" style="text-align:left; margin:6px 0 0 0; color:#64748b; font-size:9px;">' . $metaText . '</p>
                </td>
                <td style="border:none; padding:0; vertical-align:top; text-align:right; width:25%;">
                    ' . $logoHtml . '
                </td>
            </tr>
        </table>';
}

?>