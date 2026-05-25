
<?php
    
    modal_start([
        'title' => 'Asignar profesor sustituto',
        'size' => 'modal-md',
    ]);
    
    $substitution = $substitution ?? [];
    $teachersDay = $teachersDay ?? [];
    $teachersFree = $teachersFree ?? [];
    $name = $substitution['absent'] ?? 'Profesor no identificado';
    $date = $substitution['date'] ?? 'Fecha no disponible';
    
?>
<p>Ausencia de <strong><?= htmlspecialchars($name) ?></strong><br> Dia: <strong><?= htmlspecialchars($date) ?></strong></p>

<form action="" method="POST" id="formAssign">
    <input type="hidden" name="idSubstitution" value="<?= $substitution['id']?>">
    <div class="mb-3">
        <label for="teacherToday" class="form-label small fw-bold">Profesores asignados para esta hora</label>
        <select name="teacherToday" id="teacherToday" class="form-select form-control input-validate">
            <option value="">-- Seleccionar profesor --</option>
            <?php foreach($teachersDay as $teacher): ?>
                <option value="<?= $teacher['teacher_id'] ?>"> <?= $teacher['teacher_name'] ?> (<?=  $teacher['counter']?>) </option>
            <?php endforeach; ?>
        </select>
        <div class="error-message text-danger small mt-1"></div>
    </div>

    <div class="mb-3">
        <label for="teacherFree" class="form-label small fw-bold">Profesores libres para esta hora</label>
        <select name="teacherFree" id="teacherFree" class="form-select form-control input-validate">
            <option value="">-- Seleccionar profesor --</option>
            <?php foreach($teachersFree as $tFree): ?>
                <option value="<?= $tFree['teacher_id'] ?>"> <?= $tFree['teacher_name'] ?> (<?=  $tFree['counter']?>) </option>
            <?php endforeach; ?>
        </select>
        <div class="error-message text-danger small mt-1"></div>
    </div>
</form>
<?php
    modal_end([
        [
            'text' => 'Realizar Asignación', 
            'type' => 'submit',
            'id'   => 'btnSubmitAssig',
            'attr' => ''
        ],
    ]);
?>