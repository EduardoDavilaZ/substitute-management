<?php 
    $currentId = $selectedId ?? 0; 
    $isEdit = ($currentId > 0);
    $data = $event ?? [];

    $periods  = $periods ?? [];
    $classes  = $classes ?? [];
    $teachers = $teachers ?? [];

    $title       = $data['title'] ?? '';
    $description = $data['description'] ?? '';
    $start       = $data['start_date'] ?? date('Y-m-d');
    $end         = $data['end_date'] ?? date('Y-m-d');
    
    $affectedClasses  = $data['affected_classes'] ?? [];
    $affectedTeachers = $data['affected_teachers'] ?? []; 

    $markedPeriods = [];
    foreach ($affectedClasses as $ac) {
        if (!empty($ac['affected_periods'])) {
            $pIds = array_column($ac['affected_periods'], 'id');
            $markedPeriods = array_merge($markedPeriods, $pIds);
        }
    }
    $markedPeriods = array_unique(array_map('intval', $markedPeriods));

    modal_start([
        'title' => $isEdit ? 'Editar Evento' : 'Crear Nuevo Evento',
        'size' => 'modal-xl',
    ]); 
?>

<form id="formEvent" data-id="<?= $currentId ?>">
    <div class="mb-3">
        <label class="form-label small fw-bold">Título del evento</label>
        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($title) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-bold">Descripción</label>
        <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($description) ?></textarea>
    </div>

    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label small fw-bold">Fecha Inicio</label>
            <input type="date" name="start_date" class="form-control" value="<?= $start ?>" required>
        </div>
        <div class="col-6 mb-3">
            <label class="form-label small fw-bold">Fecha Fin</label>
            <input type="date" name="end_date" class="form-control" value="<?= $end ?>" required>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label small fw-bold mb-2">Clases Afectadas</label>
            <div id="classesContainer" class="border rounded p-2 bg-light" style="min-height: 200px; max-height: 300px; overflow-y: auto;">
                <div id="currentClassesList">
                    <?php foreach($affectedClasses as $ac): if(isset($ac['id'])): ?>
                        <div class="d-flex align-items-center justify-content-between bg-white border rounded p-2 mb-2 class-selector-item">
                            <span class="small">
                                <i class="bi bi-mortarboard me-2"></i>
                                <strong><?= htmlspecialchars($ac['code'] ?? 'N/A') ?></strong>
                                <br>
                                <small class="text-muted"><?= htmlspecialchars($ac['name'] ?? '') ?></small>
                            </span>
                            <input type="hidden" name="class_ids[]" value="<?= $ac['id'] ?>">
                            <button type="button" class="btn btn-link text-danger p-0 btnRemoveClass" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    <?php endif; endforeach; ?>
                </div>
                <div id="newClassesSelectors"></div>
                <div class="mt-2 pt-2 border-top">
                    <button type="button" class="btn btn-sm w-100 btn-save" id="btnAddClassSelector">
                        <i class="bi bi-plus-circle me-1"></i> Añadir otra clase
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label class="form-label small fw-bold mb-2">Profesores Acompañantes</label>
            <div id="teachersContainer" class="border rounded p-2 bg-light" style="min-height: 200px; max-height: 300px; overflow-y: auto;">
                <div id="currentTeachersList">
                    <?php foreach($affectedTeachers as $at): if(isset($at['id'])): ?>
                        <div class="d-flex align-items-center justify-content-between bg-white border rounded p-2 mb-2 teacher-selector-item">
                            <span class="small">
                                <i class="bi bi-person me-2"></i>
                                <strong><?= htmlspecialchars($at['full_name'] ?? 'N/A') ?></strong>
                            </span>
                            <input type="hidden" name="teacher_ids[]" value="<?= $at['id'] ?>">
                            <button type="button" class="btn btn-link text-danger p-0 btnRemoveTeacher" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    <?php endif; endforeach; ?>
                </div>
                <div id="newTeachersSelectors"></div>
                <div class="mt-2 pt-2 border-top">
                    <button type="button" class="btn btn-sm w-100 btn-save" id="btnAddTeacherSelector">
                        <i class="bi bi-plus-circle me-1"></i> Añadir profesor
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <label class="form-label small fw-bold mb-2">Horas / Periodos</label>
            <div class="border rounded p-2 bg-white" style="min-height: 200px; max-height: 300px; overflow-y: auto;">
                <?php foreach($periods as $p): ?>
                    <?php $isChecked = in_array((int)$p['id'], $markedPeriods) ? 'checked' : ''; ?>
                    <div class="form-check small mb-1">
                        <input class="form-check-input" type="checkbox" name="period_ids[]" 
                            value="<?= $p['id'] ?>" 
                            id="p<?= $p['id'] ?>" 
                            <?= $isChecked ?>>
                        <label class="form-check-label" for="p<?= $p['id'] ?>">
                            <strong><?= htmlspecialchars($p['name']) ?></strong> 
                            <span class="text-muted">(<?= substr($p['start_time'], 0, 5) ?>)</span>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</form>

<!-- TEMPLATES -->
<template id="classSelectorTemplate">
    <div class="class-selector-item mb-2 shadow-sm border rounded bg-white p-2">
        <div class="selector-phase">
            <div class="input-group input-group-sm">
                <select class="form-select select-class-trigger">
                    <option value="">Seleccionar clase...</option>
                    <?php foreach($classes as $c): ?>
                        <option value="<?= $c['id'] ?>" data-code="<?= htmlspecialchars($c['code']) ?>" data-name="<?= htmlspecialchars($c['name']) ?>">
                            <?= $c['code'] ?> - <?= $c['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-outline-danger btnRemoveClass" type="button"><i class="bi bi-x-lg"></i></button>
            </div>
        </div>
        <div class="display-phase d-none d-flex align-items-center justify-content-between">
            <span class="small">
                <i class="bi bi-mortarboard me-2"></i><strong class="class-code-text"></strong>
                <br><small class="text-muted class-name-text"></small>
            </span>
            <input type="hidden" name="class_ids[]" value="" class="class-id-input">
            <button type="button" class="btn btn-link text-danger p-0 btnRemoveClass"><i class="bi bi-trash"></i></button>
        </div>
    </div>
</template>

<template id="teacherSelectorTemplate">
    <div class="teacher-selector-item mb-2 shadow-sm border rounded bg-white p-2">
        <div class="selector-phase">
            <div class="input-group input-group-sm">
                <select class="form-select select-teacher-trigger">
                    <option value="">Seleccionar profesor...</option>
                    <?php foreach($teachers as $t): ?>
                        <option value="<?= $t['id'] ?>" data-name="<?= htmlspecialchars($t['full_name']) ?>">
                            <?= $t['full_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-outline-danger btnRemoveTeacher" type="button"><i class="bi bi-x-lg"></i></button>
            </div>
        </div>
        <div class="display-phase d-none d-flex align-items-center justify-content-between">
            <span class="small">
                <i class="bi bi-person me-2"></i><strong class="teacher-name-text"></strong>
            </span>
            <input type="hidden" name="teacher_ids[]" value="" class="teacher-id-input">
            <button type="button" class="btn btn-link text-danger p-0 btnRemoveTeacher"><i class="bi bi-trash"></i></button>
        </div>
    </div>
</template>

<?php 
    modal_end([
        [
            'text' => $isEdit ? 'Actualizar Evento' : 'Crear Evento', 
            'type' => 'button',
            'id'   => 'btnSubmitEvent'
        ],
    ]); 
?>