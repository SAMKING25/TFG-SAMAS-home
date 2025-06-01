<?php
session_start();
require_once('../../util/conexion.php');

if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$id_usuario = $_SESSION['usuario'];

// Consulta los usos actuales y el máximo permitido
$sql = "SELECT u.usos_plano, s.max_usos_plano
        FROM usuarios u
        JOIN suscripciones s ON u.id_suscripcion = s.id_suscripcion
        WHERE u.id_usuario = ?";
$stmt = $_conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$datos = $result->fetch_assoc();

if (!$datos) {
    http_response_code(404);
    echo json_encode(['error' => 'Usuario no encontrado']);
    exit;
}

if ($datos['usos_plano'] >= $datos['max_usos_plano']) {
    echo json_encode(['success' => false, 'limit' => true]);
    exit;
}

// Sumar un uso
$sqlUpdate = "UPDATE usuarios SET usos_plano = usos_plano + 1 WHERE id_usuario = ?";
$stmtUpdate = $_conexion->prepare($sqlUpdate);
$stmtUpdate->bind_param("i", $id_usuario);
if ($stmtUpdate->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al actualizar']);
}