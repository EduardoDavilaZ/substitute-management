<?php

function validate_uploaded_file(array $file, array $allowedExtensions = [], array $allowedMimeTypes = [], int $maxBytes = 5242880): ?string 
{
    $err = $file['error'] ?? UPLOAD_ERR_NO_FILE;

    if ($err === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($err !== UPLOAD_ERR_OK) {

        return match ($err) {
            UPLOAD_ERR_INI_SIZE,
            UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño permitido.',
            default => 'Error al subir archivo.'
        };
    }

    if (($file['size'] ?? 0) > $maxBytes) {
        return 'Archivo demasiado grande.';
    }

    $tmp = $file['tmp_name'] ?? '';

    if ($tmp === '' || !is_uploaded_file($tmp)) {
        return 'Archivo inválido.';
    }

    $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));

    if (!empty($allowedExtensions) && !in_array($ext, $allowedExtensions, true)) {
        return 'Extensión no permitida.';
    }

    $mime = mime_content_type($tmp);

    if (!empty($allowedMimeTypes) && !in_array($mime, $allowedMimeTypes, true)) {
        return 'Tipo MIME no permitido.';
    }

    return null;
}
function upload_file(array $file, string $directory, string $prefix = '', ?string $customName = null): ?string {

    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    $ext = strtolower(
        pathinfo($file['name'], PATHINFO_EXTENSION)
    );

    if ($customName !== null && $customName !== '') {
        $filename = $customName . '.' . $ext;
    } else {
        $filename = uniqid($prefix, true) . '.' . $ext;
    }

    $path = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

    if (!is_uploaded_file($file['tmp_name']) || !move_uploaded_file($file['tmp_name'], $path)) {
        return null;
    }

    return $filename;
}

function delete_file(string $directory, ?string $filename): void 
{
    if ($filename === null || $filename === '') {
        return;
    }

    $base = basename($filename);

    if ($base !== str_replace(['/', '\\', "\0"], '', $filename)) {
        return;
    }

    $realDir = realpath($directory);

    if ($realDir === false || !is_dir($realDir)) {
        return;
    }

    $candidate = $realDir . DIRECTORY_SEPARATOR . $base;

    $realFile = realpath($candidate);

    if ($realFile !== false && is_file($realFile) && str_starts_with($realFile, $realDir . DIRECTORY_SEPARATOR)) {
        @unlink($realFile);
    }
}