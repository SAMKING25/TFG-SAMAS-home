<?php
require('../util/conexion.php');
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (isset($_POST['id_suscripcion']) && isset($_SESSION['usuario'])) {
    require('../util/conexion.php');
    $id_usuario = $_SESSION['usuario'];
    $id_suscripcion = intval($_POST['id_suscripcion']);

    // Actualiza la suscripción del usuario
    $stmt = $_conexion->prepare("UPDATE usuarios SET id_suscripcion = ? WHERE id_usuario = ?");
    $stmt->bind_param("ii", $id_suscripcion, $id_usuario);
    $stmt->execute();
    $stmt->close();
}

// 1. Comprobar usuario logueado
if (!isset($_SESSION['usuario'])) {
    die('Usuario no logueado');
}

$id_usuario = $_SESSION['usuario'];
// echo "ID usuario: $id_usuario<br>";

// 2. Leer productos del carrito de la base de datos
$sql = "SELECT c.id_producto, c.cantidad, p.precio
        FROM carrito c
        INNER JOIN productos p ON c.id_producto = p.id_producto
        WHERE c.id_usuario = ?";
$stmt = $_conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

$carrito = [];
$total = 0;
while ($item = $resultado->fetch_assoc()) {
    $carrito[] = $item;
    $total += $item['precio'] * $item['cantidad'];
}

// echo '<pre>';
// print_r($carrito);
// echo '</pre>';
// echo "Total: $total<br>";

if (empty($carrito)) {
    // Si es una suscripción, consideramos el pedido realizado correctamente
    if (isset($_POST['id_suscripcion'])) {
        echo json_encode(['success' => true, 'suscripcion' => true]);
        exit;
    } else {
        echo json_encode(['success' => false, 'error' => 'El carrito está vacío o no existe']);
        exit;
    }
}

// Recoger datos del comprador del POST
$datos_usuario = [
    'nombre' => $_POST['nombre'] ?? '',
    'apellidos' => $_POST['apellidos'] ?? '',
    'email' => $_POST['email'] ?? '',
    'telefono' => $_POST['telefono'] ?? '',
    'direccion' => $_POST['direccion'] ?? ''
];


$datos_usuario_json = json_encode($datos_usuario, JSON_UNESCAPED_UNICODE);

// 3. Insertar el pedido (añade datos_usuario)
$sql_pedido = "INSERT INTO pedidos (id_usuario, total, fecha, datos_usuario) VALUES (?, ?, NOW(), ?)";
$stmt_pedido = $_conexion->prepare($sql_pedido);
if (!$stmt_pedido) {
    die('Error preparando pedido: ' . $_conexion->error);
}
$total = floatval($total);
$stmt_pedido->bind_param("ids", $id_usuario, $total, $datos_usuario_json);
if (!$stmt_pedido->execute()) {
    die('Error ejecutando pedido: ' . $stmt_pedido->error);
}

$id_pedido = $_conexion->insert_id;

// 4. Insertar los detalles del pedido
$sql_detalle = "INSERT INTO detalle_pedidos (id_pedido, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
$stmt_detalle = $_conexion->prepare($sql_detalle);
if (!$stmt_detalle) {
    die('Error preparando detalle: ' . $_conexion->error);
}

foreach ($carrito as $item) {
    $stmt_detalle->bind_param("iiid", $id_pedido, $item['id_producto'], $item['cantidad'], $item['precio']);
    if (!$stmt_detalle->execute()) {
        die('Error insertando detalle: ' . $stmt_detalle->error);
    }
}

// 5. Vaciar el carrito de la base de datos
$stmt = $_conexion->prepare("DELETE FROM carrito WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
// echo "Carrito vaciado<br>";

// 6. (Opcional) Vaciar el carrito de la sesión
unset($_SESSION['carrito']);

// 7. Confirmar éxito
// echo '<p>Pedido guardado correctamente. Carrito vaciado.</p>';
// echo '<a href="/pedidos/">Ir a mis pedidos</a>';
echo json_encode(['success' => true]);
exit;