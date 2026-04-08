<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">

<title>Gestión de Guardias</title>

<meta name="description" content="Sistema de gestión de guardias y horarios para profesores.">
<meta name="author" content="Fundación Loyola">
<meta name="robots" content="noindex, nofollow">

<meta property="og:title" content="Gestión de Guardias - Panel de Control">
<meta property="og:description" content="Accede al sistema para consultar tus horarios y guardias asignadas.">
<meta property="og:image" content="<?= asset_url('logo.png', 'img') ?>">
<meta property="og:url" content="<?= url('') ?>">
<meta property="og:type" content="website">

<?= js('jquery/jquery-4.0.0.js', 'vendor') ?>
<?= js('bootstrap/bootstrap.bundle.min.js', 'vendor') ?>
<?= js('sweetalert/sweetalert.min.js', 'vendor') ?>

<?= js('datatables/dataTables-2.3.7.js', 'vendor') ?>
<?= js('datatables/jszip-3.10.1.min.js', 'vendor') ?>
<?= js('datatables/pdfmake-0.2.7.min.js', 'vendor') ?>
<?= js('datatables/vfs_fonts-0.2.7.js', 'vendor') ?>
<?= js('datatables/dataTables.buttons-3.2.6.js', 'vendor') ?>
<?= js('datatables/buttons-3.2.6.dataTables.js', 'vendor') ?>
<?= js('datatables/buttons-3.2.6.html5.min.js', 'vendor') ?>
<?= js('datatables/buttons-3.2.6.print.min.js', 'vendor') ?>

<?= js('app.js') ?>
<?= css('app.css') ?>

<?php stack('styles') ?>

<script>
    const BASE_URL   = "<?php echo BASE_URL; ?>";
    const ASSETS_URL = "<?php echo ASSETS_URL; ?>";
    const JS_URL    = "<?php echo JS_URL; ?>";
    const CSS_URL   = "<?php echo CSS_URL; ?>";
</script>