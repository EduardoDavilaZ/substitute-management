<?php
    $currentId = $selectedId ?? 0; 
    $isEdit = ($currentId > 0);
    
    // Aseguramos que $event sea un array
    $data = (array) ($event ?? []);

    $periods = $periods ?? [];
    $classes = $classes ?? [];

    // 1. Datos básicos
    $title       = $data['title'] ?? '';
    $description = $data['description'] ?? '';
    $start       = $data['start_date'] ?? date('Y-m-d');
    $end         = $data['end_date'] ?? date('Y-m-d');
    
    // 2. Clases (usamos 'id' porque queryNested quita el prefijo 'class_')
    $affectedClasses = $data['affected_classes'] ?? [];

    // 3. Periodos (Preparamos los IDs marcados AQUÍ para que no den error luego)
    $markedPeriods = [];
        if (!empty($data['affected_periods'])) {
            // Extraemos la columna 'id' y convertimos cada valor a entero (int)
            $markedPeriods = array_map('intval', array_column($data['affected_periods'], 'id'));
        }

    modal_start([
        'title' => $isEdit ? 'Editar Evento' : 'Crear Nuevo Evento',
        'size' => 'modal-lg',
    ]); 
?>

<form id="formEvent" data-id="<?= $currentId ?>">
    <div class="mb-3">
        <label class="form-label small fw-bold">Título del evento</label>
        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($title) ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-bold">Descripción</label>
        <textarea name="description" class="form-control" rows="2" required><?= htmlspecialchars($description) ?></textarea>
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
    <!-- Columna de Clases -->
    <div class="col-md-6 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label small fw-bold mb-0">Clases Afectadas</label>
        </div>
        
        <!-- Contenedor principal de clases -->
        <div id="classesContainer" class="border rounded p-2 bg-light" style="min-height: 100px;">
            
            <!-- 1. Lista de clases que YA están (Edición) -->
            <div id="currentClassesList">
                <?php if (!empty($affectedClasses)): ?>
                    <?php foreach($affectedClasses as $ac): ?>
                        <div class="d-flex align-items-center justify-content-between bg-white border rounded p-2 mb-2 class-selector-item">
                            <span class="small">
                                <i class="bi bi-mortarboard me-2"></i>
                                <!-- Cambiado de $ac['class_code'] a $ac['code'] -->
                                <strong><?= htmlspecialchars($ac['code'] ?? 'Sin código') ?></strong>
                            </span>
                            <!-- Cambiado de $ac['class_id'] a $ac['id'] -->
                            <input type="hidden" name="class_ids[]" value="<?= $ac['id'] ?>">
                            
                            <button type="button" class="btn btn-link text-danger p-0 btnRemoveClass" title="Quitar clase">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div id="newClassesSelectors"></div>

            <!-- 3. Botón/Selector para añadir -->
            <div class="mt-2 pt-2 border-top">
                <button type="button" class="btn btn-sm btn-outline-primary w-100" id="btnAddClassSelector">
                    <i class="bi bi-plus-circle me-1"></i> Añadir otra clase
                </button>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
    <label class="form-label small fw-bold mb-2">Horas / Periodos</label>
    <div class="border rounded p-2" style="max-height: 250px; overflow-y: auto; background: #fff;">
        <?php 
            $markedPeriods = [];
            if (!empty($data['affected_periods'])) {
                $markedPeriods = array_column($data['affected_periods'], 'period_id');
                
                // Limpiamos y convertimos a enteros
                $markedPeriods = array_unique(array_map('intval', array_filter($markedPeriods)));
            }
        ?>

        <?php foreach($periods as $p): ?>
            <?php 
                $isChecked = in_array((int)$p['id'], $markedPeriods) ? 'checked' : ''; 
            ?>
            <div class="form-check small">
                <input class="form-check-input" type="checkbox" name="period_ids[]" 
                    value="<?= $p['id'] ?>" 
                    id="p<?= $p['id'] ?>" 
                    <?= $isChecked ?>>
                <label class="form-check-label" for="p<?= $p['id'] ?>">
                    <strong><?= htmlspecialchars($p['name']) ?></strong> 
                    <span class="text-muted">(<?= $p['start_time'] ?>)</span>
                </label>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</div>

<template id="classSelectorTemplate">
    <div class="input-group mb-2 class-selector-item shadow-sm">
        <select name="class_ids[]" class="form-select form-select-sm">
            <option value="">Seleccionar clase...</option>
            <?php foreach($classes as $c): ?>
                <option value="<?= $c['id'] ?>"><?= $c['code'] ?> - <?= $c['name'] ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-outline-danger btn-sm btnRemoveClass" type="button">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
</template>

<?php 
    modal_end([
        [
            'text' => $isEdit ? 'Actualizar Evento' : 'Crear Evento', 
            'type' => 'button',
            'id'   => 'btnSubmitEvent',
            'attr' => ''
        ],
    ]); 
?>