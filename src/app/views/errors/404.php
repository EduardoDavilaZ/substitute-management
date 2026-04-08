<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada</title>
    <?= css('bootstrap/bootstrap.min.css', 'vendor') ?>
    <?= css('app.css') ?>
</head>
<body class="bg-app">
    <div class="container vh-100 d-flex">
        <div class="rounded text-center bg-surface p-4 m-auto"> 
            <h1 class="display-1 fw-bold">404</h1>
            <h2 class="text-title">¡Ups! Página no encontrada</h2>
            <div class="text-subtitle mb-4">
                Lo sentimos, la página que buscas no existe o ha sido movida.
            </div>
            <div class="d-flex gap-2 justify-content-center">
                <button onclick="history.back()" class="btn btn-custom"">
                    <i class="bi bi-arrow-counterclockwise"></i> Volver atrás
                </button>
                
                <a href="<?= url('') ?>" class="btn btn-custom">
                    <i class="bi bi-house"></i> Ir al Inicio
                </a>
            </div>
        </div>
    </div>
</body>
</html>