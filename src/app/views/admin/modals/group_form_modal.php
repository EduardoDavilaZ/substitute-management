<?php
    $currentId = $selectedId ?? 0;
    $isEdit = ($currentId > 0);
    $data = $classData ?? [];
    $stages = $stages ?? Classes::validStages();

    $code  = $data['code'] ?? '';
    $name  = $data['name'] ?? '';
    $stage = $data['stage'] ?? '';

    modal_start([
        'title' => $isEdit ? 'Editar grupo' : 'Nuevo grupo',
        'size'  => 'modal-md',
    ]);
?>

<form id="formGroup" data-id="<?= (int) $currentId ?>">
    <div class="mb-3">
        <label class="form-label small fw-bold">Código</label>
        <input
            type="text"
            name="code"
            class="form-control input-validate"
            maxlength="10"
            value="<?= htmlspecialchars($code) ?>"
            placeholder="Ej: 2-DAW"
            required
        >
    </div>

    <div class="mb-3">
        <label class="form-label small fw-bold">Nombre</label>
        <input
            type="text"
            name="name"
            class="form-control input-validate"
            maxlength="50"
            value="<?= htmlspecialchars($name) ?>"
            placeholder="Ej: 2º Des. Aplicaciones Web"
            required
        >
    </div>

    <div class="mb-3">
        <label class="form-label small fw-bold">Etapa</label>
        <select name="stage" class="form-select input-validate" required>
            <option value="">Seleccionar etapa...</option>
            <?php foreach ($stages as $s): ?>
                <option value="<?= $s ?>" <?= $stage === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</form>

<?php
    modal_end([
        [
            'text' => $isEdit ? 'Guardar cambios' : 'Crear grupo',
            'type' => 'button',
            'id'   => 'btnSubmitGroup',
        ],
    ]);
?>
