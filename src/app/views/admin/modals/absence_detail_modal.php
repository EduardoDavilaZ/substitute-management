<?php
    $rows   = $absence ?? [];
    $header = $rows[0] ?? [];

    $absent   = $header['absent']       ?? '—';
    $date     = $header['date_absence'] ?? '';
    $reason   = $header['reason']       ?? '—';
    $justify  = $header['justify']      ?? 0;
    $material = $header['material']     ?? null;

    modal_start(['title' => 'Detalle de ausencia', 'size' => 'modal-lg']);
?>

<div class="absence-detail">

    <div class="absence-detail-header">
        <div class="absence-detail-row">
            <span class="absence-detail-label">Profesor ausente</span>
            <span class="absence-detail-value"><?= htmlspecialchars($absent) ?></span>
        </div>
        <div class="absence-detail-row">
            <span class="absence-detail-label">Fecha</span>
            <span class="absence-detail-value">
                <?= $date ? htmlspecialchars(date('d/m/Y', strtotime($date))) : '—' ?>
            </span>
        </div>
        <div class="absence-detail-row">
            <span class="absence-detail-label">Motivo</span>
            <span class="absence-detail-value"><?= htmlspecialchars($reason) ?></span>
        </div>
        <div class="absence-detail-row">
            <span class="absence-detail-label">Justificación</span>
            <span class="absence-detail-value">
                <?php if ($justify == 1): ?>
                    <span class="badge bg-success">Justificada</span>
                <?php else: ?>
                    <span class="badge bg-danger">Sin justificar</span>
                <?php endif; ?>
            </span>
        </div>
        <?php if (!empty($material)): ?>
        <div class="absence-detail-row">
            <span class="absence-detail-label">Justificante</span>
            <span class="absence-detail-value">
                <a href="<?= ASSETS_URL . 'uploads/' . htmlspecialchars($material) ?>" target="_blank" class="absence-detail-file">
                    <i class="bi bi-file-earmark-text"></i> Ver archivo
                </a>
            </span>
        </div>
        <?php endif; ?>
    </div>

    <?php if (!empty($rows)): ?>
    <div class="absence-detail-periods">
        <h6 class="absence-detail-section-title">Horas afectadas</h6>
        <?php foreach ($rows as $i => $row):
            $state = $row['state'] ?? '';
            $badgeClass = 'bg-secondary';
            if ($state === 'CONFIRMADO') $badgeClass = 'bg-success';
            if ($state === 'PENDIENTE')  $badgeClass = 'bg-warning';
        ?>
            <?php if ($i > 0): ?><hr class="absence-detail-divider"><?php endif; ?>
            <div class="absence-detail-row">
                <span class="absence-detail-label">Hora</span>
                <span class="absence-detail-value"><?= htmlspecialchars($row['name_hour'] ?? '—') ?></span>
            </div>
            <div class="absence-detail-row">
                <span class="absence-detail-label">Clase</span>
                <span class="absence-detail-value"><?= htmlspecialchars($row['class'] ?? '—') ?></span>
            </div>
            <div class="absence-detail-row">
                <span class="absence-detail-label">Estado</span>
                <span class="absence-detail-value">
                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($state) ?></span>
                </span>
            </div>
            <div class="absence-detail-row">
                <span class="absence-detail-label">Sustituto</span>
                <span class="absence-detail-value">
                    <?= !empty($row['sustitute']) ? htmlspecialchars($row['sustitute']) : '<i class="text-muted">(no asignado)</i>' ?>
                </span>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>

<?php modal_end([]); ?>