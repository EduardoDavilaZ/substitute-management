<nav class="top-nav">
    <a href="<?= url('teacher/home') ?>" class="nav-pill">
        <i class="bi bi-house-door"></i>
        <span>Inicio</span>
    </a>
    <a href="<?= url('teacher/substitutions') ?>" class="nav-pill <?= active_class('teacher/substitutions') ?>">
        <i class="bi bi-shield-check"></i>
        <span>Mis Guardias</span>
    </a>
    <a href="<?= url('teacher/schedule') ?>" class="nav-pill <?= active_class('teacher/schedule') ?>">
        <i class="bi bi-calendar-week"></i>
        <span>Mi Horario</span>
    </a>
    <a href="<?= url('teacher/absences') ?>" class="nav-pill <?= active_class('teacher/absences') ?>">
        <i class="bi bi-person-x"></i>
        <span>Mis Ausencias</span>
    </a>
    <a href="<?= url('teacher/generate_absence') ?>" class="nav-pill <?= active_class('teacher/generate_absence') ?>">
        <i class="bi bi-megaphone"></i>
        <span>Informar Ausencia</span>
    </a>
</nav>