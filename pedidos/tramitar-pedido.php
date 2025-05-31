<?php
require('../util/conexion.php');
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

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
    die('El carrito está vacío o no existe');
}

// 3. Insertar el pedido
$sql_pedido = "INSERT INTO pedidos (id_usuario, total, fecha) VALUES (?, ?, NOW())";
$stmt_pedido = $_conexion->prepare($sql_pedido);
if (!$stmt_pedido) {
    die('Error preparando pedido: ' . $_conexion->error);
}
$stmt_pedido->bind_param("id", $id_usuario, $total);
if (!$stmt_pedido->execute()) {
    die('Error ejecutando pedido: ' . $stmt_pedido->error);
} else {
    echo "Pedido insertado<br>";
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
    } else {
        echo "Detalle insertado para producto {$item['id_producto']}<br>";
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
// echo '<a href="/pedidos/index.php">Ir a mis pedidos</a>';
    header("location: /pasarela-pago/completado.html");
exit;
?>
