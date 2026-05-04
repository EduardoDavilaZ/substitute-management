<!DOCTYPE html>
<html lang="es">
<head>
    <?php

    include_once('header.php');
    stack('styles');

    ?>
</head>
<body class="bg-app">
    <?php

    include_once('menu.php'); 
    echo $content; 
    include_once('footer.php');
    stack('scripts'); 
    
    ?>
    <div id="modal-container"></div>
</body>
</html>