<?php

final class TeacherController extends Controller
{
    private const UPLOAD_PATH = UPLOADS_PATH . 'teachers/';

    protected function init(): void
    {
        $this->layout = 'teacher/layout';
    }

    public function home(): array
    {
        $id = current_user_id();

        $this->view = 'teacher/home';

        return [
            'teacher' => (new Teacher())->getTeacher($id)
        ];
    }

    public function absences(): void
    {
        $id = current_user_id();

        $this->view = 'teacher/absences';
    }

    public function generateAbsence(): array
    {
        $id = current_user_id();

        $this->view = 'teacher/generate_absence';

        return [
            'periods' => (new Period())->getPeriods()
        ];
    }

    public function schedule(): void
    {
        $id = current_user_id();

        $this->view = 'teacher/schedule';
    }

    public function substitutions(): void
    {
        $id = current_user_id();

        $this->view = 'teacher/substitutions';
    }

    public function getTecEnabled()
    {
        return json([
            'data' => (new Teacher())->getTeachersEnabled()
        ]);
    }

    // public function deleteTeacherById()
    // {
    //     $id = (int) input('teacher_id', 0);

    //     if ($id <= 0) {
    //         return json_error("ID de profesor no válido.");
    //     }

    //     $teacherModel = new Teacher();
    //     $teacher = $teacherModel->getTeacher($id);

    //     if (!$teacher) {
    //         return json_error("Profesor no encontrado.");
    //     }

    //     $result = $teacherModel->deleteTeacher($id);

    //     if ($result) {

    //         delete_file(
    //             self::UPLOAD_PATH,
    //             $teacher['profile_img_path'] ?? null
    //         );

    //         return json_success("Profesor dado de baja");
    //     }

    //     return json_error("Error al dar de baja al profesor");
    // }

    public function getTeacherById(int $id): array
    {
        $this->layout = null;
        $this->view = 'admin/modals/mod_teacher_modal';

        return [
            'teacher' => (new Teacher())->getTeacher($id)
        ];
    }

    // public function updateTeacher()
    // {
    //     $id = (int) input('id', 0);

    //     if ($id <= 0) {
    //         return json_error('ID de profesor no válido.');
    //     }

    //     $fullName   = input('nameTeacher', '');
    //     $email      = input('emailTeacher', '');
    //     $phone      = input('phoneTeacher', '');
    //     $tutor      = input('tutor', null);

    //     if ($tutor === null || $tutor === '') {
    //         $tutor = 0;
    //     }

    //     $tutor = (int) $tutor;

    //     if ($fullName === '' || $email === '') {
    //         return json_error('Nombre y correo son requeridos.');
    //     }

    //     if ($error = validate_length($fullName, 2, 100, 'El nombre')) {
    //         return json_error($error);
    //     }

    //     if ($error = validate_email($email, 100)) {
    //         return json_error($error);
    //     }

    //     if ($phone !== '') {
    //         if ($error = validate_regex($phone, '/^[\d\s+\-]+$/', 'El teléfono contiene caracteres inválidos.')) {
    //             return json_error($error);
    //         }

    //         if (strlen($phone) > 15) {
    //             return json_error('El teléfono no puede superar 15 caracteres.');
    //         }
    //     }

    //     $teacherModel = new Teacher();
    //     $existing = $teacherModel->getTeacher($id);

    //     if (!$existing) {
    //         return json_error('Profesor no encontrado.');
    //     }

    //     if ((int) $existing['enabled'] === 0) {
    //         return json_error('No se puede modificar un profesor dado de baja.');
    //     }

    //     $newProfileImgPath = null;

    //     if (!empty($_FILES['profileImage']['name'])) {
    //         $imgError = validate_uploaded_file(
    //             $_FILES['profileImage'],
    //             ['jpg', 'jpeg', 'png', 'gif'],
    //             ['image/jpeg', 'image/png', 'image/gif'],
    //             5 * 1024 * 1024
    //         );

    //         if ($imgError !== null) {
    //             return json_error($imgError);
    //         }

    //         $newProfileImgPath = upload_file($_FILES['profileImage'], self::UPLOAD_PATH, 'teacher_');

    //         if ($newProfileImgPath === null) {
    //             return json_error('No se pudo guardar imagen.');
    //         }
    //     }

    //     $result = $teacherModel->updateTeacher($id, $fullName, $email, $phone, $tutor, $newProfileImgPath);

    //     if (!$result) {
    //         return json_error($teacherModel->lastError ?? 'Error desconocido');
    //     }

    //     if ($result) {
    //         if ($newProfileImgPath !== null) {
    //             delete_file(self::UPLOAD_PATH, $existing['profile_img_path'] ?? null);
    //         }
    //         return json_success('Profesor actualizado correctamente.');
    //     }
        
    //     return json_error('Error al actualizar profesor.');
    // }

    public function getTeacherByIdSchedule(int $id): array
    {
        $this->layout = null;
        $this->view = 'admin/modals/add_schedule_modal';

        return [
            'teacher' => (new Teacher())->getTeacher($id)
        ];
    }
}