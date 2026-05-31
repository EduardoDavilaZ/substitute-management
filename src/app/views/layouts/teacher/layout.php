<!DOCTYPE html>
<html lang="es">
<head>
    <?php

    include_once('header.php');
    stack('styles');

    ?>
</head>
<body class="teacher-bg">
    <?php

    $headerLogo      = ASSETS_URL . 'img/logo.png';
    $headerAppNombre = 'Gestión de Guardias';
    $headerAppSub    = 'Escuela Virgen de Guadalupe';
    $headerHome      = url('teacher/home');
    $headerLogout    = url('auth/logout');

    include_once(VIEWS_PATH . 'layouts/evg/header.php');
    echo $content;
    include_once(VIEWS_PATH . 'layouts/evg/footer.php');
    stack('scripts'); 
    
    ?>
</body>
</html>