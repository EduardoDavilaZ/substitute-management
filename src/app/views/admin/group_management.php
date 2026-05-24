<main class="main">
    <h1 class="text-title">Gestión de grupos</h1>
    <span class="text-muted-custom">Administra los grupos de clase del centro</span>

    <button type="button" class="btn-create right-self my-2 py-2 px-4" id="btnNewGroup">
        <i class="bi bi-plus-square"></i> Nuevo grupo
    </button>

    <div id="groups-table-container" class="card-container p-4 p-md-5 mt-2 shadow-sm">
        <div class="table-responsive">
            <table id="groups-table" class="w-100">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th class="text-center">Etapa</th>
                        <th class="text-center">Modificar</th>
                        <th class="text-center">Eliminar</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</main>

<?php
    push_css('admin/group_management.css');
    push_js('admin/group_management.js');
?>
