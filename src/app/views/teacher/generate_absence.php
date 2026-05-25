<?php
    include_once(VIEWS_PATH . '/layouts/teacher/menu.php');
    $periods = $periods = [];
?>

<main class="main absence-page">
    <div class="container-fluid px-0">
        <div class="absence-card mx-auto">

            <div class="absence-header mb-4">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="absence-icon">
                        <i class="bi bi-calendar-x"></i>
                    </div>

                    <div>
                        <h1 class="absence-title mb-1">Informar ausencia</h1>
                        <p class="absence-subtitle mb-0">
                            Comunica tu ausencia y adjunta material para el profesorado de guardia.
                        </p>
                    </div>
                </div>
            </div>

            <form id="absence-form" enctype="multipart/form-data">

                <div class="row g-4">

                    <div class="col-12 col-lg-6">
                        <label for="date" class="form-label">
                            Fecha de ausencia *
                        </label>

                        <input
                            type="date"
                            name="date"
                            id="date"
                            class="form-control form-custom">
                    </div>

                    <div class="col-12">
                        <div class="hours-section">

                            <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                                <div>
                                    <h2 class="section-title mb-1">
                                        Horas afectadas
                                    </h2>

                                    <p class="section-text mb-0">
                                        Marca las horas en las que no podrás asistir.
                                    </p>
                                </div>
                            </div>

                            <div class="row g-3">

                                <?php foreach ($periods as $period): ?>

                                    <div class="col-12 col-md-6">
                                        <div class="hour-card">

                                            <div class="form-check hour-check">
                                                <input
                                                    class="form-check-input check-hour"
                                                    type="checkbox"
                                                    value="<?= $period['id'] ?>"
                                                    id="period-<?= $period['id'] ?>"
                                                    name="periods[]">

                                                <label
                                                    class="form-check-label w-100"
                                                    for="period-<?= $period['id'] ?>">

                                                    <div class="hour-content">
                                                        <div class="hour-badge">
                                                            <?= htmlspecialchars($period['name']) ?>
                                                        </div>

                                                        <div class="hour-info">
                                                            <h3>
                                                                <?= substr($period['start_time'], 0, 5) ?>
                                                                -
                                                                <?= substr($period['end_time'], 0, 5) ?>
                                                            </h3>

                                                            <p class="mb-0">
                                                                Hora lectiva
                                                            </p>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>

                                            <div class="material-upload d-none">

                                                <label
                                                    for="material-<?= $period['id'] ?>"
                                                    class="upload-box">

                                                    <input
                                                        type="file"
                                                        multiple
                                                        hidden
                                                        class="material-input"
                                                        id="material-<?= $period['id'] ?>"
                                                        name="materials[<?= $period['id'] ?>][]"
                                                        accept=".pdf,.png,.jpg,.jpeg">

                                                    <div class="upload-content">
                                                        <i class="bi bi-cloud-arrow-up"></i>

                                                        <p class="upload-title mb-1">
                                                            Subir material
                                                        </p>

                                                        <span class="upload-text">
                                                            PDF, imágenes, documentos...
                                                        </span>
                                                    </div>
                                                </label>

                                                <div class="uploaded-files mt-2"></div>
                                            </div>

                                        </div>
                                    </div>

                                <?php endforeach; ?>

                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">
                            Motivo de la ausencia *
                        </label>

                        <textarea
                            name="description"
                            id="description"
                            rows="4"
                            class="form-control form-custom"
                            placeholder="Explica el motivo principal de la ausencia..."></textarea>
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label">
                            Indicaciones para guardia
                        </label>

                        <textarea
                            name="notes"
                            id="notes"
                            rows="4"
                            class="form-control form-custom"
                            placeholder="Actividades, instrucciones o detalles importantes..."></textarea>
                    </div>

                    <div class="col-12">

                        <div class="general-upload">

                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <div>
                                    <h2 class="section-title mb-1">
                                        Justificante general
                                    </h2>

                                    <p class="section-text mb-0">
                                        Puedes adjuntar un justificante médico o documento oficial.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    id="remove-justification"
                                    class="btn btn-outline-danger btn-sm">
                                    Eliminar archivo
                                </button>
                            </div>

                            <label for="justification" class="upload-box upload-general">

                                <input
                                    type="file"
                                    hidden
                                    id="justification"
                                    name="justification"
                                    accept=".pdf">

                                <div class="upload-content">
                                    <i class="bi bi-file-earmark-pdf"></i>

                                    <p class="upload-title mb-1">
                                        Haz clic para subir un justificante
                                    </p>

                                    <span class="upload-text">
                                        PDF · Máximo 5MB
                                    </span>
                                </div>
                            </label>

                        </div>
                    </div>

                    <div class="col-12">
                        <div class="info-box">
                            <i class="bi bi-info-circle"></i>

                            <p class="mb-0">
                                Si no adjuntas justificante ahora, la ausencia quedará pendiente de justificar.
                            </p>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-end flex-wrap gap-2">
                            <button
                                type="button"
                                class="btn btn-light px-4">
                                Cancelar
                            </button>

                            <button
                                type="submit"
                                class="btn btn-custom px-4">
                                Enviar notificación
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</main>

<?php
    push_css('teacher/generate_absence.css');
    push_js('teacher/generate_absence.js');
?>