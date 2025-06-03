<?php
// Incluir archivo de conexión a la base de datos
require('../util/conexion.php');
// Iniciar sesión
session_start();

// Verificar si el usuario está logueado, si no redirigir a registro
if (!isset($_SESSION['usuario'])) {
    header('Location: /login/usuario/registro_usuario');
    exit;
}

// Obtener el ID del usuario desde la sesión
$id_usuario = $_SESSION['usuario'];
// ID de la suscripción básica
$id_basica = 1;

// Actualiza la suscripción del usuario a la básica en la base de datos
$stmt = $_conexion->prepare("UPDATE usuarios SET id_suscripcion = ? WHERE id_usuario = ?");
$stmt->bind_param("ii", $id_basica, $id_usuario);
$stmt->execute();
$stmt->close();

// Guardar mensaje de éxito en la sesión para mostrarlo después
$_SESSION['mensaje_cancelacion'] = "¡Suscripción cancelada! Ahora tienes la suscripción básica.";

// Redirigir al usuario a la página de suscripción
header('Location: /suscripcion/');
exit;
