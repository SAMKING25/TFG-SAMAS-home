<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

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
    <title>Detalle del pedido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <!-- Archivo CSS personalizado -->
    <link rel="stylesheet" href="/css/landing.css" />
    <style>
        body {
            background: linear-gradient(120deg, #f8f6f2 0%, #f4e5cc 100%);
            font-family: 'Segoe UI', 'Roboto', Arial, sans-serif;
            color: #222;
        }

        .pedido-detalle-container {
            background: #fffbe9;
            border-radius: 2rem;
            box-shadow: 0 4px 24px 0 #bfa16a22;
            padding: 2.5rem 2rem;
            margin-top: 6rem;
            /* Más espacio arriba */
            margin-bottom: 4rem;
            /* Más espacio abajo */
        }

        .table-detalle {
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 2px 12px #bfa16a22;
        }

        .table-detalle th,
        .table-detalle td {
            text-align: center;
            vertical-align: middle !important;
        }

        .table-detalle th {
            color: #fff;
            font-size: 1.08rem;
            letter-spacing: 0.5px;
            border: none;
            font-weight: 700;
            height: 54px;
        }

        .th-img {
            background: #c8ad7f !important;
        }

        .th-producto {
            background: #eec06b !important;
        }

        .th-precio {
            background: #ffd88a !important;
        }

        .th-cantidad {
            background: #d6b77b !important;
        }

        .th-subtotal {
            background: rgb(236, 204, 145) !important;
        }

        .table-detalle td {
            background: #fff;
            font-size: 1.05rem;
            border: none;
            height: 54px;
        }

        .icono-cabecera {
            color: #b88c4a;
            font-size: 1.3rem;
            margin-right: 0.3em;
            vertical-align: middle;
        }

        .detalle-titulo {
            color: #b88c4a;
            letter-spacing: 1px;
            font-weight: bold;
        }

        .btn-volver {
            background: linear-gradient(90deg, #bfa16a 60%, #ffc25a 100%);
            color: #fff !important;
            font-weight: 600;
            border: none;
            border-radius: 1.5rem;
            padding: 0.35rem 1.2rem;
            font-size: 1rem;
            box-shadow: 0 2px 8px #bfa16a33;
            transition: background 0.2s, color 0.2s, transform 0.15s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.2rem;
        }

        .btn-volver:hover,
        .btn-volver:focus {
            background: linear-gradient(90deg, #a88c54 60%, #ffb340 100%);
            color: #fff !important;
            transform: scale(1.05);
        }

        @media (max-width: 900px) {
            .pedido-detalle-container {
                padding: 1.2rem 0.2rem;
            }

            .table-detalle th,
            .table-detalle td {
                font-size: 0.97rem;
                padding: 0.5rem 0.3rem;
            }

            .table-detalle img {
                width: 100px !important;
                height: 100px !important;
            }
        }

        @media (max-width: 600px) {

            .pedido-detalle-container {
                padding: 0.5rem 0.1rem;
            }

            .table-detalle th,
            .table-detalle td {
                font-size: 0.92rem;
                padding: 0.35rem 0.15rem;
            }

            .table-detalle img {
                width: 60px !important;
                height: 60px !important;
            }

            .detalle-titulo {
                font-size: 1.1rem;
            }

            .btn-volver {
                font-size: 0.95rem;
                padding: 0.25rem 0.7rem;
            }
        }
    </style>
</head>

<body>
    <?php include('../navbar.php'); ?>
    <div class="container pedido-detalle-container">
        <h2 class="mb-4 detalle-titulo">
            <i class="bi bi-receipt-cutoff icono-cabecera"></i>
            Detalle del pedido
        </h2>
        <div class="mb-3">
            <span class="me-4">
                <i class="bi bi-calendar3 icono-cabecera"></i>
                <strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['fecha'])); ?>
            </span>
            <span>
                <i class="bi bi-cash-coin icono-cabecera"></i>
                <strong>Total:</strong> <?php echo number_format($pedido['total'], 2); ?> €
            </span>
        </div>
        <a href="/pedidos/" class="btn btn-volver">
            <i class="bi bi-arrow-left"></i> Volver a mis pedidos
        </a>
        <?php if ($productos->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-responsive table-detalle align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="th-img">
                                <i class="bi bi-image icono-cabecera"></i> Imagen
                            </th>
                            <th class="th-producto">
                                <i class="bi bi-box-seam icono-cabecera"></i> Producto
                            </th>
                            <th class="th-precio">
                                <i class="bi bi-currency-euro icono-cabecera"></i> Precio unitario
                            </th>
                            <th class="th-cantidad">
                                <i class="bi bi-hash icono-cabecera"></i> Cantidad
                            </th>
                            <th class="th-subtotal">
                                <i class="bi bi-calculator icono-cabecera"></i> Subtotal
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($prod = $productos->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php
                                    // Si la ruta no es absoluta ni URL, la ajustamos a /img/productos/
                                    $img = $prod['img_producto'];
                                    if ($img && strpos($img, '/') !== 0 && strpos($img, 'http') !== 0) {
                                        $img = '/img/productos/' . ltrim($img, '/');
                                    } elseif ($img && strpos($img, '/img/productos/') !== 0 && strpos($img, 'http') !== 0) {
                                        // Si la ruta no contiene la carpeta correcta, la anteponemos
                                        $img = '/img/productos/' . basename($img);
                                    }
                                    ?>
                                    <div style="display: flex; justify-content: center; align-items: center;">
                                        <img src="<?php echo htmlspecialchars($img); ?>"
                                            alt="Imagen producto"
                                            style="width: 180px; height: 180px; object-fit: cover; border-radius: 18px; box-shadow: 0 2px 12px #bfa16a22; border: 2.5px solid #f3e2b8; margin: 12px;">
                                    </div>
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
            <div class="alert alert-info mt-4">No hay productos en este pedido.</div>
        <?php endif; ?>
    </div>
    <?php include('../footer.php'); ?>
    <?php include('../cookies.php'); ?>
    <?php include('../udify-bot.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>