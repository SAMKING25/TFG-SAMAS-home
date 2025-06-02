<?php
    // Inicia la sesión para poder destruirla
    session_start();
    // Destruye todas las variables de sesión
    session_destroy();
    // Redirige al usuario a la página de inicio
    header("location: /");
    exit;
?>