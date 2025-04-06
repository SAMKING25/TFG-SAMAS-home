<?php
    session_start();
    session_destroy();
    header("location: iniciar_sesion.php");
    exit;
?>