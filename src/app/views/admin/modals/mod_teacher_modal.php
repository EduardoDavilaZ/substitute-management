<?php
    $isEdit = !empty($selectedId);

    modal_start([
        'title' => $isEdit ? 'Editar Hora de Guardia' : 'Asignar Profesor a Guardia',
        'size' => 'modal-md',
    ]); 
?>

<?php 
    modal_end([
        [
            'text' => $isEdit ? 'Guardar Cambios' : 'Realizar Asignación', 
            'type' => 'button',
            'id'   => 'btnSubmitGuard',
            'attr' => ''
        ],
]); ?>