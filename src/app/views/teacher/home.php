<?php 
    $teacher = $teacher ?? [];
?>

<div class="teacher-bg py-5">
    <div class="container">
        <div class="dashboard-card">
            <div class="row align-items-center">
                <div class="col-lg-4 text-center ">

                    <?php if (!empty($teacher['profile_img_path'])): ?>
                        <?php $imgSrc = (str_starts_with($teacher['profile_img_path'], 'http')) ? $teacher['profile_img_path'] : UPLOADS_URL . 'teachers/' . $teacher['profile_img_path']; ?>
                        <img src="<?= htmlspecialchars($imgSrc) ?>"
                            alt="Foto de perfil de <?= htmlspecialchars($teacher['full_name']) ?>"
                            class="profile-picture">
                    <?php else: ?>
                        <i class="bi bi-person-circle profile-picture profile-icon"></i>
                    <?php endif; ?>

                    <h1 class="welcome-title mt-4">
                        Bienvenido, <?= htmlspecialchars($teacher['full_name']) ?>
                    </h1>

                    <p class="welcome-time mb-0">
                        <span id="fecha"></span>
                        -
                        <span id="reloj"></span>
                    </p>
                </div>

                <div class="col-lg-8">

                    <div class="row">

                        <div class="col-sm-6 p-3">
                            <a class="dashboard-option center p-4" href="<?= url('teacher/substitutions') ?>">
                                <i class="bi bi-shield-check option-icon"></i>
                                <h5 class="option-title">Mis Guardias</h5>
                            </a>
                        </div>

                        <div class="col-sm-6 p-3">
                            <a class="dashboard-option center p-4" href="<?= url('teacher/schedule') ?>">
                                <i class="bi bi-calendar-week option-icon"></i>
                                <h5 class="option-title">Mi horario</h5>
                            </a>
                        </div>

                        <div class="col-sm-6 p-3">
                            <a class="dashboard-option center p-4" href="<?= url('teacher/absences') ?>">
                                <i class="bi bi-person-x option-icon"></i>
                                <h5 class="option-title">Mis ausencias</h5>
                            </a>
                        </div>

                        <div class="col-sm-6 p-3">
                            <a class="dashboard-option center p-4" href="<?= url('teacher/generate-absence') ?>">
                                <i class="bi bi-megaphone option-icon"></i>
                                <h5 class="option-title">Informar ausencia</h5>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    push_css('teacher/home.css');
    push_js('teacher/home.js');
?>