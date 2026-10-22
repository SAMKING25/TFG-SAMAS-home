<?php
// Copy this file to util/conexion.php and fill in your database details.
// util/conexion.php is ignored by Git, so real credentials are never committed.
$_servidor   = "localhost";   // Database host
$_usuario    = "root";        // Database user
$_contrasena = "";            // Database password
$_bd         = "samashome";   // Database name

$_conexion = new mysqli($_servidor, $_usuario, $_contrasena, $_bd)
    or die("Error de conexión");
$_conexion->set_charset("utf8mb4");
?>
