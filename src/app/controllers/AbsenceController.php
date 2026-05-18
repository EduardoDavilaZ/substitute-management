<?php

use App\Services\PdfService;
use App\Services\ExcelService;

final class AbsenceController extends Controller
{
    protected function init() : void
    {
        $this->layout = 'admin/layout';
    }

    public function exportExcel() : void
    {
        $absences = (new Absence())->getAbsences();
        $headers  = ['Profesor', 'Fecha', 'Cant. Horas', 'Motivo', 'Justificada'];
        $data     = [];

        foreach ($absences as $absence) {
            $data[] = [
                $absence['full_name'],
                $absence['date'],
                $absence['total_periods'],
                $absence['reason'],
                $absence['is_justified'] ? 'Sí' : 'No'
            ];
        }

        $writer = ExcelService::create($headers, $data, 'Historial de Ausencias');
        download_excel($writer, 'historial_ausencias_' . date('Ymd'));
    }

    public function exportPdf() : void
    {
        $absences = (new Absence())->getAbsences();

        $html  = '<style>';
        $html .= '    body { font-family: sans-serif; }';
        $html .= '    h1 { text-align: center; font-family: sans-serif; }';
        $html .= '    table { width: 100%; border-collapse: collapse; margin-top: 20px; }';
        $html .= '    th, td { border: 1px solid #333333; padding: 8px; text-align: center; font-size: 12px; }';
        $html .= '    th { background-color: #0F4C81; color: #ffffff; }';
        $html .= '    tr:nth-child(even) { background-color: #f2f2f2; }';
        $html .= '</style>';
        $html .= '<h1>Historial de Ausencias</h1>';
        $html .= '<table>';
        $html .= '    <thead>';
        $html .= '        <tr>';
        $html .= '            <th>Profesor</th>';
        $html .= '            <th>Fecha</th>';
        $html .= '            <th>Cant. Horas</th>';
        $html .= '            <th>Motivo</th>';
        $html .= '            <th>Justificada</th>';
        $html .= '        </tr>';
        $html .= '    </thead>';
        $html .= '    <tbody>';

        foreach ($absences as $absence) {
            $justified = $absence['is_justified'] ? 'Sí' : 'No';

            $html .= '        <tr>';
            $html .= '            <td>' . $absence['full_name']    . '</td>';
            $html .= '            <td>' . $absence['date']         . '</td>';
            $html .= '            <td>' . $absence['total_periods'] . '</td>';
            $html .= '            <td>' . $absence['reason']       . '</td>';
            $html .= '            <td>' . $justified               . '</td>';
            $html .= '        </tr>';
        }

        $html .= '    </tbody>';
        $html .= '</table>';

        $pdf = PdfService::create($html);
        download_pdf($pdf, 'historial_ausencias_' . date('Ymd') . '.pdf');
    }
}

?>
