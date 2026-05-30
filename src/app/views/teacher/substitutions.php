<?php
    include_once(VIEWS_PATH . '/layouts/teacher/menu.php');

    $substitutions = $substitutions ?? [];
?>

<main class="main teacher-section-page">
    <div class="container-fluid px-0">
        <section class="teacher-panel mx-auto">
            <div class="teacher-panel-header">
                <div class="teacher-panel-icon">
                    <i class="bi bi-shield-check"></i>
                </div>

                <div>
                    <h1 class="teacher-panel-title">Mis guardias</h1>
                    <p class="teacher-panel-subtitle mb-0">
                        Consulta las guardias que tienes asignadas como sustituto.
                    </p>
                </div>
            </div>

            <?php if (empty($substitutions)): ?>
                <div class="empty-state">
                    <i class="bi bi-calendar-check"></i>
                    <h2>No tienes guardias asignadas</h2>
                    <p class="mb-0">Cuando se te asigne una guardia aparecera en esta pantalla.</p>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($substitutions as $substitution): ?>
                        <?php
                            $status = strtolower((string) $substitution['status']);
                            $date = new DateTime($substitution['date']);
                        ?>

                        <div class="col-12 col-xl-6">
                            <article class="teacher-info-card">
                                <div class="card-topline">
                                    <span class="date-badge">
                                        <i class="bi bi-calendar-event"></i>
                                        <?= htmlspecialchars($date->format('d/m/Y')) ?>
                                    </span>

                                    <span class="status-badge status-<?= htmlspecialchars($status) ?>">
                                        <?= htmlspecialchars($substitution['status']) ?>
                                    </span>
                                </div>

                                <h2 class="card-title">
                                    <?= htmlspecialchars($substitution['class_name'] ?? 'Sin clase') ?>
                                </h2>

                                <div class="detail-grid">
                                    <div>
                                        <span>Hora</span>
                                        <strong>
                                            <?= htmlspecialchars($substitution['period_name']) ?>
                                            (<?= substr($substitution['start_time'], 0, 5) ?>-<?= substr($substitution['end_time'], 0, 5) ?>)
                                        </strong>
                                    </div>

                                    <div>
                                        <span>Profesor ausente</span>
                                        <strong><?= htmlspecialchars($substitution['absent_teacher']) ?></strong>
                                    </div>

                                    <div>
                                        <span>Codigo</span>
                                        <strong><?= htmlspecialchars($substitution['class_code'] ?? '-') ?></strong>
                                    </div>

                                    <div>
                                        <span>Etapa</span>
                                        <strong><?= htmlspecialchars($substitution['class_stage'] ?? '-') ?></strong>
                                    </div>
                                </div>

                                <?php if (!empty($substitution['reason'])): ?>
                                    <p class="card-note mb-0">
                                        <?= htmlspecialchars($substitution['reason']) ?>
                                    </p>
                                <?php endif; ?>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>

<?php
    push_css('teacher/teacher_sections.css');
?>
