<?php
    $_servidor = "127.0.0.1";
    $_usuario = "estudiante";
    $_contrasena = "estudiante";
    $_base_de_datos = "prueba_samas";

    $_conexion = new Mysqli($_servidor, $_usuario, $_contrasena, $_base_de_datos)
        or die("Error de conexión");
?>