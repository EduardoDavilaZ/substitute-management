<main class="main">
    <h1 class="text-title">Gestión de ausencias y sustituciones</h1>

    <div id="substitutions-table-container">
        <div class="filters-wrapper">
            <div class="filters-bar">
                <div class="filter-group search-group">
                    <i class="bi bi-search"></i>
                    <input type="text" id="search-teacher" placeholder="Buscar profesor...">
                </div>
                <div class="filter-group date-group">
                    <input type="date" id="date-from">
                    <span class="date-separator">–</span>
                    <input type="date" id="date-to">
                </div>
            </div>
            <div class="results-info">
                <span id="record-count">0</span> resultado(s)
            </div>
        </div>

        <div class="table-responsive">
            <table id="substitutions-table">
                <thead>
                    <th>Profesor ausente</th>
                    <th>Clase</th>
                    <th class="text-center">Fecha</th>
                    <th class="text-center">Hora</th>
                    <th class="text-center">Justificación</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Sustituto</th>
                    <th class="text-center">Detalles de ausencia</th>
                    <th class="text-center">Asignar sustituto</th>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php
    push_css('admin/managementAbsences.css');
    push_js('admin/managementAbsences.js');
?>