<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService {
    public static function create(string $html) : Dompdf
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape'); 
        $dompdf->render();
        
        return $dompdf;
    }
}

?>