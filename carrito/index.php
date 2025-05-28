<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require('../util/conexion.php');
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: /login/usuario/iniciar_sesion_usuario.php");
    exit;
}

$id_usuario = $_SESSION["usuario"];

// Obtener productos del carrito agrupados por producto
$sql = "SELECT c.id_producto, SUM(c.cantidad) AS cantidad_total, p.nombre, p.precio, p.img_producto, o.porcentaje
        FROM carrito c
        INNER JOIN productos p ON c.id_producto = p.id_producto
        LEFT JOIN ofertas o ON p.id_oferta = o.id_oferta
        WHERE c.id_usuario = ?
        GROUP BY c.id_producto";

$stmt = $_conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

$productos = [];
$total = 0;

while ($fila = $resultado->fetch_assoc()) {
    $cantidad = $fila["cantidad_total"];
    $precio = $fila["precio"];
    $porcentaje = $fila["porcentaje"];

    if (!is_null($porcentaje)) {
        $precio_final = $precio * (1 - $porcentaje / 100);
    } else {
        $precio_final = $precio;
    }

    $subtotal = $precio_final * $cantidad;
    $total += $subtotal;

    $productos[] = [
        "nombre" => $fila["nombre"],
        "cantidad" => $cantidad,
        "img" => $fila["img_producto"],
        "precio" => $precio,
        "porcentaje" => $porcentaje,
        "precio_final" => $precio_final,
        "subtotal" => $subtotal
    ];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Mi Carrito</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="/css/landing.css" />
</head>

<body style="background-color: #f4e5cc;">
    <?php include('../navbar.php'); ?>

    <div class="container py-5 main-content">
        <div class="row">
            <!-- Productos a la izquierda -->
            <div class="col-md-7">
                <h2 class="mb-4">Mi Carrito</h2>
                <?php if (empty($productos)): ?>
                    <p>Tu carrito está vacío.</p>
                <?php else: ?>
                    <?php foreach ($productos as $producto): ?>
                        <div class="card mb-3 shadow-sm">
                            <div class="row g-0">
                                <div class="col-md-4 text-center p-3">
                                    <img src="/img/productos/<?php echo $producto["img"]; ?>" class="img-fluid rounded" style="max-height: 150px; object-fit: contain;">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title fw-bold"><?php echo $producto["nombre"]; ?></h5>
                                        <p class="card-text mb-1">Cantidad: <?php echo $producto["cantidad"]; ?></p>
                                        <?php if (!is_null($producto["porcentaje"])): ?>
                                            <p class="card-text text-danger mb-1">
                                                Descuento del <?php echo $producto["porcentaje"]; ?>%
                                            </p>
                                            <p class="card-text">
                                                <span class="text-muted text-decoration-line-through me-2"><?php echo number_format($producto["precio"], 2, ',', '.'); ?> €</span>
                                                <span class="text-success fw-semibold"><?php echo number_format($producto["precio_final"], 2, ',', '.'); ?> €</span>
                                            </p>
                                        <?php else: ?>
                                            <p class="card-text text-success fw-semibold">
                                                <?php echo number_format($producto["precio"], 2, ',', '.'); ?> €
                                            </p>
                                        <?php endif; ?>
                                        <p class="card-text"><strong>Subtotal:</strong> <?php echo number_format($producto["subtotal"], 2, ',', '.'); ?> €</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Resumen y pago a la derecha -->
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Resumen del pedido</h4>
                        <p class="card-text">Cantidad de productos: <strong><?php echo array_sum(array_column($productos, "cantidad")); ?></strong></p>
                        <p class="card-text fs-5">Total: <span class="fw-bold text-success"><?php echo number_format($total, 2, ',', '.'); ?> €</span></p>

                        <hr>
                        <form action="#" method="post">
                            <div class="mb-3">
                                <label for="codigo_descuento" class="form-label">Código de descuento</label>
                                <input type="text" class="form-control" name="codigo_descuento" id="codigo_descuento" placeholder="Introduce tu código">
                            </div>
                            <button type="submit" class="btn btn-outline-primary">Aplicar código</button>
                        </form>

                        <hr>
                        <button class="btn btn-success w-100 mt-3">Finalizar compra</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pop-up de cookies incluido-->
	<?php include('../cookies.php'); ?>
    <?php include('../footer.php'); ?>
    <?php include('../udify-bot.php'); ?>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</html>