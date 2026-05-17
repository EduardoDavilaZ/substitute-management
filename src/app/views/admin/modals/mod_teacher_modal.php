<?php
    $teacher = $teacher ?? [];
    $isEdit = !empty($teacher['id']);

    modal_start([
        'title' => $isEdit ? 'Editar datos del profesor' : 'Sin datos del profesor',
        'size' => 'modal-md',
    ]);
?>
<form id="formModTeacher" data-id="<?= $teacher['id'] ?? 0 ?>">
    <div class="mb-3">
        <label class="form-label small fw-bold">Nombre Completo</label>
        <input type="text" name="nameTeacher" class="form-control input-validate" value="<?= htmlspecialchars($teacher['full_name'] ?? '') ?>">
        <div class="error-message text-danger small mt-1"></div>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-bold">Correo Electrónico</label>
        <input type="email" name="emailTeacher" class="form-control input-validate" value="<?= htmlspecialchars($teacher['email'] ?? '') ?>">
        <div class="error-message text-danger small mt-1"></div>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-bold">Teléfono</label>
        <input type="tel" name="phoneTeacher" class="form-control input-validate" value="<?= htmlspecialchars($teacher['phone'] ?? '') ?>">
        <div class="error-message text-danger small mt-1"></div>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-bold">Imagen de Perfil</label>
        <?php if (!empty($teacher['profile_img_path'])): ?>
            <div class="mb-2">
                <img src="<?= ASSETS_URL . 'imgTeacher/' . htmlspecialchars($teacher['profile_img_path']) ?>" alt="Imagen de perfil" style="max-width: 100px; border-radius: 4px;">
            </div>
        <?php endif; ?>
        <input type="file" name="profileImage" class="form-control input-validate">
        <div class="error-message text-danger small mt-1"></div>
    </div>
</form>
<?php
    modal_end([
        [
            'text' => $isEdit ? 'Guardar Cambios' : 'Realizar cambios',
            'type' => 'button',
            'id'   => 'btnSubmitGuard',
            'attr' => ''
        ],
]); ?>