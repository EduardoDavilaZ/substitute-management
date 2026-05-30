<?php
    include_once(VIEWS_PATH . '/layouts/teacher/menu.php');
    $periods = $periods ?? [];
?>

<main class="main absence-page">
    <div class="container-fluid px-0">
        <section class="absence-card mx-auto">

            <div class="absence-header">
                <div class="absence-heading">
                    <div class="absence-icon">
                        <i class="bi bi-megaphone"></i>
                    </div>
                    <div>
                        <h1 class="absence-title">Informar ausencia</h1>
                        <p class="absence-subtitle mb-0">
                            Prepara la comunicacion de una ausencia y el material para las horas afectadas.
                        </p>
                    </div>
                </div>
            </div>

            <form id="absence-form" action="<?= url('teacher/store_absence') ?>" method="post" enctype="multipart/form-data">
                <div class="row g-3 align-items-stretch">

                    <!-- COLUMNA 1: Todos los datos del formulario -->
                    <div class="col-12 col-lg-5">
                        <div class="form-section h-100">
                            <div class="section-heading">
                                <div>
                                    <h2 class="section-title">Datos de la ausencia</h2>
                                    <p class="section-text mb-0">Indica la fecha, motivo e instrucciones.</p>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-6">
                                    <label for="date" class="form-label">Fecha de ausencia</label>
                                    <input
                                        type="date"
                                        name="date"
                                        id="date"
                                        class="form-control form-custom">
                                </div>

                                <div class="col-6">
                                    <label for="absence-type" class="form-label">Tipo</label>
                                    <select id="absence-type" name="absence_type" class="form-select form-custom">
                                        <option value="">Selecciona...</option>
                                        <option value="medical">Medica</option>
                                        <option value="personal">Personal</option>
                                        <option value="training">Formacion</option>
                                        <option value="other">Otra</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label for="description" class="form-label">Motivo</label>
                                    <textarea
                                        name="description"
                                        id="description"
                                        rows="2"
                                        class="form-control form-custom"
                                        placeholder="Explica brevemente el motivo de la ausencia..."></textarea>
                                </div>

                                <div class="col-12">
                                    <label for="notes" class="form-label">Indicaciones para la guardia</label>
                                    <textarea
                                        name="notes"
                                        id="notes"
                                        rows="2"
                                        class="form-control form-custom"
                                        placeholder="Actividades, tareas, aula alternativa o detalles importantes..."></textarea>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Justificante <span class="text-muted fw-normal">(opcional)</span></label>
                                    <label for="justification" class="justificante-field">
                                        <input
                                            type="file"
                                            hidden
                                            id="justification"
                                            name="justification"
                                            accept=".pdf">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                        <span id="justification-label">Subir PDF justificante</span>
                                        <small>max. 5MB</small>
                                    </label>
                                    <button
                                        type="button"
                                        id="remove-justification"
                                        class="btn btn-outline-danger btn-sm w-100 mt-2 d-none">
                                        <i class="bi bi-x me-1"></i>Quitar archivo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- COLUMNA 2: Horas afectadas + Resumen -->
                    <div class="col-12 col-lg-7">
                        <div class="d-flex flex-column gap-3 h-100">

                            <div class="form-section flex-grow-1">
                                <div class="section-heading">
                                    <div>
                                        <h2 class="section-title">Horas afectadas</h2>
                                        <p class="section-text mb-0">Marca las franjas en las que no podras asistir.</p>
                                    </div>
                                </div>

                                <?php if (empty($periods)): ?>
                                    <div class="empty-periods">
                                        <i class="bi bi-calendar-x"></i>
                                        <p class="mb-0">No hay periodos disponibles para mostrar.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="periods-list">
                                        <?php foreach ($periods as $period): ?>
                                            <article class="period-row">
                                                <label class="period-row-check" for="period-<?= htmlspecialchars($period['id']) ?>">
                                                    <input
                                                        class="form-check-input check-hour"
                                                        type="checkbox"
                                                        value="<?= htmlspecialchars($period['id']) ?>"
                                                        id="period-<?= htmlspecialchars($period['id']) ?>"
                                                        name="periods[]">
                                                    <span class="period-badge"><?= htmlspecialchars($period['name']) ?></span>
                                                    <span class="period-time">
                                                        <?= substr($period['start_time'], 0, 5) ?> &ndash; <?= substr($period['end_time'], 0, 5) ?>
                                                    </span>
                                                </label>

                                                <div class="period-row-upload d-none">
                                                    <label for="material-<?= htmlspecialchars($period['id']) ?>" class="period-upload-btn">
                                                        <input
                                                            type="file"
                                                            multiple
                                                            hidden
                                                            class="material-input"
                                                            id="material-<?= htmlspecialchars($period['id']) ?>"
                                                            name="materials[<?= htmlspecialchars($period['id']) ?>][]"
                                                            accept=".pdf,.png,.jpg,.jpeg,.doc,.docx">
                                                        <i class="bi bi-paperclip"></i>
                                                        <span>Adjuntar</span>
                                                    </label>
                                                    <div class="uploaded-files"></div>
                                                </div>
                                            </article>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="side-block">
                                <h2 class="section-title mb-3">Resumen</h2>
                                <div class="summary-list">
                                    <div>
                                        <span>Fecha</span>
                                        <strong id="summary-date">Sin seleccionar</strong>
                                    </div>
                                    <div>
                                        <span>Horas seleccionadas</span>
                                        <strong id="selected-periods-count">0</strong>
                                    </div>
                                    <div>
                                        <span>Materiales adjuntos</span>
                                        <strong id="attached-files-count">0</strong>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <div class="absence-form-footer">
                    <a href="<?= url('teacher/home') ?>" class="btn btn-light">
                        Cancelar
                    </a>
                    <button type="submit" id="submit-absence" class="btn btn-custom">
                        <i class="bi bi-send me-2"></i>Enviar ausencia
                    </button>
                </div>
            </form>
        </section>
    </div>
</main>

<?php
    push_css('teacher/generate_absence.css');
    push_js('teacher/generate_absence.js');
?>
