<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

final class PdfService
{
    public static function create(
        string $html,
        string $orientation = 'landscape'
    ): Dompdf {

        $options = new Options();

        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', $orientation);

        $dompdf->render();

        return $dompdf;
    }

    public static function logoDataUri(
        string $filename = 'isotype.png'
    ): ?string {

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

        return 'data:' . $mime
            . ';base64,'
            . base64_encode((string) file_get_contents($path));
    }

    public static function reportHeader(
        string $title,
        string $meta = ''
    ): string {

        $metaText = $meta !== ''
            ? htmlspecialchars($meta, ENT_QUOTES, 'UTF-8')
            : 'Generado el '
                . date('d/m/Y H:i');

        $logoHtml = '';

        $logo = self::logoDataUri();

        if ($logo !== null) {

            $logoHtml = '
                <img 
                    src="' . $logo . '" 
                    alt="Logo"
                    style="height:48px;width:auto;"
                >
            ';
        }

        $titleEsc = htmlspecialchars(
            $title,
            ENT_QUOTES,
            'UTF-8'
        );

        return '
            <table style="
                width:100%;
                border-collapse:collapse;
                margin-bottom:16px;
            ">
                <tr>

                    <td style="
                        width:75%;
                        vertical-align:top;
                    ">
                        <h1 style="
                            margin:0;
                            font-size:22px;
                            color:#0F4C81;
                        ">
                            ' . $titleEsc . '
                        </h1>

                        <p style="
                            margin:6px 0 0;
                            font-size:9px;
                            color:#64748b;
                        ">
                            ' . $metaText . '
                        </p>
                    </td>

                    <td style="
                        width:25%;
                        text-align:right;
                    ">
                        ' . $logoHtml . '
                    </td>

                </tr>
            </table>
        ';
    }
}