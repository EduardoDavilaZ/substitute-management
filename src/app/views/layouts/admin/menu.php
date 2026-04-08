<nav class="navbar navbar-expand-lg navbar-dark bg-brand shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand rounded-2 fw-bold w-50px" href="https://fundacionloyola.com/vguadalupe/">
            <?= img('isotype.png', 'logo', 'w-100') ?>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav d-flex gap-1">
                <li class="nav-item">
                    <a 
                        class="nav-link rounded nav-custom-link px-3 <?= active_class('admin/home') ?>" 
                        href="<?= url('admin/home') ?>">
                        Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a 
                        class="nav-link rounded nav-custom-link px-3 <?= active_class('admin/substitution-schedule') ?>" 
                        href="<?= url('admin/substitution-schedule') ?>">
                        Libro de guardias
                    </a>
                </li>
                <li class="nav-item">
                    <a 
                        class="nav-link rounded nav-custom-link px-3 <?= active_class('admin/daily-substitutions') ?>" 
                        href="<?= url('admin/daily-substitutions') ?>">
                        Guardias de hoy
                    </a>
                </li>
                <li class="nav-item">
                    <a 
                        class="nav-link rounded nav-custom-link px-3 <?= active_class('admin/teacher-management') ?>" 
                        href="<?= url('admin/teacher-management') ?>">
                        Profesores
                    </a>
                </li>
                <li class="nav-item">
                    <a 
                        class="nav-link rounded nav-custom-link px-3 <?= active_class('admin/group-management') ?>" 
                        href="<?= url('admin/group-management') ?>">
                        Clases
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <button type="submit" class="nav-link btn btn-link border-0 shadow-none text-white fw-medium px-3 rounded nav-custom-link">
                        <i class="bi bi-arrow-right-square"></i> Cerrar sesión
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>