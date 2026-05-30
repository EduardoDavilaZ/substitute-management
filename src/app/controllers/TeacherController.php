<?php

final class TeacherController extends Controller
{
    private const UPLOAD_PATH = UPLOADS_PATH . 'teachers/';
    private const ABSENCE_UPLOAD_PATH = UPLOADS_PATH . 'absences/';

    protected function init(): void
    {
        $this->layout = 'teacher/layout';
    }

    public function home(): array
    {
        $id = $_SESSION['user_id'];

        $this->view = 'teacher/home';

        return [
            'teacher' => (new Teacher())->getTeacher($id)
        ];
    }

    public function absences(): array
    {
        $id = $_SESSION['user_id'];

        $this->view = 'teacher/absences';

        return [
            'absences' => (new Absence())->getAbsencesByTeacher($id)
        ];
    }

    public function generateAbsence(): array
    {
        $id = $_SESSION['user_id'];

        $this->view = 'teacher/generate_absence';

        return [
            'periods' => (new Period())->getTeachingPeriods()
        ];
    }

    public function storeAbsence(): never
    {
        $teacherId   = (int) ($_SESSION['user_id'] ?? 0);
        $date        = input('date', '');
        $absenceType = input('absence_type', '');
        $description = input('description', '');
        $notes       = input('notes', '');
        $periods     = $_POST['periods'] ?? [];

        $typeLabels = [
            'medical'  => 'Medica',
            'personal' => 'Personal',
            'training' => 'Formacion',
            'other'    => 'Otra',
        ];
        $reason = isset($typeLabels[$absenceType])
            ? $typeLabels[$absenceType] . ' - ' . $description
            : $description;

        if ($teacherId <= 0) {
            json_error('Sesion no valida.', 401);
        }

        if ($date === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            json_error('Debes indicar una fecha valida.');
        }

        if ((new DateTime($date))->format('N') > 5) {
            json_error('Solo se pueden informar ausencias en dias lectivos.');
        }

        if ($description === '') {
            json_error('Debes indicar el motivo de la ausencia.');
        }

        if ($error = validate_length($description, 3, 255, 'El motivo')) {
            json_error($error);
        }

        if ($notes !== '' && mb_strlen($notes) > 1000) {
            json_error('Las indicaciones no pueden superar 1000 caracteres.');
        }

        if (!is_array($periods) || empty($periods)) {
            json_error('Selecciona al menos una hora afectada.');
        }

        $periodIds = array_values(array_filter(array_map('intval', $periods), fn($id) => $id > 0));
        if (empty($periodIds)) {
            json_error('Selecciona al menos una hora afectada.');
        }

        $uploadedFiles = [];
        $proofFilePath = null;

        if (!empty($_FILES['justification']['name'])) {
            $fileError = validate_uploaded_file(
                $_FILES['justification'],
                ['pdf'],
                ['application/pdf'],
                5 * 1024 * 1024
            );

            if ($fileError !== null) {
                json_error($fileError);
            }

            $filename = upload_file($_FILES['justification'], self::ABSENCE_UPLOAD_PATH, 'proof_');
            if ($filename === null) {
                json_error('No se pudo guardar el justificante.');
            }

            $uploadedFiles[] = $filename;
            $proofFilePath = '/uploads/absences/' . $filename;
        }

        $materialsByPeriod = $this->uploadAbsenceMaterials($periodIds, $uploadedFiles);

        $result = (new Absence())->createTeacherAbsence(
            $teacherId,
            $date,
            $reason,
            $periodIds,
            $proofFilePath,
            $materialsByPeriod,
            $notes ?: null
        );

        if (!$result['success']) {
            foreach ($uploadedFiles as $filename) {
                delete_file(self::ABSENCE_UPLOAD_PATH, $filename);
            }
            json_error($result['message'] ?? 'No se pudo registrar la ausencia.');
        }

        json_success('Ausencia registrada correctamente.', [
            'redirect'      => url('teacher/absences'),
            'absence_id'    => $result['absence_id'] ?? null,
            'substitutions' => $result['substitutions'] ?? 0,
        ]);
    }

    private function uploadAbsenceMaterials(array $periodIds, array &$uploadedFiles): array
    {
        $materialsByPeriod = [];
        $materials = $_FILES['materials'] ?? null;

        if (!is_array($materials) || empty($materials['name'])) {
            return [];
        }

        $allowedExtensions = ['pdf', 'png', 'jpg', 'jpeg', 'doc', 'docx'];
        $allowedMimeTypes = [
            'application/pdf',
            'image/png',
            'image/jpeg',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/zip',
            'application/octet-stream',
        ];

        foreach ($periodIds as $periodId) {
            if (!isset($materials['name'][$periodId]) || !is_array($materials['name'][$periodId])) {
                // no hay archivos para este periodo
            } else {
                $urls = [];

                foreach (array_filter($materials['name'][$periodId]) as $index => $name) {
                    $file = [
                        'name'     => $name,
                        'type'     => $materials['type'][$periodId][$index] ?? '',
                        'tmp_name' => $materials['tmp_name'][$periodId][$index] ?? '',
                        'error'    => $materials['error'][$periodId][$index] ?? UPLOAD_ERR_NO_FILE,
                        'size'     => $materials['size'][$periodId][$index] ?? 0,
                    ];

                    $fileError = validate_uploaded_file($file, $allowedExtensions, $allowedMimeTypes, 5 * 1024 * 1024);
                    if ($fileError !== null) {
                        foreach ($uploadedFiles as $uploadedFile) {
                            delete_file(self::ABSENCE_UPLOAD_PATH, $uploadedFile);
                        }
                        json_error($fileError);
                    }

                    $filename = upload_file($file, self::ABSENCE_UPLOAD_PATH, 'material_');
                    if ($filename === null) {
                        foreach ($uploadedFiles as $uploadedFile) {
                            delete_file(self::ABSENCE_UPLOAD_PATH, $uploadedFile);
                        }
                        json_error('No se pudo guardar un material adjunto.');
                    }

                    $uploadedFiles[] = $filename;
                    $urls[] = '/uploads/absences/' . $filename;
                }

                if (!empty($urls)) {
                    $materialsByPeriod[$periodId] = implode(',', $urls);
                }
            }
        }

        return $materialsByPeriod;
    }

    public function schedule(): array
    {
        $id = $_SESSION['user_id'];

        $this->view = 'teacher/schedule';

        return [
            'periods' => (new Period())->getTeachingPeriods(),
            'schedule' => (new Schedule())->getTeacherSchedule($id)
        ];
    }

    public function substitutions(): array
    {
        $id = $_SESSION['user_id'];

        $this->view = 'teacher/substitutions';

        return [
            'substitutions' => (new Substitution())->getSubstitutionsBySubstitute($id)
        ];
    }

    public function getTecEnabled()
    {
        return json([
            'data' => (new Teacher())->getTeachersEnabled()
        ]);
    }

    public function deleteTeacherById()
    {
        $id = (int) input('teacher_id', 0);

        if ($id <= 0) {
            return json_error("ID de profesor no válido.");
        }

        $teacherModel = new Teacher();
        $teacher = $teacherModel->getTeacher($id);

        if (!$teacher) {
            return json_error("Profesor no encontrado.");
        }

        $result = $teacherModel->deleteTeacher($id);

        if ($result) {

            delete_file(
                self::UPLOAD_PATH,
                $teacher['profile_img_path'] ?? null
            );

            return json_success("Profesor dado de baja");
        }

        return json_error("Error al dar de baja al profesor");
    }

    public function getTeacherById(int $id): array
    {
        $this->layout = null;
        $this->view = 'admin/modals/mod_teacher_modal';

        return [
            'teacher' => (new Teacher())->getTeacher($id)
        ];
    }

    public function updateTeacher()
    {
        $id = (int) input('id', 0);

        if ($id <= 0) {
            return json_error('ID de profesor no válido.');
        }

        $fullName   = input('nameTeacher', '');
        $email      = input('emailTeacher', '');
        $phone      = input('phoneTeacher', '');
        $tutor      = input('tutor', null);

        if ($tutor === null || $tutor === '') {
            $tutor = 0;
        }

        $tutor = (int) $tutor;

        if ($fullName === '' || $email === '') {
            return json_error('Nombre y correo son requeridos.');
        }

        if ($error = validate_length($fullName, 2, 100, 'El nombre')) {
            return json_error($error);
        }

        if ($error = validate_email($email, 100)) {
            return json_error($error);
        }

        if ($phone !== '') {
            if ($error = validate_regex($phone, '/^[\d\s+\-]+$/', 'El teléfono contiene caracteres inválidos.')) {
                return json_error($error);
            }

            if (strlen($phone) > 15) {
                return json_error('El teléfono no puede superar 15 caracteres.');
            }
        }

        $teacherModel = new Teacher();
        $existing = $teacherModel->getTeacher($id);

        if (!$existing) {
            return json_error('Profesor no encontrado.');
        }

        if ((int) $existing['enabled'] === 0) {
            return json_error('No se puede modificar un profesor dado de baja.');
        }

        $newProfileImgPath = null;

        if (!empty($_FILES['profileImage']['name'])) {
            $imgError = validate_uploaded_file(
                $_FILES['profileImage'],
                ['jpg', 'jpeg', 'png', 'gif'],
                ['image/jpeg', 'image/png', 'image/gif'],
                5 * 1024 * 1024
            );

            if ($imgError !== null) {
                return json_error($imgError);
            }

            $newProfileImgPath = upload_file($_FILES['profileImage'], self::UPLOAD_PATH, 'teacher_');

            if ($newProfileImgPath === null) {
                return json_error('No se pudo guardar imagen.');
            }
        }

        $result = $teacherModel->updateTeacher($id, $fullName, $email, $phone, $tutor, $newProfileImgPath);

        if (!$result) {
            return json_error($teacherModel->lastError ?? 'Error desconocido');
        }

        if ($result) {
            if ($newProfileImgPath !== null) {
                delete_file(self::UPLOAD_PATH, $existing['profile_img_path'] ?? null);
            }
            return json_success('Profesor actualizado correctamente.');
        }
        
        return json_error('Error al actualizar profesor.');
    }

    public function getTeacherByIdSchedule(int $id): array
    {
        $this->layout = null;
        $this->view = 'admin/modals/add_schedule_modal';

        return [
            'teacher' => (new Teacher())->getTeacher($id)
        ];
    }
}
