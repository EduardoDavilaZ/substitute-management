<?php
    modal_start([
        'title' => 'Detalles de la ausencia',
        'size' => 'modal-lg',
    ]);
    $data = $data ?? [];
?>
<div class="detail-container p-4">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold text-secondary small">Profesor</label>
                            <p class="mb-0 fs-5"><?= $data['absent'] ?? 'N/A' ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-secondary small">Clase</label>
                            <p class="mb-0 fs-5"><?= $data['class'] ?? 'N/A' ?></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold text-secondary small">Fecha</label>
                            <p class="mb-0 fs-5"><?= $data['date_absence'] ?? 'N/A' ?></p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-secondary small">Hora</label>
                            <p class="mb-0 fs-5"><?= $data['name_hour'] ?? 'N/A' ?></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="fw-bold text-secondary small">Justificado</label>
                            <p class="mb-0">
                                <?php if(isset($data['justify']) && $data['justify'] == 1): ?>
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Sí</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><i class="bi bi-x-circle"></i> No</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold text-secondary small">Estado</label>
                            <p class="mb-0">
                                <?php
                                    $state = $data['state'] ?? 'N/A';
                                    $badgeClass = 'bg-secondary';
                                    if($state === 'CONFIRMADO') $badgeClass = 'bg-success';
                                    if($state === 'PENDIENTE') $badgeClass = 'bg-warning';
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= $state ?></span>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <label class="fw-bold text-secondary small">Razón</label>
                            <p class="mb-0 text-muted"><?= $data['reason'] ?? 'N/A' ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    modal_end([
        
    ]);
?>