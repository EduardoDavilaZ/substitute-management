<main class="main">
    <h1 class="text-title">Gestión de ausencias y sustituciones</h1>

    <div id="substitutions-table-container">
        
        <div class="filters-wrapper">
            <div class="filters-bar">
                
                <div class="filter-group search-group">
                    <i class="bi bi-search"></i>
                    <input type="text" id="search-teacher" placeholder="Buscar profesor...">
                </div>
                
                <div class="filter-group select-group">
                    <select name="states" id="states">
                        <option value="">Todos los estados</option>
                        <option value="CONFIRMADO">Confirmados</option>
                        <option value="PENDIENTE">Pendientes</option>
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
            <table id="substitutions-table">
                <thead>
                    <tr>
                        <th>Profesor ausente</th>
                        <th>Clase</th>
                        <th class="text-center">Fecha</th>
                        <th class="text-center">Hora</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Sustituto</th>
                        <th class="text-center">Detalles de ausencia</th>
                        <th class="text-center">Asignar sustituto</th>
                        <th class="text-center">Eliminar ausencia</th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
            </table>
        </div>
    </div>
</main>

<?php
    push_css('admin/substitution_management.css');
    push_js('admin/datatables_export.js');
    push_js('admin/substitution_management.js');
?>