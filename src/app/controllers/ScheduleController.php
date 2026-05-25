<?php

require_once __DIR__ . '/../Services/ExcelService.php';
use App\Services\ExcelService; 
use App\Services\PdfService;
use App\Services\TemplateService;

final class ScheduleController extends Controller 
{
    public function downloadScheduleTemplate(): never
    {
        try {
            $writer = (new TemplateService())->generateUniversalScheduleTemplate();
        } catch (\Throwable $e) {
            http_response_code(500);
            exit($e->getMessage());
        }

        download_excel($writer, 'Plantilla_Gestion_Profesores_' . date('Ymd'));
    }

    public function uploadTeacherSchedule(): never
    {
        $teacherId = (int) input('teacher_id', 0);

        if ($teacherId <= 0) {
            json_error('ID de profesor no válido.');
        }

        if (empty($_FILES['schedule']['name'])) {
            json_error('Debe seleccionar un archivo Excel.');
        }

        $fileError = validate_uploaded_file(
            $_FILES['schedule'],
            ['xlsx'],
            [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/octet-stream',
            ],
            5 * 1024 * 1024
        );

        if ($fileError !== null) {
            json_error($fileError);
        }

        $result = (new Schedule())->importTeacherSchedule($teacherId, $_FILES['schedule']['tmp_name']);

        if ($result['success']) {
            json_success($result['message'], ['count' => $result['count'] ?? 0]);
        }

        json_error($result['message']);
    }

    public function guardScheduleAssignment(int $id, string $day, int $period_id) : array
    {
        $this->view = 'admin/modals/guard_schedule_assignment';
        $this->layout = null;

        $teachers = (new Teacher())->getTeachers();
        $period = (new Period())->getPeriod($period_id);
        $dayNames = ['L' => 'Lunes', 'M' => 'Martes', 'X' => 'Miércoles', 'J' => 'Jueves', 'V' => 'Viernes'];
        $infoText = ($dayNames[$day] ?? $day) . " - " . 
                    $period['name'] . " (" . $period['start_time'] . "-" . $period['end_time'] . ")";
        
        $currentSchedule = ($id > 0) ? (new Schedule())->getSchedule($id) : null;
        $selectedTeacherId = $currentSchedule ? $currentSchedule['teacher_id'] : null;

        return [
            'teachers'          => $teachers,
            'selectedId'        => $id, 
            'selectedTeacherId' => $selectedTeacherId,
            'infoText'          => $infoText,
            'day'               => $day,
            'period'            => $period['id']
        ];
    }   

    public function setGuardPeriod() : void
    {
        $fields = validate_fields(['teacher_id', 'day', 'period_id']);
        
        if (!$fields) {
            json_error("Faltan datos requeridos.");
        }

        extract($fields);

        $scheduleModel = new Schedule();
        $guardError = $scheduleModel->validateGuardAssignment((int) $teacher_id);

        if ($guardError !== null) {
            json_error($guardError);
        }

        if ($scheduleModel->setGuardPeriod((int) $teacher_id, (int) $period_id, $day)) {
            json_success("La asignación se realizó correctamente.");
        }

        json_error("No se pudo guardar la asignación en la base de datos.");
    }

    public function updateGuardPeriod() : void 
    {
        $id = $_POST['id'] ?? 0;
        $teacher_id = $_POST['teacher_id'] ?? 0;
        
        if ($id <= 0 || $teacher_id <= 0) {
            json_error("Datos inválidos. ID: $id, Teacher: $teacher_id");
            return;
        }

        $scheduleModel = new Schedule();
        $guardError = $scheduleModel->validateGuardAssignment((int) $teacher_id, (int) $id);

        if ($guardError !== null) {
            json_error($guardError);
        }

        if ($scheduleModel->updateGuardPeriod((int) $id, (int) $teacher_id)) {
            json_success("El profesor ha sido actualizado correctamente.");
        }

        json_error("No se pudo actualizar la asignación.");
    }

    public function deleteGuardPeriod() : void 
    {
        $id = $_POST['id'] ?? 0;
        if ($id <= 0) {
            json_error("ID de guardia inválido.");
        }

        $result = (new Schedule())->deleteGuardPeriod($id);
        
        if ($result) {
            json_success("La guardia ha sido eliminada correctamente.");
        } else {
            json_error("No se pudo eliminar: el registro no existe o ya fue eliminado.");
        }
    }

    public function exportExcel() : void
    {
        $periodsList = (new Period())->getPeriods();
        $schedules = (new Schedule())->getGuardSchedules();
        
        $days = ['L', 'M', 'X', 'J', 'V'];
        
        $guards = [];
        foreach ($schedules as $s) {
            $guards["{$s['day']}-{$s['period_id']}"][] = $s;
        }

        $headers = ['HORA', 'LUNES', 'MARTES', 'MIÉRCOLES', 'JUEVES', 'VIERNES'];
        $data = [];

        foreach ($periodsList as $p) {
            $timeRange = substr($p['start_time'], 0, 5) . " - " . substr($p['end_time'], 0, 5);
            $row = ["{$p['name']} ({$timeRange})"]; 
            
            foreach ($days as $day) {
                $key = "$day-{$p['id']}";
                $teachers = $guards[$key] ?? [];
                $names = array_map(fn($t) => $t['full_name'], $teachers);
                $row[] = implode(' / ', $names);
            }
            $data[] = $row;
        }

        $writer = ExcelService::create($headers, $data, 'Libro de Guardias');
        download_excel($writer, 'libro_guardias_' . date('Ymd'));
    }

    public function exportPdf(): void
    {
        $periodsList = (new Period())->getPeriods();
        $schedules = (new Schedule())->getGuardSchedules();

        $days = ['L', 'M', 'X', 'J', 'V'];

        $guards = [];

        foreach ($schedules as $s) {
            $guards["{$s['day']}-{$s['period_id']}"][] = $s;
        }

        $html = $this->buildGuardSchedulePdfHtml($periodsList, $guards, $days);
        $pdf = PdfService::create($html);

        download_pdf($pdf, 'libro_guardias_' . date('Ymd'));
    }

    private function buildGuardSchedulePdfHtml(array $periodsList, array $guards, array $days ): string 
    {
        $html = "
            <style>
                body {
                    font-family: 'DejaVu Sans', sans-serif;
                    color: #334155;
                    font-size: 11px;
                }

                table.data-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 12px;
                }

                table.data-table th,
                table.data-table td {
                    border: 1px solid #e2e8f0;
                    padding: 10px 8px;
                    text-align: center;
                    vertical-align: middle;
                }

                table.data-table th {
                    background-color: #0F4C81;
                    color: #ffffff;
                    font-weight: bold;
                    text-transform: uppercase;
                    font-size: 9px;
                    letter-spacing: 0.5px;
                }

                table.data-table tbody tr:nth-child(even) td {
                    background-color: #e0f2fe;
                }

                table.data-table tbody tr:nth-child(odd) td {
                    background-color: #ffffff;
                }

                table.data-table td strong {
                    color: #0c3c66;
                }
            </style>
        ";

        $html .= PdfService::reportHeader('Libro de Guardias');

        $html .= '
            <table class="data-table">
                <thead>
                    <tr><th>HORA</th><th>LUNES</th><th>MARTES</th><th>MIÉRCOLES</th><th>JUEVES</th><th>VIERNES</th>
                    </tr>
                </thead>
                <tbody>
        ';

        foreach ($periodsList as $p) {
            $timeRange = substr($p['start_time'], 0, 5) . ' - ' . substr($p['end_time'], 0, 5);
            $html .= "
                <tr>
                    <td>
                        <strong>{$p['name']}</strong><br>
                        {$timeRange}
                    </td>
            ";

            foreach ($days as $day) {
                $key = "$day-{$p['id']}";
                $teachers = $guards[$key] ?? [];
                $names = array_map(
                    fn($t) => $t['full_name'],
                    $teachers
                );
                $html .= '
                    <td>' . implode('<br>', $names) . '</td>
                ';
            }
            $html .= '</tr>';
        }

        $html .= '
                </tbody>
            </table>
        ';
        return $html;
    }
}
