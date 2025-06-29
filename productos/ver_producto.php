<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require('../util/conexion.php');
session_start();

// Procesar solicitud POST para añadir al carrito
if (isset($_GET["id_producto"])) {
    $id = intval($_GET["id_producto"]);

    $sql = "SELECT p.*, o.porcentaje, pr.nombre_proveedor 
        FROM productos p
        LEFT JOIN ofertas o ON p.id_oferta = o.id_oferta
        LEFT JOIN proveedores pr ON p.id_proveedor = pr.id_proveedor
        WHERE p.id_producto = $id";
    $resultado = $_conexion->query($sql);

    if ($resultado->num_rows > 0) {
        $producto = $resultado->fetch_assoc();
        $medidas = json_decode($producto["medidas"], true);
        $precio = $producto["precio"];
        $porcentaje = $producto["porcentaje"];
        $nombre_proveedor = $producto["nombre_proveedor"];
    } else {
        die("Producto no encontrado.");
    }

    // Ahora procesamos el POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!isset($_SESSION["usuario"])) {
            header("Location: ../login/usuario/iniciar_sesion_usuario");
            exit;
        }

        $id_producto = intval($_GET["id_producto"]);
        $id_usuario = $_SESSION["usuario"];
        $cantidad = isset($_POST["cantidad"]) ? intval($_POST["cantidad"]) : 0;

        // 1. Consulta cuántos ya tiene el usuario en el carrito
        $stmt_carrito = $_conexion->prepare("SELECT cantidad FROM carrito WHERE id_usuario = ? AND id_producto = ?");
        $stmt_carrito->bind_param("ii", $id_usuario, $id_producto);
        $stmt_carrito->execute();
        $stmt_carrito->bind_result($cantidad_en_carrito);
        $stmt_carrito->fetch();
        $stmt_carrito->close();

        if (!isset($cantidad_en_carrito)) {
            $cantidad_en_carrito = 0;
        }

        // 2. Comprobar si la suma supera el stock
        if ($cantidad < 1) {
            $mensaje = "error";
            $errorMsg = "Cantidad no válida.";
        } elseif ($cantidad_en_carrito >= $producto["stock"]) {
            $mensaje = "error";
            $errorMsg = "Ya tienes el máximo stock permitido en tu carrito.";
        } elseif (($cantidad + $cantidad_en_carrito) > $producto["stock"]) {
            $mensaje = "error";
            $errorMsg = "No puedes añadir más de " . $producto["stock"] . " unidades en total.";
        } else {
            // Insertar o actualizar cantidad en el carrito
            $stmt = $_conexion->prepare(
                "INSERT INTO carrito (id_usuario, id_producto, cantidad) VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE cantidad = cantidad + VALUES(cantidad)"
            );
            $stmt->bind_param("iii", $id_usuario, $id_producto, $cantidad);

            if ($stmt->execute()) {
                $mensaje = "success";
            } else {
                $mensaje = "error";
                $errorMsg = $stmt->error;
            }
            $stmt->close();
        }
    }
} else {
    die("ID no válido.");
}

$hayOferta = !is_null($porcentaje);
if ($hayOferta) {
    $precioFinal = $precio * (1 - $porcentaje / 100);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SAMAS HOME</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link id="favicon" rel="shortcut icon" href="/img/logos/loguito_gris.png" />
    <!-- Archivo CSS personalizado -->
    <link rel="stylesheet" href="/css/landing.css" />
    <!--search-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />

    <style>
        .hover-shadow:hover {
            box-shadow: 0 0 0 4px #ffe5b4, 0 8px 24px rgba(184, 140, 74, 0.10) !important;
        }

        .img-fija {
            width: 100%;
            max-width: 500px;
            height: 400px;
            object-fit: contain;
            background: #f8f8f8;
        }

        .img-similar-wrapper {
            width: 100%;
            height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f8f8;
            border-radius: 12px;
            overflow: hidden;
        }

        .img-similar {
            width: 100%;
            height: 200px;
            object-fit: contain;
            background-color: #f8f8f8;
            padding: 10px;
            display: block;
            margin: 0 auto;
        }

        /* Cambia el fondo del container principal a un gris muy claro elegante */
        .main-content {
            background: #f5f5f7;
            /* Antes: #fffbe9 */
            border-radius: 24px;
        }

        /* Responsive para móvil */
        @media (max-width: 767.98px) {
            .bg-light.p-3 {
                padding: 0 !important;
                min-height: 200px !important;
            }

            .img-fija {
                width: 100vw !important;
                max-width: 100vw !important;
                height: 260px !important;
                min-height: 140px !important;
                object-fit: cover !important;
                margin: 0 !important;
                border-radius: 0 !important;
                background: #f8f8f8;
                box-shadow: none !important;
                display: block;
                position: relative;
                left: 50%;
                transform: translateX(-50%);
            }

            .main-content {
                padding: 0.5rem !important;
                border-radius: 10px;
            }

            .card.shadow-lg {
                padding: 1rem !important;
                border-radius: 12px !important;
            }

            .img-fija {
                max-width: 100%;
                width: 100%;
                height: 320px;
                min-height: 180px;
                object-fit: contain;
                background: #f8f8f8;
                display: block;
                margin: 0 auto;
            }

            .img-similar-wrapper {
                height: 120px;
                border-radius: 8px;
            }

            .img-similar {
                height: 100px;
                padding: 4px;
            }

            .badge.bg-danger,
            .badge.bg-success,
            .badge.bg-secondary {
                font-size: 0.9rem !important;
                padding: 0.5em 0.8em !important;
                border-radius: 8px !important;
            }

            .row-cols-sm-2>* {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .row-cols-md-3>* {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .card-body.text-center {
                padding: 0.7rem 0.3rem !important;
            }

            h1.fw-bold {
                font-size: 1.3rem !important;
            }

            .fs-3,
            .fs-5 {
                font-size: 1.1rem !important;
            }

            .btn-lg,
            .btn {
                font-size: 1rem !important;
                padding: 0.5rem 1rem !important;
                border-radius: 10px !important;
            }

            .btn.btn-warning {
                width: 56px !important;
                height: 56px !important;
                padding: 0 !important;
                border-radius: 12px !important;
                min-width: 0 !important;
                justify-content: center !important;
                align-items: center !important;
                display: flex !important;
            }

            .btn.btn-warning .bi {
                margin: 0 !important;
                font-size: 1.7rem !important;
            }

            .mb-5 {
                margin-bottom: 1.2rem !important;
            }

            .rounded-4,
            .rounded-5 {
                border-radius: 10px !important;
            }

            form.d-flex.gap-3 {
                gap: 1rem !important;
            }
        }
    </style>
</head>

<body>
    <?php include('../navbar.php'); ?>

    <div class="container main-content py-5">
        <!-- Script para mostrar notificaciones con SweetAlert2 según el resultado de añadir al carrito -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            <?php if (isset($mensaje) && $mensaje == "success"): ?>
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Producto añadido al carrito',
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
            <?php elseif (isset($mensaje) && $mensaje == "error"): ?>
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'Error al añadir el producto al carrito',
                    text: '<?php echo addslashes($errorMsg); ?>',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            <?php endif; ?>
        </script>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Card principal con la información del producto -->
                <div class="card shadow-lg border-0 rounded-5 p-4 mb-5" style="background: #fffbe9;">
                    <div class="row g-4 align-items-center">
                        <!-- Imagen del producto y etiqueta de oferta si aplica -->
                        <div class="col-md-6 position-relative">
                            <div class="bg-light rounded-4 p-3 d-flex align-items-center justify-content-center"
                                style="min-height: 450px;">
                                <img class="img-fija rounded-4 shadow-sm"
                                    src="../../img/productos/<?php echo $producto["img_producto"]; ?>"
                                    alt="<?php echo htmlspecialchars($producto["nombre"]); ?>">
                                <?php if ($hayOferta): ?>
                                    <span
                                        class="badge bg-danger fs-6 py-2 px-3 rounded-3 position-absolute top-0 start-0 mt-3 ms-3 shadow">
                                        ¡-<?php echo $porcentaje; ?>%!
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- Información textual y formulario de compra -->
                        <div class="col-md-6">
                            <h1 class="fw-bold mb-2" style="color: #b88c4a;"><?php echo $producto["nombre"]; ?></h1>
                            <p class="fs-5 text-muted mb-3"><?php echo $producto["descripcion"]; ?></p>
                            <div class="d-flex align-items-center mb-3">
                                <?php if ($hayOferta): ?>
                                    <span class="text-muted text-decoration-line-through me-2 fs-5">
                                        <?php echo number_format($precio, 2, ',', '.'); ?> €
                                    </span>
                                    <span class="text-success fw-bold fs-3">
                                        <?php echo number_format($precioFinal, 2, ',', '.'); ?> €
                                    </span>
                                <?php else: ?>
                                    <span class="text-success fw-bold fs-3">
                                        <?php echo number_format($precio, 2, ',', '.'); ?> €
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <span
                                    class="badge <?php echo $producto["stock"] > 0 ? 'bg-success' : 'bg-secondary'; ?> fs-6 px-3 py-2 rounded-pill">
                                    <?php echo $producto["stock"] > 0 ? "Disponible" : "No hay stock actualmente"; ?>
                                </span>
                            </div>
                            <ul class="list-unstyled fs-5 mb-4">
                                <li><i class="bi bi-tag-fill me-2" style="color:#b88c4a"></i><strong>Categoría:</strong>
                                    <?php echo $producto["categoria"]; ?></li>
                                <li><i class="bi bi-rulers me-2" style="color:#b88c4a"></i><strong>Medidas:</strong>
                                    <?php echo "{$medidas['largo']}cm × {$medidas['ancho']}cm × {$medidas['alto']}cm"; ?>
                                </li>
                                <li><i class="bi bi-building me-2" style="color:#b88c4a"></i><strong>Proveedor:</strong>
                                    <?php echo htmlspecialchars($nombre_proveedor); ?>
                                </li>
                            </ul>
                            <!-- Formulario para seleccionar cantidad y añadir al carrito -->
                            <form action="" method="post" class="d-flex align-items-end gap-3">
                                <div>
                                    <label for="cantidad" class="form-label mb-1 fw-semibold">Cantidad</label>
                                    <?php if ($producto["stock"] <= 0): ?>
                                        <select name="cantidad" id="cantidad"
                                            class="form-select form-select-lg w-auto rounded-3 shadow-sm" disabled>
                                            <option value="">-</option>
                                        </select>
                                    <?php else: ?>
                                        <select name="cantidad" id="cantidad"
                                            class="form-select form-select-lg w-auto rounded-3 shadow-sm">
                                            <?php
                                            $max = min(5, $producto["stock"]);
                                            for ($i = 1; $i <= $max; $i++): ?>
                                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    <?php endif; ?>
                                </div>
                                <button type="submit" class="btn btn-warning btn-lg rounded-4 px-4 shadow d-flex align-items-center justify-content-center"
                                    style="background:#b88c4a; border:none;"
                                    <?php if ($producto["stock"] <= 0) echo "disabled"; ?>>
                                    <i class="bi bi-cart-plus fs-4"></i>
                                    <span class="d-none d-md-inline ms-2">
                                        <?php echo $producto["stock"] <= 0 ? "Sin stock" : "Añadir al carrito"; ?>
                                    </span>
                                </button>
                                <a href="./" class="btn btn-outline-secondary btn-lg rounded-4 px-4">← Volver</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Productos similares -->
        <div class="container mt-4">
            <h3 class="mb-4" style="color:#b88c4a;">Productos similares</h3>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
                <?php
                // Obtiene la categoría y el id del producto actual
                $categoria = $producto["categoria"];
                $id_actual = $producto["id_producto"];
                // Consulta hasta 3 productos similares (misma categoría, distinto id)
                $sql_similares = "SELECT p.*, o.porcentaje 
                          FROM productos p 
                          LEFT JOIN ofertas o ON p.id_oferta = o.id_oferta 
                          WHERE p.categoria = ? AND p.id_producto != ?
                          LIMIT 3";
                $stmt_similares = $_conexion->prepare($sql_similares);
                $stmt_similares->bind_param("si", $categoria, $id_actual);
                $stmt_similares->execute();
                $result_similares = $stmt_similares->get_result();

                // Si no hay productos similares, muestra un aviso
                if ($result_similares->num_rows === 0): ?>
                    <div class="col">
                        <div class="alert alert-warning text-center w-100" role="alert">
                            Actualmente no hay productos similares disponibles.
                        </div>
                    </div>
                    <?php
                else:
                    // Recorre los productos similares y los muestra
                    while ($sim = $result_similares->fetch_assoc()):
                        $hayOfertaSim = !is_null($sim["porcentaje"]);
                        $precioFinalSim = $hayOfertaSim ? $sim["precio"] * (1 - $sim["porcentaje"] / 100) : $sim["precio"];
                    ?>
                        <div class="col mb-5">
                            <a href="ver_producto?id_producto=<?php echo $sim["id_producto"]; ?>"
                                class="text-decoration-none text-dark">
                                <div class="card h-100 shadow-sm border-0 rounded-4 position-relative hover-shadow"
                                    style="transition: box-shadow .2s;">
                                    <?php if ($hayOfertaSim): ?>
                                        <!-- Etiqueta de porcentaje de descuento si hay oferta -->
                                        <span class="position-absolute top-0 end-0 bg-danger text-white px-2 py-1 rounded-start">
                                            -<?php echo $sim["porcentaje"]; ?>%
                                        </span>
                                    <?php endif; ?>
                                    <div class="img-similar-wrapper">
                                        <img src="../../img/productos/<?php echo $sim["img_producto"]; ?>" class="img-similar"
                                            alt="Producto similar">
                                    </div>
                                    <div class="card-body text-center">
                                        <h6 class="card-title fw-bold mb-2"><?php echo $sim["nombre"]; ?></h6>
                                        <div class="card-text fs-6">
                                            <?php if ($hayOfertaSim): ?>
                                                <!-- Muestra precio original y precio con oferta -->
                                                <span class="text-muted text-decoration-line-through me-2">
                                                    <?php echo number_format($sim["precio"], 2, ',', '.'); ?> €
                                                </span>
                                                <span class="text-success fw-semibold">
                                                    <?php echo number_format($precioFinalSim, 2, ',', '.'); ?> €
                                                </span>
                                            <?php else: ?>
                                                <!-- Solo precio normal si no hay oferta -->
                                                <span class="text-success fw-semibold">
                                                    <?php echo number_format($sim["precio"], 2, ',', '.'); ?> €
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                <?php endwhile;
                endif;
                // Cierra el statement de productos similares
                $stmt_similares->close();
                ?>
            </div>
        </div>
    </div>
    <?php include('../cookies.php'); ?>
    <?php include('../footer.php'); ?>
    <?php include('../udify-bot.php'); ?>
</body>

</html>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>