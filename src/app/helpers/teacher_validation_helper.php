<?php

/**
 * Validaciones de servidor para modificar un profesor.
 */
final class TeacherUpdateValidator
{
    public const FULL_NAME_MAX_LENGTH = 100;
    public const EMAIL_MAX_LENGTH = 100;
    public const PHONE_MAX_LENGTH = 15;
    public const FULL_NAME_MIN_LENGTH = 2;
    public const PROFILE_IMAGE_MAX_BYTES = 5 * 1024 * 1024;

    /**
     * @param Teacher $teacherModel
     * @param array<string, mixed> $existing Fila devuelta por Teacher::getTeacher()
     * @return string|null Mensaje de error o null si los datos son válidos.
     */
    public static function validateUpdatePayload(
        $teacherModel,
        int $id,
        array $existing,
        string $fullName,
        string $email,
        string $phone
    ): ?string {
        if ($fullName === '' || $email === '') {
            return 'Nombre y correo son requeridos.';
        }

        if (empty($existing['id'])) {
            return 'Profesor no encontrado.';
        }

        if (array_key_exists('enabled', $existing) && (int) $existing['enabled'] === 0) {
            return 'No se puede modificar un profesor dado de baja.';
        }

        $fieldError = self::validateTextFields($fullName, $email, $phone);
        if ($fieldError !== null) {
            return $fieldError;
        }

        if ($teacherModel->existsOtherWithEmail($email, $id)) {
            return 'Ya existe otro profesor con ese correo.';
        }

        if ($phone !== '' && $teacherModel->existsOtherWithPhone($phone, $id)) {
            return 'Ya existe otro profesor con ese teléfono.';
        }

        return null;
    }

    private static function validateTextFields(string $fullName, string $email, string $phone): ?string
    {
        $lenName = function_exists('mb_strlen') ? mb_strlen($fullName, 'UTF-8') : strlen($fullName);
        if ($lenName < self::FULL_NAME_MIN_LENGTH) {
            return 'El nombre debe tener al menos ' . self::FULL_NAME_MIN_LENGTH . ' caracteres.';
        }
        if ($lenName > self::FULL_NAME_MAX_LENGTH) {
            return 'El nombre no puede superar ' . self::FULL_NAME_MAX_LENGTH . ' caracteres.';
        }

        if (strlen($email) > self::EMAIL_MAX_LENGTH) {
            return 'El correo no puede superar ' . self::EMAIL_MAX_LENGTH . ' caracteres.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'El correo electrónico no es válido.';
        }

        if ($phone !== '') {
            if (strlen($phone) > self::PHONE_MAX_LENGTH) {
                return 'El teléfono no puede superar ' . self::PHONE_MAX_LENGTH . ' caracteres.';
            }
            if (!preg_match('/^[\d\s+\-]+$/', $phone)) {
                return 'El teléfono solo puede incluir dígitos, espacios, + y guiones.';
            }
        }

        return null;
    }

    /**
     * @param array{name?: string, type?: string, tmp_name?: string, error?: int, size?: int} $file
     */
    public static function validateProfileImageUpload(array $file): ?string
    {
        $err = $file['error'] ?? UPLOAD_ERR_NO_FILE;

        if ($err === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($err !== UPLOAD_ERR_OK) {
            if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
                return 'La imagen excede el tamaño permitido.';
            }
            return 'Error al subir imagen.';
        }

        $size = (int) ($file['size'] ?? 0);
        if ($size > self::PROFILE_IMAGE_MAX_BYTES) {
            return 'La imagen excede 5MB.';
        }

        $tmp = $file['tmp_name'] ?? '';
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            return 'Archivo de imagen no válido.';
        }

        $ext = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $allowedExt, true)) {
            return 'Extensión no permitida. Use JPG, PNG o GIF.';
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmp);
        $allowedMime = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($mime, $allowedMime, true)) {
            return 'Tipo de archivo no permitido.';
        }

        return null;
    }
}
