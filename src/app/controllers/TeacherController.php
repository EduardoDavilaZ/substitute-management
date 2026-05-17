<?php

final class TeacherController extends Controller
{
    private const TEACHER_IMG_DOCUMENT_SUBPATH = '/assets/imgTeacher/';

    protected function init() : void
    {
        $this->layout = 'teacher/layout';
    }

    public function home() : void
    {
        $this->view = 'teacher/home';
    }

    public function getTecEnabled()
    {
        return json(['data' => (new Teacher())->getTeachersEnabled()]);
    }

    public function deleteTeacherById()
    {
        $id = $_POST["teacher_id"] ?? 0;
        if($id <= 0)
        {
            return json_error("ID de profesor no válido.");
        }
        $teacherModel = new Teacher();
        $teacher = $teacherModel->getTeacher((int) $id);
        $result = $teacherModel->deleteTeacher($id);
        if ($result) {
            $this->deleteTeacherProfileImage($teacher['profile_img_path'] ?? null);
            return json_success("Profesor dado de baja");
        } else {
            return json_error("Error al dar de baja al profesor");
        }
    }

    public function getTeacherById(int $id)
    {
        $this->layout = null;
        $this->view = 'admin/modals/mod_teacher_modal';
        return ['teacher' => (new Teacher())->getTeacher($id)];
    }

    public function updateTeacher()
    {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            return json_error("ID de profesor no válido.");
        }

        $fullName = trim((string) ($_POST['nameTeacher'] ?? ''));
        $email = trim((string) ($_POST['emailTeacher'] ?? ''));
        $phone = trim((string) ($_POST['phoneTeacher'] ?? ''));

        $teacherModel = new Teacher();
        $existing = $teacherModel->getTeacher($id);

        $payloadError = TeacherUpdateValidator::validateUpdatePayload(
            $teacherModel,
            $id,
            $existing,
            $fullName,
            $email,
            $phone
        );
        if ($payloadError !== null) {
            return json_error($payloadError);
        }

        $newProfileImgPath = null;
        if (!empty($_FILES['profileImage']) && is_array($_FILES['profileImage'])) {
            $fileErr = $_FILES['profileImage']['error'] ?? UPLOAD_ERR_NO_FILE;
            if ($fileErr !== UPLOAD_ERR_NO_FILE) {
                $imgError = TeacherUpdateValidator::validateProfileImageUpload($_FILES['profileImage']);
                if ($imgError !== null) {
                    return json_error($imgError);
                }

                $file = $_FILES['profileImage'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $fileName = uniqid('teacher_') . '.' . $ext;
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . self::TEACHER_IMG_DOCUMENT_SUBPATH;

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $uploadPath = $uploadDir . $fileName;

                if (!is_uploaded_file($file['tmp_name']) || !move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    return json_error("No se pudo guardar imagen.");
                }
                $newProfileImgPath = $fileName;
            }
        }

        $result = $teacherModel->updateTeacher($id, $fullName, $email, $phone, $newProfileImgPath);
        if ($result) {
            if ($newProfileImgPath !== null) {
                $this->deleteTeacherProfileImage($existing['profile_img_path'] ?? null);
            }
            return json_success("Profesor actualizado correctamente.");
        }

        if ($newProfileImgPath !== null) {
            $this->deleteTeacherProfileImage($newProfileImgPath);
        }
        return json_error("Error al actualizar profesor.");
        
    }

    private function deleteTeacherProfileImage(?string $fileName): void
    {
        if ($fileName === null || $fileName === '') {
            return;
        }

        $base = basename($fileName);
        if ($base === '' || $base !== str_replace(['/', '\\', "\0"], '', $fileName)) {
            return;
        }

        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . self::TEACHER_IMG_DOCUMENT_SUBPATH;
        $realUploadDir = realpath($uploadDir);
        if ($realUploadDir === false || !is_dir($realUploadDir)) {
            return;
        }

        $candidate = $realUploadDir . DIRECTORY_SEPARATOR . $base;
        $realFile = realpath($candidate);
        if (
            $realFile !== false
            && is_file($realFile)
            && strpos($realFile, $realUploadDir . DIRECTORY_SEPARATOR) === 0
        ) {
            @unlink($realFile);
        }
    }
}
?>
