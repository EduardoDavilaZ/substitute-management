<?php
    $teacher = $teacher ?? [];
    $isEdit = !empty($teacher['id']);

    modal_start([
        'title' => $isEdit ? 'Agrega el horario de '.$teacher['full_name'] : 'Sin datos del profesor',
        'size' => 'modal-md',
    ]);
?>
<form action="">
    <input type="file" name="schedule" class="form-control input-validate">
</form>
<?php
    modal_end([
        [
            'text' => $isEdit ? 'Guardar Horario' : 'Realizar cambios',
            'type' => 'button',
            'id'   => 'btnSubmitSchedule',
            'attr' => ''
        ],
]); ?>