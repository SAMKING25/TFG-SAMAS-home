<?php
require('../util/conexion.php');
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: /login/usuario/registro_usuario.php');
    exit;
}

$id_usuario = $_SESSION['usuario'];
$id_basica = 1;

// Actualiza la suscripción del usuario a la básica
$stmt = $_conexion->prepare("UPDATE usuarios SET id_suscripcion = ? WHERE id_usuario = ?");
$stmt->bind_param("ii", $id_basica, $id_usuario);
$stmt->execute();
$stmt->close();

// Mensaje de éxito usando sesión
$_SESSION['mensaje_cancelacion'] = "¡Suscripción cancelada! Ahora tienes la suscripción básica.";

header('Location: /suscripcion/');
exit;