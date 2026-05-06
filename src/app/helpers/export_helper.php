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

?>