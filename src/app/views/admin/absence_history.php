<main class="main">
    <h1 class="text-title">Historial de ausencias</h1>

    <div id="absence-history-container">
        <div class="filters-wrapper">
            <div class="filters-bar">
                <div class="filter-group search-group">
                    <i class="bi bi-search"></i>
                    <input type="text" id="search-teacher" placeholder="Buscar profesor...">
                </div>
                <div class="filter-group select-group">
                    <select name="justifies" id="justifies">
                        <option value="">Todas las justificaciones</option>
                        <option value="1">Justificadas</option>
                        <option value="0">Sin justificar</option>
                    </select>
                </div>
                <div class="filter-group date-group">
                    <input type="date" id="date-from" class="input-white" placeholder="dd/mm/aaaa">
                    <span class="date-separator">–</span>
                    <input type="date" id="date-to" class="input-white" placeholder="dd/mm/aaaa">
                </div>
                <div id="reset">
                    <button class="btn-cancel">Reiniciar filtros</button>
                </div>
            </div>
            <div class="results-info">
                <span id="record-count">0</span>&nbsp;<span>resultado(s)</span>
            </div>
        </div>
        <div class="table-responsive">
            <table id="absence-history-table">
                <thead>
                    <th>Profesor ausente</th>
                    <th class="text-center">Horas ausentes</th>
                    <th class="text-center">Fecha</th>
                    <th>Motivo</th>
                    <th class="text-center">Justificación</th>
                    <th class="text-center">Detalles</th>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php
    push_css('admin/absence_history.css');
    push_js('admin/datatables_export.js');
    push_js('admin/absence_history.js');
?>
