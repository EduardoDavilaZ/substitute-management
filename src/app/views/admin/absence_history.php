<?php $absences = $absences ?? []; ?>

<main class="main">
    <h1 class="text-title">Historial de ausencias</h1>

    <div class="right">
        <a href="<?= BASE_URL ?>absence/export-excel" class="btn btn-excel mx-1">
            <i class="bi bi-file-earmark-spreadsheet"></i> Exportar a Excel
        </a>
        <a href="<?= BASE_URL ?>absence/export-pdf" class="btn btn-pdf mx-1">
            <i class="bi bi-file-earmark-pdf"></i> Descargar pdf
        </a>
    </div>

    <div id="absences-table-container">

        <div id="table-controls">
            <div class="controls-left">
                <label>
                    Mostrar
                    <select id="page-size">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="-1">Todos</option>
                    </select>
                    registros
                </label>
            </div>
            <div class="controls-right">
                <select id="filter-justified">
                    <option value="">Justificada: Todas</option>
                    <option value="1">Justificada: Sí</option>
                    <option value="0">Justificada: No</option>
                </select>
                <input type="text" id="filter-search" placeholder="Buscar...">
            </div>
        </div>

        <table id="absences-table">
            <thead>
                <tr>
                    <th>Profesor</th>
                    <th>Fecha</th>
                    <th>Cant. Horas</th>
                    <th>Motivo</th>
                    <th>Justificada</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($absences)) { ?>
                    <tr>
                        <td colspan="5" class="text-center">No hay ausencias registradas</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($absences as $absence) { ?>
                        <tr>
                            <td><?= $absence['full_name'] ?></td>
                            <td><?= $absence['date'] ?></td>
                            <td><?= $absence['total_periods'] ?></td>
                            <td><?= $absence['reason'] ?></td>
                            <td>
                                <?php if ($absence['is_justified']) { ?>
                                    <span class="badge-status badge-green">Sí</span>
                                <?php } else { ?>
                                    <span class="badge-status badge-red">No</span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>

        <div id="table-info"></div>

    </div>
</main>

<?php
    push_css('admin/absence_history.css');
    push_js('admin/absence_history.js');
?>
