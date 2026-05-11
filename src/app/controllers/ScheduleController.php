<?php

require_once __DIR__ . '/../services/ExcelService.php';
use App\Services\ExcelService;
use App\Services\PdfService;

final class ScheduleController extends Controller 
{
    protected function init() : void
    {
        $this->layout = 'teacher/layout';
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
        $success = (new Schedule())->setGuardPeriod($teacher_id, $period_id, $day);
        
        if ($success) {
            json_success("La asignación se realizó correctamente.");
        } else {
            json_error("No se pudo guardar la asignación en la base de datos.");
        }
    }

    public function updateGuardPeriod() : void 
    {
        $id = $_POST['id'] ?? 0;
        $teacher_id = $_POST['teacher_id'] ?? 0;
        
        if ($id <= 0 || $teacher_id <= 0) {
            json_error("Datos inválidos. ID: $id, Teacher: $teacher_id");
            return;
        }

        $result = (new Schedule())->updateGuardPeriod($id, $teacher_id);
        
        if ($result) {
            json_success("El profesor ha sido actualizado correctamente.");
        } else {
            json_error("No se pudo actualizar la asignación.");
        }
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

    public function exportPdf() : void 
    {
        $periodsList = (new Period())->getPeriods();
        $schedules = (new Schedule())->getGuardSchedules();
        $days = ['L', 'M', 'X', 'J', 'V'];
        
        $guards = [];
        foreach ($schedules as $s) {
            $guards["{$s['day']}-{$s['period_id']}"][] = $s;
        }

        $html = "
            <style>
                body { font-family: sans-serif; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #333; padding: 8px; text-align: center; }
                th { background-color: #f2f2f2; }
                h1 { text-align: center; }
            </style><h1>Libro de Guardias</h1>
            <table>
                <thead>
                    <tr><th>HORA</th><th>LUNES</th><th>MARTES</th><th>MIÉRCOLES</th><th>JUEVES</th><th>VIERNES</th></tr>
                </thead>
                <tbody>";

                foreach ($periodsList as $p) {
                    $timeRange = substr($p['start_time'], 0, 5) . " - " . substr($p['end_time'], 0, 5);
                    $html .= "<tr><td><strong>{$p['name']}</strong><br>{$timeRange}</td>";
                    
                    foreach ($days as $day) {
                        $key = "$day-{$p['id']}";
                        $teachers = $guards[$key] ?? [];
                        $names = array_map(fn($t) => $t['full_name'], $teachers);
                        $html .= "<td>" . implode('<br>', $names) . "</td>";
                    }
                    $html .= "</tr>";
                }
        $html .= "</tbody></table>";

        $writer = PdfService::create($html);
        download_pdf($writer, 'libro_guardias_' . date('Ymd') . '.pdf');
    }
}
