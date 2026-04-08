<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Error interno</title>
    <?= css('bootstrap/bootstrap.min.css', 'vendor') ?>
    <?= css('app.css') ?>
</head>
<body class="bg-app">
    <div class="container vh-100 d-flex">
        <div class="rounded text-center bg-surface p-4 m-auto"> 
            <h1 class="display-1 fw-bold">500</h1>
            <h2 class="text-title">Error interno del servidor</h2>
            <div class="text-subtitle mb-4">
                Lo sentimos, ha ocurrido un error inesperado en nuestro sistema. 
                Por favor, inténtalo de nuevo más tarde o contacta con soporte si el problema persiste.
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