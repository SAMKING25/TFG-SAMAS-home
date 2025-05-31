<?php
require('../util/conexion.php');
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: /login/usuario/iniciar_sesion_usuario");
    exit;
}

$id_usuario = $_SESSION['usuario'];
$id_pedido = isset($_GET['id_pedido']) ? intval($_GET['id_pedido']) : 0;

// Verifica que el pedido pertenece al usuario
$sql = "SELECT * FROM pedidos WHERE id_pedido = ? AND id_usuario = ?";
$stmt = $_conexion->prepare($sql);
$stmt->bind_param("ii", $id_pedido, $id_usuario);
$stmt->execute();
$pedido = $stmt->get_result()->fetch_assoc();

if (!$pedido) {
    die("Pedido no encontrado o no tienes permiso para verlo.");
}

// Obtiene los productos del pedido
$sql = "SELECT d.*, p.nombre, p.img_producto FROM detalle_pedidos d
        JOIN productos p ON d.id_producto = p.id_producto
        WHERE d.id_pedido = ?";
$stmt = $_conexion->prepare($sql);
$stmt->bind_param("i", $id_pedido);
$stmt->execute();
$productos = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del pedido #<?php echo $id_pedido; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4">Detalle del pedido #<?php echo $id_pedido; ?></h2>
    <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['fecha'])); ?></p>
    <p><strong>Total:</strong> <?php echo number_format($pedido['total'], 2); ?> €</p>
    <a href="index.php" class="btn btn-secondary mb-3">← Volver a mis pedidos</a>
    <?php if ($productos->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Imagen</th>
                        <th>Producto</th>
                        <th>Precio unitario</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($prod = $productos->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <img src="<?php echo htmlspecialchars($prod['img_producto']); ?>" alt="Imagen producto" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                        </td>
                        <td><?php echo htmlspecialchars($prod['nombre']); ?></td>
                        <td><?php echo number_format($prod['precio_unitario'], 2); ?> €</td>
                        <td><?php echo $prod['cantidad']; ?></td>
                        <td><?php echo number_format($prod['precio_unitario'] * $prod['cantidad'], 2); ?> €</td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No hay productos en este pedido.</div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>