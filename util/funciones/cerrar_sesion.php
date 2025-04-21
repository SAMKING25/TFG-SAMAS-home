<?php
    session_start();
    session_destroy();
    header("location: /login/usuario/iniciar_sesion_usuario.php");
    exit;
?>