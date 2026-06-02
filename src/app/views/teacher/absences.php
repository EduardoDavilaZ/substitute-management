<?php
    include_once(VIEWS_PATH . '/layouts/teacher/menu.php');

    $absences = $absences ?? [];
?>

<main class="main teacher-section-page">
    <div class="container-fluid px-0">
        <section class="teacher-panel mx-auto">
            <div class="teacher-panel-header">
                <div class="teacher-panel-icon">
                    <i class="bi bi-person-x"></i>
                </div>

                <div>
                    <h1 class="teacher-panel-title">Mis ausencias</h1>
                    <p class="teacher-panel-subtitle mb-0">
                        Revisa tus ausencias comunicadas y el estado de cobertura.
                    </p>
                </div>
            </div>

            <?php if (empty($absences)): ?>
                <div class="empty-state">
                    <i class="bi bi-clipboard-check"></i>
                    <h2>No tienes ausencias registradas</h2>
                    <p class="mb-0">Las ausencias que comuniques apareceran aqui.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive teacher-table-wrap">
                    <table class="table teacher-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Horas</th>
                                <th>Motivo</th>
                                <th>Justificante</th>
                                <th>Subir justificante</th>
                                <th>Guardias</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($absences as $absence): ?>
                                <?php
                                    $date = new DateTime($absence['date']);
                                    $pending = (int) ($absence['pending_substitutions'] ?? 0);
                                    $confirmed = (int) ($absence['confirmed_substitutions'] ?? 0);
                                    $cancelled = (int) ($absence['cancelled_substitutions'] ?? 0);
                                ?>

                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($date->format('d/m/Y')) ?></strong>
                                    </td>

                                    <td>
                                        <span class="period-list">
                                            <?= htmlspecialchars($absence['periods'] ?? '-') ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($absence['reason']) ?>
                                    </td>

                                    <td>
                                        <?php if ((int) $absence['is_justified'] === 1): ?>
                                            <span class="status-badge status-confirmado">Justificada</span>
                                        <?php else: ?>
                                            <span class="status-badge status-pendiente">Pendiente</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center">
                                        <?php if ((int) $absence['is_justified'] === 1): ?>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="Ya justificada">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-upload-file"
                                                    data-absence-id="<?= $absence['id'] ?>"
                                                    data-url="<?= url('teacher/upload-justification') ?>"
                                                    title="Subir justificante">
                                                <i class="bi bi-upload"></i>
                                            </button>
                                            <input type="file" accept=".pdf" hidden
                                                   class="d-none input-upload-file"
                                                   data-absence-id="<?= $absence['id'] ?>">
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <div class="coverage-summary">
                                            <span><?= $confirmed ?> confirmadas</span>
                                            <span><?= $pending ?> pendientes</span>
                                            <?php if ($cancelled > 0): ?>
                                                <span><?= $cancelled ?> canceladas</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>

<?php
    push_css('teacher/teacher_sections.css');
    push_js('teacher/absences.js');
?>
