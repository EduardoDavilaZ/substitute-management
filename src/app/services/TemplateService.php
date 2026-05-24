<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RuntimeException;



/**
 * Genera y parsea plantillas Excel de horario docente.
 *
 * Coordenadas fijas (hoja HORARIO):
 * - Fila 1: cabecera (Hora, Lunes…Viernes)
 * - Fila 2+: una fila por periodo lectivo (sin recreo)
 * - Columna A: etiqueta horaria
 * - B=L, C=M, D=X, E=J, F=V
 */
final class TemplateService
{
    public const SHEET_SCHEDULE = 'HORARIO';
    public const SHEET_DATA     = 'DATA';
    public const SHEET_META     = 'META';

    public const HEADER_ROW        = 1;
    public const FIRST_PERIOD_ROW  = 2;
    private const GUARD_LABEL = 'GUARDIA';

    /** @var array<string, string> columna Excel => día en BD */
    private const DAY_COLUMNS = [
        'B' => 'L',
        'C' => 'M',
        'D' => 'X',
        'E' => 'J',
        'F' => 'V',
    ];

    private const HEADER_LABELS = ['Hora', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

    private Spreadsheet $spreadsheet;
    private Worksheet $scheduleSheet;
    private Worksheet $dataSheet;
    private Worksheet $metaSheet;

    /** @var list<array<string, mixed>> */
    private array $periods = [];

    /** @var list<string> */
    private array $classCodes = [];

    private int $teacherId;
    private string $teacherName;

    /**
     * Plantilla universal (misma para todos los profesores).
     * El profesor se asocia al subir el archivo desde el CRUD.
     */
    public function generateUniversalScheduleTemplate(): Xlsx
    {
        return $this->buildScheduleTemplate(0, '');
    }

    /**
     * Plantilla vinculada a un profesor (META con teacher_id).
     */
    public function generateTeacherScheduleTemplate(int $teacherId, string $teacherName): Xlsx
    {
        return $this->buildScheduleTemplate($teacherId, $teacherName);
    }

    private function buildScheduleTemplate(int $teacherId, string $teacherName): Xlsx
    {
        $this->teacherId   = $teacherId;
        $this->teacherName = $teacherName;
        $this->periods     = $this->loadPeriods();
        $this->classCodes  = $this->loadClasses();

        if (empty($this->periods)) {
            throw new RuntimeException('No hay periodos lectivos configurados.');
        }

        if (empty($this->classCodes)) {
            throw new RuntimeException('No hay clases activas para los desplegables.');
        }

        $this->spreadsheet = new Spreadsheet();
        $this->scheduleSheet = $this->spreadsheet->getActiveSheet();
        $this->scheduleSheet->setTitle(self::SHEET_SCHEDULE);

        $this->dataSheet = $this->spreadsheet->createSheet();
        $this->dataSheet->setTitle(self::SHEET_DATA);

        $this->metaSheet = $this->spreadsheet->createSheet();
        $this->metaSheet->setTitle(self::SHEET_META);

        $this->createHeader();
        $this->fillPeriodRows();
        $this->createDataSheet();
        $this->createHiddenMetaSheet();
        $this->createDropdowns();
        $this->applyGuardConditionalFormatting();
        $this->applyStyles();

        $this->spreadsheet->setActiveSheetIndex(0);

        return new Xlsx($this->spreadsheet);
    }

    /**
     * Lee un Excel subido y devuelve entradas de horario listas para persistir.
     *
     * @return array{
     *     teacher_id: int,
     *     teacher_name: string,
     *     entries: list<array{period_id: int, day: string, code: string}>
     * }
     */
    public static function parseTeacherScheduleFile(string $filePath, int $expectedTeacherId): array
    {
        if (!is_readable($filePath)) {
            throw new RuntimeException('No se pudo leer el archivo Excel.');
        }

        $spreadsheet = IOFactory::load($filePath);

        $scheduleSheet = $spreadsheet->getSheetByName(self::SHEET_SCHEDULE);
        $metaSheet     = $spreadsheet->getSheetByName(self::SHEET_META);

        if ($scheduleSheet === null || $metaSheet === null) {
            throw new RuntimeException('El archivo no es una plantilla válida (faltan hojas HORARIO o META).');
        }

        $teacherName = (string) $metaSheet->getCell('B2')->getCalculatedValue();

        $teacherIdFromFile = (int) $metaSheet->getCell('B1')->getCalculatedValue();

        if ($teacherIdFromFile > 0 && $teacherIdFromFile !== $expectedTeacherId) {
            throw new RuntimeException('La plantilla pertenece a otro profesor.');
        }

        $teacherId = $expectedTeacherId;

        $entries = [];

        $periods = (new \Period())->getTeachingPeriods();

        if (empty($periods)) {
            throw new RuntimeException('No hay periodos definidos en la base de datos.');
        }

        foreach ($periods as $i => $period) {
            $row = self::FIRST_PERIOD_ROW + $i;
            $periodId = (int) ($period['id'] ?? 0);

            if ($periodId <= 0) {
                throw new RuntimeException("Periodo inválido en índice {$i}.");
            }

            foreach (self::DAY_COLUMNS as $col => $day) {
                $code = trim((string) $scheduleSheet->getCell("{$col}{$row}")->getCalculatedValue());

                if ($code === '') {
                    continue;
                }
                $entries[] = [
                    'period_id' => $periodId,
                    'day'       => $day,
                    'code'      => strtoupper($code),
                    'is_guard'  => strtoupper($code) === self::GUARD_LABEL,
                ];
            }
        }

        return [
            'teacher_id'   => $teacherId,
            'teacher_name' => $teacherName,
            'entries'      => $entries,
        ];
    }

    private function createHeader(): void
    {
        $this->scheduleSheet->fromArray([self::HEADER_LABELS], null, 'A' . self::HEADER_ROW);
    }

    private function fillPeriodRows(): void
    {
        $row = self::FIRST_PERIOD_ROW;

        foreach ($this->periods as $period) {
            $this->scheduleSheet->setCellValue("A{$row}", $this->formatPeriodLabel($period));
            $this->metaSheet->setCellValue("A{$row}", (int) $period['id']);
            $row++;
        }
    }

    private function createDataSheet(): void
    {
        $this->dataSheet->setCellValue('A1', 'CLASS_CODE');

        $row = 2;
        foreach ($this->classCodes as $code) {
            $this->dataSheet->setCellValue("A{$row}", $code);
            $row++;
        }
    }

    private function createHiddenMetaSheet(): void
    {
        $this->metaSheet->setCellValue('A1', 'teacher_id');
        $this->metaSheet->setCellValue('B1', $this->teacherId);
        $this->metaSheet->setCellValue('A2', 'teacher_name');
        $this->metaSheet->setCellValue('B2', $this->teacherName);
        $this->metaSheet->setCellValue('A4', 'period_id_por_fila');
        $this->metaSheet->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
    }

    private function createDropdowns(): void
    {
        $listFormula = $this->buildInlineListFormula();
        $lastPeriodRow = self::FIRST_PERIOD_ROW + count($this->periods) - 1;

        foreach (range(self::FIRST_PERIOD_ROW, $lastPeriodRow) as $row) {
            foreach (array_keys(self::DAY_COLUMNS) as $col) {
                $this->applyListValidationToCell("{$col}{$row}", $listFormula);
            }
        }
    }

    private function buildInlineListFormula(): string
    {
        $items = array_merge(
            [self::GUARD_LABEL],
            $this->classCodes
        );

        $items = array_map(static function (string $code): string {
            return str_replace('"', '""', $code);
        }, $items);

        return '"' . implode(',', $items) . '"';
    }

    private function applyListValidationToCell(string $cellCoordinate, string $listFormula): void
    {
        $validation = new DataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
        $validation->setAllowBlank(true);
        $validation->setShowDropDown(true);
        $validation->setShowInputMessage(true);
        $validation->setPromptTitle('Clase');
        $validation->setPrompt('Seleccione un código de clase de la lista.');
        $validation->setShowErrorMessage(true);
        $validation->setErrorTitle('Valor no reconocido');
        $validation->setError('Use un código de la lista o déjelo vacío.');
        $validation->setFormula1($listFormula);

        $this->scheduleSheet->getCell($cellCoordinate)->setDataValidation($validation);
    }

    private function applyStyles(): void
    {
        $lastRow = self::FIRST_PERIOD_ROW + count($this->periods) - 1;
        $headerRange = 'A1:F1';
        $gridRange   = "A1:F{$lastRow}";

        $this->scheduleSheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F4C81'],
            ],
        ]);

        $this->scheduleSheet->getStyle($gridRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'],
                ],
            ],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $this->scheduleSheet->getStyle("A" . self::FIRST_PERIOD_ROW . ":A{$lastRow}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0F2FE'],
            ],
        ]);

        $this->scheduleSheet->getColumnDimension('A')->setWidth(18);
        foreach (array_keys(self::DAY_COLUMNS) as $col) {
            $this->scheduleSheet->getColumnDimension($col)->setWidth(16);
        }

        $this->scheduleSheet->freezePane('B2');
    }

    private function loadPeriods(): array
    {
        return (new \Period())->getTeachingPeriods();
    }

    private function loadClasses(): array
    {
        $rows = (new \Classes())->getActiveClassCodes();
        return array_column($rows, 'code');
    }

    private function formatPeriodLabel(array $period): string
    {
        $start = substr((string) ($period['start_time'] ?? ''), 0, 5);
        $end   = substr((string) ($period['end_time'] ?? ''), 0, 5);

        return "{$start}-{$end}";
    }

    private function applyGuardConditionalFormatting(): void
    {
        $lastPeriodRow = self::FIRST_PERIOD_ROW + count($this->periods) - 1;

        foreach (range(self::FIRST_PERIOD_ROW, $lastPeriodRow) as $row) {

            foreach (array_keys(self::DAY_COLUMNS) as $col) {

                $cell = "{$col}{$row}";

                $conditional = new \PhpOffice\PhpSpreadsheet\Style\Conditional();

                $conditional->setConditionType(
                    \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT
                );

                $conditional->setOperatorType(
                    \PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_CONTAINSTEXT
                );

                $conditional->setText(self::GUARD_LABEL);

                $conditional->addCondition(self::GUARD_LABEL);

                $style = $conditional->getStyle();

                $style->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('DCE8FA');

                $conditions = $this->scheduleSheet
                    ->getStyle($cell)
                    ->getConditionalStyles();

                $conditions[] = $conditional;

                $this->scheduleSheet
                    ->getStyle($cell)
                    ->setConditionalStyles($conditions);
            }
        }
    }
}

