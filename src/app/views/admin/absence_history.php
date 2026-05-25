<main class="main">
    <h1 class="text-title">Historial de ausencias</h1>

    <div id="absence-history-container">
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