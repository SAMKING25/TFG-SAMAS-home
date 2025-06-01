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
$sql = "SELECT c.id_producto, c.cantidad, p.nombre, p.precio, p.img_producto, o.porcentaje, p.stock
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
        "subtotal" => $subtotal,
        "stock" => $fila["stock"] // <-- Añadido
    ];
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["eliminar_producto"])) {
    $id_producto = intval($_POST["eliminar_producto"]);
    $stmt = $_conexion->prepare("DELETE FROM carrito WHERE id_usuario = ? AND id_producto = ?");
    $stmt->bind_param("ii", $id_usuario, $id_producto);
    $stmt->execute();
    header("Location: /carrito/?eliminado=1");
    exit;
}

// Procesar actualización de cantidad
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["actualizar_cantidad"])) {
    $id_producto = intval($_POST["id_producto"]);
    $nueva_cantidad = max(1, intval($_POST["nueva_cantidad"])); // Evita cantidades menores a 1

    // Comprobar stock disponible
    $stmt = $_conexion->prepare("SELECT stock FROM productos WHERE id_producto = ?");
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $stmt->bind_result($stock_disponible);
    $stmt->fetch();
    $stmt->close();

    if ($nueva_cantidad > $stock_disponible) {
        header("Location: /carrito/?stock=1");
        exit;
    }

    $stmt = $_conexion->prepare("UPDATE carrito SET cantidad = ? WHERE id_usuario = ? AND id_producto = ?");
    $stmt->bind_param("iii", $nueva_cantidad, $id_usuario, $id_producto);
    $stmt->execute();
    header("Location: /carrito/?actualizado=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Mi Carrito</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link id="favicon" rel="shortcut icon" href="/img/logos/loguito_gris.png" />
    <link rel="stylesheet" href="/css/landing.css" />
</head>
<style>
    body {
        background: #f9f6f2;
    }

    .main-content {
        background: #fff8ef;
        border-radius: 18px;
        box-shadow: 0 4px 24px rgba(180, 140, 80, 0.07);
        padding: 40px 30px;
        margin-top: 30px;
    }

    .carrito-card {
        border: none;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 2px 12px rgba(180, 140, 80, 0.08);
        transition: box-shadow 0.2s;
    }

    .carrito-card:hover {
        box-shadow: 0 4px 24px rgba(180, 140, 80, 0.13);
    }

    .carrito-img {
        background: #f4e5cc;
        border-radius: 12px;
        padding: 12px;
        max-height: 140px;
        object-fit: contain;
    }

    .carrito-titulo {
        color: #a67c52;
        font-weight: 700;
        font-size: 1.2rem;
    }

    .carrito-precio {
        color: #b08d57;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .carrito-precio-final {
        color: #4e944f;
        font-weight: 700;
        font-size: 1.15rem;
    }

    .carrito-descuento {
        color: #d35400;
        font-size: 0.95rem;
        font-weight: 600;
    }

    .btn-outline-primary,
    .btn-outline-primary:focus {
        border-color: #a67c52;
        color: #a67c52;
        background: transparent;
    }

    .btn-outline-primary:hover {
        background: #a67c52;
        color: #fff;
    }

    .btn-secondary,
    .btn-secondary:focus {
        background: linear-gradient(90deg, #b08d57 0%, #a67c52 100%);
        border: none;
        color: #fff;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .btn-secondary:disabled {
        background: #e0c9a6;
        color: #fff;
    }

    .form-control:focus {
        border-color: #b08d57;
        box-shadow: 0 0 0 0.2rem rgba(180, 140, 80, 0.13);
    }

    .resumen-card {
        border-radius: 16px;
        background: #fffdf8;
        box-shadow: 0 2px 12px rgba(180, 140, 80, 0.08);
    }

    .swal2-popup {
        font-family: inherit !important;
    }

    /* RESPONSIVE MOBILE - estilo tipo ver_producto */
    @media (max-width: 767.98px) {
        .main-content {
            padding: 0.7rem !important;
            border-radius: 10px;
            margin-top: 10px;
        }

        .carrito-card {
            border-radius: 10px !important;
            margin-bottom: 1rem !important;
            box-shadow: 0 2px 8px rgba(180, 140, 80, 0.1);
        }

        .carrito-img {
            max-height: 160px !important;   /* Antes: 90px */
            padding: 12px !important;       /* Igual que en desktop */
            width: 100% !important;
            object-fit: contain !important;
            display: block;
            margin: 0 auto;
            background: #f4e5cc;
            border-radius: 12px !important;
        }

        .carrito-titulo {
            font-size: 1.05rem !important;
        }

        .carrito-precio,
        .carrito-precio-final {
            font-size: 1rem !important;
        }

        .carrito-descuento {
            font-size: 0.9rem !important;
        }

        .card-body {
            flex-direction: column !important;
            gap: 0.5rem !important;
            padding: 0.7rem 0.5rem !important;
        }

        .col-md-4,
        .col-md-8 {
            flex: 0 0 100%;
            max-width: 100%;
            padding: 0 !important;
            text-align: center !important;
        }

        .row.g-0 {
            flex-direction: column !important;
        }

        .resumen-card {
            border-radius: 10px !important;
            margin-top: 1.5rem !important;
            box-shadow: 0 2px 8px rgba(180, 140, 80, 0.1);
        }

        .btn,
        .btn-sm,
        .btn-secondary,
        .btn-outline-primary {
            font-size: 1rem !important;
            border-radius: 10px !important;
            padding: 0.4rem 1rem !important;
        }

        .btn-eliminar-producto .bi-x-lg {
            font-size: 1.5rem !important;
        }

        .form-eliminar-producto {
            margin-left: 0 !important;
            margin-top: 0.5rem !important;
        }

        h2.mb-4 {
            font-size: 1.2rem !important;
            margin-bottom: 1rem !important;
        }

        .card-title,
        .fw-bold {
            font-size: 1.1rem !important;
        }

        .card-text {
            font-size: 0.98rem !important;
        }

        .col-md-7,
        .col-md-5 {
            flex: 0 0 100%;
            max-width: 100%;
        }

        .row {
            flex-direction: column !important;
        }

        .resumen-card {
            position: static !important;
            top: unset !important;
            z-index: unset !important;
        }
    }
</style>

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
                        <div class="card mb-3 shadow-sm carrito-card">
                            <div class="row g-0">
                                <div class="col-md-4 text-center p-3">
                                    <img src="/img/productos/<?php echo $producto["img"]; ?>"
                                        class="img-fluid rounded carrito-img" style="max-height: 150px; object-fit: contain;">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 class="card-title fw-bold carrito-titulo"><?php echo $producto["nombre"]; ?>
                                            </h5>
                                            <p class="card-text mb-1">
                                                Cantidad:
                                            <form method="post" action="" class="d-inline">
                                                <input type="hidden" name="id_producto"
                                                    value="<?php echo $producto["id_producto"]; ?>">
                                                <input type="hidden" name="nueva_cantidad"
                                                    value="<?php echo $producto["cantidad"] - 1; ?>">
                                                <button type="submit" name="actualizar_cantidad"
                                                    class="btn btn-sm btn-outline-primary" title="Restar uno" <?php echo ($producto["cantidad"] <= 1) ? 'disabled' : ''; ?>>
                                                    <i class="bi bi-dash"></i>
                                                </button>
                                            </form>
                                            <span class="mx-2 fw-bold"><?php echo $producto["cantidad"]; ?></span>
                                            <form method="post" action="" class="d-inline">
                                                <input type="hidden" name="id_producto"
                                                    value="<?php echo $producto["id_producto"]; ?>">
                                                <input type="hidden" name="nueva_cantidad"
                                                    value="<?php echo $producto["cantidad"] + 1; ?>">
                                                <button type="submit" name="actualizar_cantidad"
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="Sumar uno"
                                                    <?php echo ($producto["cantidad"] >= $producto["stock"]) ? 'disabled' : ''; ?>>
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </form>
                                            <?php if ($producto["cantidad"] >= $producto["stock"]): ?>
                                                <span class="text-danger ms-2" style="font-size:0.95em;">No hay más stock disponible</span>
                                            <?php endif; ?>
                                            </p>
                                            <?php if (!is_null($producto["porcentaje"])): ?>
                                                <p class="card-text carrito-descuento">
                                                    Descuento del <?php echo $producto["porcentaje"]; ?>%
                                                </p>
                                                <p class="card-text">
                                                    <span
                                                        class="text-success fw-semibold carrito-precio-final"><?php echo number_format($producto["precio_final"], 2, ',', '.'); ?>
                                                        €</span>
                                                    <span
                                                        class="text-muted text-decoration-line-through me-2 carrito-precio"><?php echo number_format($producto["precio"], 2, ',', '.'); ?>
                                                        €</span>

                                                </p>
                                            <?php else: ?>
                                                <p class="card-text text-success fw-semibold">
                                                    <?php echo number_format($producto["precio"], 2, ',', '.'); ?> €
                                                </p>
                                            <?php endif; ?>
                                            <p class="card-text"><strong>Subtotal:</strong>
                                                <?php echo number_format($producto["subtotal"], 2, ',', '.'); ?> €</p>
                                        </div>
                                        <!-- Botón de eliminar -->
                                        <!-- filepath: c:\xampp\htdocs\carrito\ -->
                                        <form method="post" action="" class="form-eliminar-producto d-inline"
                                            style="margin-left:10px;">
                                            <input type="hidden" name="eliminar_producto"
                                                value="<?php echo $producto["id_producto"]; ?>">
                                            <button type="button" class="btn btn-link text-danger p-0 btn-eliminar-producto"
                                                title="Eliminar producto">
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
                <div class="card shadow-sm resumen-card" style="position:sticky; top:90px; z-index:1;">
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
                        <p class="card-text fs-5">Total: <span
                                class="fw-bold text-success"><?php echo number_format($total, 2, ',', '.'); ?> €</span>
                        </p>

                        <hr>
                        <!-- Formulario para aplicar código de descuento -->
                        <form action="" method="">
                            <div class="mb-3">
                                <label for="codigo_descuento" class="form-label">Código de descuento</label>
                                <input type="text" class="form-control" name="codigo_descuento" id="codigo_descuento"
                                    placeholder="Introduce tu código">
                            </div>
                            <button type="submit" class="btn btn-outline-primary">Aplicar código</button>
                        </form>

                        <hr>
                        <!-- Formulario para finalizar compra -->
                        <form action="../pasarela-pago/" method="post">
                            <input type="hidden" name="importe"
                                value="<?php echo number_format((float) $total, 2, '.', ''); ?>">
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

    <?php if (isset($_GET['stock']) && $_GET['stock'] == '1'): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: 'No hay suficiente stock disponible',
                showConfirmButton: false,
                timer: 2500,
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

    <script>
        // Elimina los parámetros de mensaje de la URL tras mostrar el mensaje
        ['actualizado', 'eliminado', 'stock'].forEach(function(param) {
            if (window.location.search.includes(param + '=1')) {
                const url = new URL(window.location);
                url.searchParams.delete(param);
                window.history.replaceState({}, document.title, url.pathname + url.search);
            }
        });
    </script>
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