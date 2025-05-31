<?php
require('../util/conexion.php');
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: /login/usuario/iniciar_sesion_usuario");
    exit;
}

$id_usuario = $_SESSION['usuario'];

// Obtener todos los productos comprados por el usuario, junto con los datos del pedido
$sql = "SELECT p.nombre, p.img_producto, d.precio_unitario, d.cantidad, pe.fecha
        FROM pedidos pe
        JOIN detalle_pedidos d ON pe.id_pedido = d.id_pedido
        JOIN productos p ON d.id_producto = p.id_producto
        WHERE pe.id_usuario = ?
        ORDER BY pe.fecha DESC";
$stmt = $_conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$productos = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis productos comprados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4">Mis productos comprados</h2>
    <?php if (isset($_GET['exito'])): ?>
        <div class="alert alert-success">¡Compra completada correctamente!</div>
    <?php endif; ?>
    <?php if ($productos->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Imagen</th>
                        <th>Producto</th>
                        <th>Precio unitario</th>
                        <th>Cantidad</th>
                        <th>Fecha de compra</th>
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
                        <td><?php echo date('d/m/Y H:i', strtotime($prod['fecha'])); ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No tienes productos comprados.</div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>