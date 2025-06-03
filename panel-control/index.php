<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <!-- Bootstrap y estilos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link id="favicon" rel="shortcut icon" href="/img/logos/loguito_gris.png" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <?php
    // Muestra errores en pantalla
    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    // Conexión a la base de datos
    require('../util/conexion.php');

    // Inicia sesión y verifica si el proveedor está logueado
    session_start();
    if (!isset($_SESSION["proveedor"])) {
        header("location: ../login/usuario/iniciar_sesion_usuario");
        exit;
    }
    ?>
    <style>
        body {
            background-color: #F7E5CB;
        }

        .main-header {
            background: rgba(120, 80, 40, 0.92);
            color: #fff;
            border-radius: 0 0 1rem 1rem;
            box-shadow: 0 2px 8px rgba(120, 80, 40, 0.08);
        }

        .btn-gold {
            background: #a37030;
            color: #fff;
            border: none;
        }

        .btn-gold:hover {
            background: #7c5522;
            color: #fff;
        }

        .card-producto {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 12px rgba(120, 80, 40, 0.10);
            transition: transform 0.15s;
        }

        .card-producto:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 4px 20px rgba(120, 80, 40, 0.15);
        }

        .card-img-top {
            height: 220px;
            object-fit: cover;
            border-radius: 1rem 1rem 0 0;
        }

        .empty-state {
            color: #a37030;
        }
    </style>
</head>

<body>
    <?php
    // Incluye el header y el sidebar del panel
    include('./layout/header.php');
    include('./layout/sidebar.php');

    // Si se recibe un POST, elimina el producto correspondiente
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_producto = $_POST["id_producto"];

        $sql = "DELETE FROM productos WHERE id_producto = '$id_producto'";
        $_conexion->query($sql);
    }

    // Consulta los productos del proveedor actual
    $sql = "SELECT * FROM productos WHERE id_proveedor = '" . $_SESSION['proveedor'] . "'";
    $resultado = $_conexion->query($sql);
    ?>

    <div class="container py-5">
        <div class="main-header p-4 mb-5 text-center shadow-sm">
            <h1 class="mb-1">Panel de Control</h1>
            <p class="mb-0">Gestiona tus productos de forma sencilla y rápida</p>
        </div>

        <?php if ($resultado->num_rows === 0): ?>
            <!-- Estado vacío: no hay productos -->
            <div class="d-flex flex-column align-items-center justify-content-center" style="height: 40vh;">
                <p class="fs-4 mb-4 text-center empty-state">
                    <i class="bi bi-box-seam fs-1 mb-2"></i><br>
                    Todavía no has subido ningún producto.<br>
                    ¡Crea tu primer producto!
                </p>
                <a href="./productos/nuevo_producto" class="btn btn-gold px-4 py-2">
                    <i class="bi bi-plus-circle"></i> Nuevo producto
                </a>
            </div>
        <?php else: ?>
            <!-- Lista de productos del proveedor -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Mis productos</h2>
                <a href="./productos/nuevo_producto" class="btn btn-gold">
                    <i class="bi bi-plus-circle"></i> Nuevo producto
                </a>
            </div>
            <div class="row g-4">
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                        <div class="card card-producto h-100">
                            <a href="./productos/ver_producto/?id_producto=<?php echo $fila['id_producto'] ?>"
                                class="text-decoration-none">
                                <img src="../img/productos/<?php echo $fila['img_producto']; ?>" class="card-img-top"
                                    alt="<?php echo $fila['nombre']; ?>">
                                <div class="card-body">
                                    <h5 class="card-title text-dark"><?php echo $fila['nombre']; ?></h5>
                                    <p class="card-text text-muted"><?php echo $fila['descripcion']; ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold"
                                            style="color: #a37030"><?php echo number_format($fila['precio'], 2); ?>€</span>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="id_producto" value="<?php echo $fila["id_producto"]; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar"><i
                                                    class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php include('../cookies.php'); ?>
    <?php include('../udify-bot.php'); ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>