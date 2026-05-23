<main class="main">
    <h1 class="text-title">Gestión de profesores</h1>
    <div id="teachers-table-container" class="card-container p-4 p-md-5 mt-2 shadow-sm">
        <div class="table-responsive">
            <table id="teachers-table" class="w-100">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th class="text-center">Teléfono</th>
                        <th class="text-center">Sustituciones</th>
                        <th class="text-center">Tutor</th>
                        <th class="text-center">Modificar</th>
                        <th class="text-center">Eliminar</th>
                        <th class="text-center">Cargar Horario</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php
    push_css('admin/teacher_management.css');
    push_js('admin/datatables_export.js');
    push_js('admin/management_teachers.js');
?>