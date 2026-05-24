<?php
    $teacher = $teacher ?? [];
    $isEdit = !empty($teacher['id']);
    $teacherId = (int) ($teacher['id'] ?? 0);

    modal_start([
        'title' => $isEdit
            ? 'Subir horario de ' . htmlspecialchars($teacher['full_name'])
            : 'Sin datos del profesor',
        'size'  => 'modal-md',
    ]);
?>

<form id="formUploadSchedule" enctype="multipart/form-data" data-teacher-id="<?= $teacherId ?>">
    <p class="small text-muted mb-3">
        Use la plantilla descargada para este profesor. Solo se admiten códigos de clase del desplegable (.xlsx).
    </p>
    <div class="mb-3">
        <label class="form-label small fw-bold">Archivo Excel</label>
        <input
            type="file"
            name="schedule"
            class="form-control"
            accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
            required
        >
    </div>
</form>

<?php
    modal_end([
        [
            'text' => $isEdit ? 'Importar horario' : 'Subir',
            'type' => 'button',
            'id'   => 'btnSubmitSchedule',
        ],
    ]);
?>
