
<?php
    
    modal_start([
        'title' => 'Asignar profesor sustituto',
        'size' => 'modal-md',
    ]);
    
    $substitution = $substitution ?? [];
    $teachersDay = $teachersDay ?? [];
    var_dump($substitution);
    $name = $substitution['absent_teacher_id'] ?? 'Profesor no identificado';
    $date = $substitution['date'] ?? 'Fecha no disponible';
    $teacherFree = [];
?>
<p>Ausencia de <?= htmlspecialchars($name) ?> el <?= htmlspecialchars($date) ?></p>

<form action="" method="POST">
    <label for="teacherToday">Profesores asignados para esta hora</label>
    <select name="teacherToday" id="teacherToday">
        <?php foreach($teachersDay as $teacher): ?>
            <option value="<?= $teacher['teacher_id'] ?>"> <?= $teacher['full_name'] ?> </option>
        <?php endforeach; ?>
    </select>

    <label for="teacherFree">Profesores libres para esta hora</label>
    <select name="teacherFree" id="teacherFree">
        <?php foreach($teacherFree as $tFree): ?>
            <option value="<?= $tFree['id'] ?>"> <?= $tFree['nombre'] ?> </option>
        <?php endforeach; ?>
    </select>
</form>
<?php
    modal_end([
        [
            'text' => 'Realizar Asignación', 
            'type' => 'button',
            'id'   => 'btnSubmitAssig',
            'attr' => ''
        ],
    ]);
?>