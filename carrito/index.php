<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require('../util/conexion.php');
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: /login/usuario/iniciar_sesion_usuario");
    exit;
}

$id_usuario = $_SESSION["usuario"];

// Obtener productos del carrito agrupados por producto
$sql = "SELECT c.id_producto, c.cantidad, p.nombre, p.precio, p.img_producto, o.porcentaje
        FROM carrito c
        INNER JOIN productos p ON c.id_producto = p.id_producto
        LEFT JOIN ofertas o ON p.id_oferta = o.id_oferta
        WHERE c.id_usuario = ?";

$stmt = $_conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();

$productos = [];
$total = 0;

while ($fila = $resultado->fetch_assoc()) {
    $cantidad = $fila["cantidad"]; // <--- Corrección aquí
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
        "id_producto" => $fila["id_producto"],
        "nombre" => $fila["nombre"],
        "cantidad" => $cantidad,
        "img" => $fila["img_producto"],
        "precio" => $precio,
        "porcentaje" => $porcentaje,
        "precio_final" => $precio_final,
        "subtotal" => $subtotal
    ];
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["eliminar_producto"])) {
    $id_producto = intval($_POST["eliminar_producto"]);
    $stmt = $_conexion->prepare("DELETE FROM carrito WHERE id_usuario = ? AND id_producto = ?");
    $stmt->bind_param("ii", $id_usuario, $id_producto);
    $stmt->execute();
    header("Location: index.php?eliminado=1");
    exit;
}

// Procesar actualización de cantidad
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["actualizar_cantidad"])) {
    $id_producto = intval($_POST["id_producto"]);
    $nueva_cantidad = max(1, intval($_POST["nueva_cantidad"])); // Evita cantidades menores a 1
    $stmt = $_conexion->prepare("UPDATE carrito SET cantidad = ? WHERE id_usuario = ? AND id_producto = ?");
    $stmt->bind_param("iii", $nueva_cantidad, $id_usuario, $id_producto);
    $stmt->execute();
    header("Location: index.php?actualizado=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Mi Carrito</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link id="favicon" rel="shortcut icon" href="/img/logos/loguito_gris.png" />
    <link rel="stylesheet" href="/css/landing.css" />
</head>

<body>
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
                                    <div class="card-body d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 class="card-title fw-bold"><?php echo $producto["nombre"]; ?></h5>
                                            <p class="card-text mb-1">
                                                Cantidad:
                                            <form method="post" action="" class="d-inline">
                                                <input type="hidden" name="id_producto" value="<?php echo $producto["id_producto"]; ?>">
                                                <input type="number" name="nueva_cantidad" value="<?php echo $producto["cantidad"]; ?>" min="1" style="width:60px;" class="form-control d-inline p-1" required>
                                                <button type="submit" name="actualizar_cantidad" class="btn btn-sm btn-outline-primary ms-1" title="Actualizar cantidad">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </button>
                                            </form>
                                            </p>
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
                                        <!-- Botón de eliminar -->
                                        <!-- filepath: c:\xampp\htdocs\carrito\index.php -->
                                        <form method="post" action="" class="form-eliminar-producto d-inline" style="margin-left:10px;">
                                            <input type="hidden" name="eliminar_producto" value="<?php echo $producto["id_producto"]; ?>">
                                            <button type="button" class="btn btn-link text-danger p-0 btn-eliminar-producto" title="Eliminar producto">
                                                <i class="bi bi-x-lg fs-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Resumen y pago a la derecha -->
            <div class="col-md-5">
                <div class="card shadow-sm" style="position:sticky; top:90px; z-index:1;">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Resumen del pedido</h4>
                        <p class="card-text mb-1">
                            Cantidad total:
                            <span class="fw-bold">
                                <?php
                                $cantidad_total = 0;
                                foreach ($productos as $producto) {
                                    $cantidad_total += $producto["cantidad"];
                                }
                                echo $cantidad_total;
                                ?>
                            </span>
                        </p>
                        <p class="card-text fs-5">Total: <span class="fw-bold text-success"><?php echo number_format($total, 2, ',', '.'); ?> €</span></p>

                        <hr>
                        <!-- Formulario para aplicar código de descuento -->
                        <form action="" method="">
                            <div class="mb-3">
                                <label for="codigo_descuento" class="form-label">Código de descuento</label>
                                <input type="text" class="form-control" name="codigo_descuento" id="codigo_descuento" placeholder="Introduce tu código">
                            </div>
                            <button type="submit" class="btn btn-outline-primary">Aplicar código</button>
                        </form>

                        <hr>
                        <!-- Formulario para finalizar compra -->
                        <form action="../pasarela-pago/" method="post">
                            <input type="hidden" name="importe" value="<?php echo number_format((float)$total, 2, '.', ''); ?>">
                            <button type="submit" class="btn btn-secondary w-100 mt-3" <?php echo ($total <= 0) ? 'disabled' : ''; ?>>
                                Finalizar compra
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('../cookies.php'); ?>
    <?php include('../footer.php'); ?>
    <?php include('../udify-bot.php'); ?>

    <?php if (isset($_GET['actualizado']) && $_GET['actualizado'] == '1'): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Cantidad actualizada',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                background: '#f4e5cc',
                color: '#333',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        </script>
    <?php endif; ?>

    <?php if (isset($_GET['eliminado']) && $_GET['eliminado'] == '1'): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Producto eliminado del carrito',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                background: '#f4e5cc',
                color: '#333',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        </script>
    <?php endif; ?>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.btn-eliminar-producto').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = btn.closest('form');
            Swal.fire({
                title: "¿Estás seguro?",
                text: "¿Quieres eliminar este producto del carrito?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>

</html>