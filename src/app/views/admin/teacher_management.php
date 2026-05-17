<main class="main">
    <h1 class="text-title">Gestión de profesores</h1>
    <div id="teachers-table-container">
        <div class="table-responsive">
            <table id="teachers-table">
                <thead>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th class="text-center">Telefono</th>
                    <th class="text-center">Sustituciones realizadas</th>
                    <th class="text-center">Modificar</th>
                    <th class="text-center">Eliminar</th>
                    <th class="text-center">Cargar Horario</th>
                <!-- <th class="text-center">Tutor</th> -->
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php
    push_css('admin/teacher_management.css?v=1.2');
    push_js('admin/datatables_export.js');
    push_js('admin/management_teachers.js');
?>