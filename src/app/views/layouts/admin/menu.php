<nav class="navbar navbar-expand-lg navbar-dark bg-brand shadow-sm">
    <div class="container-fluid">
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav d-flex gap-1">
                <li class="nav-item">
                    <a 
                        class="nav-link rounded nav-custom-link px-3 <?= active_class('admin/home') ?>" 
                        href="<?= url('admin/home') ?>">
                        <i class="bi bi-house"></i> Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a 
                        class="nav-link rounded nav-custom-link px-3 <?= active_class('admin/substitution-schedule') ?>" 
                        href="<?= url('admin/substitution-schedule') ?>">
                        <i class="bi bi-journal-bookmark"></i> Libro de guardias
                    </a>
                </li>
                <li class="nav-item">
                    <a
                        class="nav-link rounded nav-custom-link px-3 <?= active_class('admin/substitution-management') ?>" 
                        href="<?= url('admin/substitution-management') ?>">
                        <i class="bi bi-repeat"></i> Sustituciones
                    </a>
                </li>
                <li class="nav-item">
                    <a 
                        class="nav-link rounded nav-custom-link px-3 <?= active_class('admin/substitution-calendar') ?>" 
                        href="<?= url('admin/substitution-calendar') ?>">
                        <i class="bi bi-calendar2-check"></i> Calendario
                    </a>
                </li>
                <li class="nav-item">
                    <a 
                        class="nav-link rounded nav-custom-link px-3 <?= active_class('admin/absence-history') ?>" 
                        href="<?= url('admin/absence-history') ?>">
                        <i class="bi bi-clock-history"></i> Historial de ausencias
                    </a>
                </li>
                <li class="nav-item">
                    <a 
                        class="nav-link rounded nav-custom-link px-3 <?= active_class('admin/event-management') ?>" 
                        href="<?= url('admin/event-management') ?>">
                        <i class="bi bi-bookmark-check"></i> Eventos
                    </a>
                </li>
                <li class="nav-item">
                    <a 
                        class="nav-link rounded nav-custom-link px-3 <?= active_class('admin/teacher-management') ?>" 
                        href="<?= url('admin/teacher-management') ?>">
                        <i class="bi bi-people"></i> Profesores
                    </a>
                </li>
                <li class="nav-item">
                    <a 
                        class="nav-link rounded nav-custom-link px-3 <?= active_class('admin/group-management') ?>" 
                        href="<?= url('admin/group-management') ?>">
                        <i class="bi bi-book"></i> Clases
                    </a>
                </li>
            </ul>

        </div>
    </div>
</nav>