<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExcelService {
    /**
     * Create and return an XLSX writer object.
     *
     * @param array  $headers Array of column names for the first row.
     * @param array  $data    Matrix of data to be inserted starting from the second row.
     * @param string $title   The title/name of the Excel sheet.
     * @param array  $options Optional configuration for styling or formatting.
     * * @return \PhpOffice\PhpSpreadsheet\Writer\Xlsx
     */
    
    public static function create(array $headers, array $data, string $title = 'Reporte', array $options = []) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($title);

        $sheet->fromArray($headers, NULL, 'A1');
        $sheet->fromArray($data, NULL, 'A2');

        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();
        
        $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0'] // Un gris suave
            ]
        ]);

        foreach (range('A', $highestColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return new Xlsx($spreadsheet);
    }
}

?>