<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">

<title>Portal Docente | Gestión de Ausencias y Guardias</title>

<meta name="description" content="Plataforma de comunicación de ausencias y gestión de guardias para el profesorado.">
<meta name="author" content="Fundación Loyola">
<meta name="robots" content="noindex, nofollow">

<meta property="og:title" content="Portal Docente - Panel de Gestión">
<meta property="og:description" content="Accede para comunicar ausencias y consultar tus guardias asignadas.">
<meta property="og:image" content="<?= asset_url('logo.png', 'img') ?>">
<meta property="og:url" content="<?= url('') ?>">
<meta property="og:type" content="website">

<?= js('jquery/jquery-4.0.0.js', 'vendor') ?>
<?= js('bootstrap/bootstrap.bundle.min.js', 'vendor') ?>
<?= js('sweetalert/sweetalert.min.js', 'vendor') ?>

<?= js('app.js') ?>
<?= css('app.css') ?>

<?php stack('styles') ?>

<script>
    const BASE_URL   = "<?php echo BASE_URL; ?>";
    const ASSETS_URL = "<?php echo ASSETS_URL; ?>";
    const JS_URL    = "<?php echo JS_URL; ?>";
    const CSS_URL   = "<?php echo CSS_URL; ?>";
</script>