<?php
    $isEdit = !empty($selectedId);

    modal_start([
        'title' => $isEdit ? 'Editar Hora de Guardia' : 'Asignar Profesor a Guardia',
        'size' => 'modal-md',
    ]); 
?>

<?php
    $selectedId = $selectedId ?? null;
    $teachers   = $teachers ?? [];
    $infoText   = $infoText ?? 'Información de horario no disponible';
    $day        = $day ?? '';
    $period     = $period ?? 0;
    $selectedTeacherId = $selectedTeacherId ?? null;
    $isEdit = !empty($selectedId);
?>

<form id="formAddGuardPeriod" data-id="<?= $selectedId ?? 0 ?>" data-day="<?= $day ?>" data-period="<?= $period ?>">
    <div class="my-2">
        <div class="mb-3">
            <span class="text-muted small"><?= htmlspecialchars($infoText) ?></span>
        </div>
        
        <label for="teacherSelect" class="form-label small fw-bold">Profesor</label>
        <select class="form-select" name="teacher_id" id="teacherSelect" required>
            <option value="" <?= !$isEdit ? 'selected' : '' ?> disabled>
                -- Seleccionar profesor --
            </option>
            
            <?php foreach ($teachers as $t): ?>
                <option value="<?= $t['id'] ?>" 
                    <?= ($isEdit && $selectedTeacherId == $t['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t['full_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</form>

<?php 
    modal_end([
        [
            'text' => $isEdit ? 'Guardar Cambios' : 'Realizar Asignación', 
            'type' => 'button',
            'id'   => 'btnSubmitGuard',
            'attr' => ''
        ],
]); ?>