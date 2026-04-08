<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Seleccionar Rol</title>
    <?= css('bootstrap/bootstrap.min.css', 'vendor') ?>
    <?= css('app.css') ?>
</head>
<body>
    <header class="text-center py-4">
        <h1 class="text-title">Bienvenido al Sistema de Guardias</h1>
    </header>

    <main class="container text-center">
        <div class="row row-cols-1 row-cols-md-2 g-4">
            <div class="col">
                <a href="<?= url('role/set-role/admin') ?>" class="card shadow border-0 p-4 text-decoration-none bg-brand-dark text-white">
                    <i class="bi bi-shield-lock fs-1"></i>
                    <h3 class="my-4">Administrador</h3>
                </a>
            </div>
            <div class="col">
                <a href="<?= url('role/set-role/teacher') ?>" class="card shadow border-0 p-4 text-decoration-none bg-brand-dark text-white">
                    <i class="bi bi-person-badge fs-1"></i>
                    <h3 class="my-4">Profesor</h3>
                </a>
            </div>
        </div>
    </main>
</body>
</html>