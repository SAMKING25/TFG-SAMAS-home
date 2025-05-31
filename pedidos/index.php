<?php
require('../util/conexion.php');
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: /login/usuario/iniciar_sesion_usuario");
    exit;
}

$id_usuario = $_SESSION['usuario'];

// Obtener todos los pedidos del usuario
$sql = "SELECT id_pedido, fecha, total FROM pedidos WHERE id_usuario = ? ORDER BY fecha DESC";
$stmt = $_conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$pedidos = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4">Mis pedidos</h2>
    <?php if ($pedidos->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID Pedido</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($pedido = $pedidos->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $pedido['id_pedido']; ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($pedido['fecha'])); ?></td>
                        <td><?php echo number_format($pedido['total'], 2); ?> €</td>
                        <td>
                            <a href="ver_pedido.php?id_pedido=<?php echo $pedido['id_pedido']; ?>" class="btn btn-primary btn-sm">
                                Ver detalles
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No tienes pedidos realizados.</div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>