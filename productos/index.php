<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require('../util/conexion.php');
session_start();
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
    <link rel="shortcut icon" href="./img/logos/logo-marron-nobg.ico" />
    <link rel="stylesheet" href="/css/landing.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />

    <style>
        .img-fija {
            width: 450px !important;
            height: 350px !important;
            object-fit: fill !important;
            display: block;
        }
    </style>

</head>

<body>
    <?php include('../navbar.php'); ?>

    <div class="container">
        <h1 class="text-center mt-4 mb-4">Productos</h1>

        <!-- Buscador -->
        <form method="GET" class="row g-2 mb-4 justify-content-center">
            <div class="col-md-4">
                <input type="text" class="form-control" name="busqueda" id="input-busqueda" placeholder="Buscar por nombre"
                    value="<?php echo isset($_GET['busqueda']) ? ($_GET['busqueda']) : ''; ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">Buscar</button>
            </div>
            <div class="col-auto">
                <a href="./index.php" class="btn btn-outline-secondary">Ver todos</a>
            </div>
        </form>

        <?php
        if (isset($_GET['busqueda']) && !empty(trim($_GET['busqueda']))) {
            $busqueda = $_conexion->real_escape_string($_GET['busqueda']);

            $sql = "SELECT p.*, o.porcentaje FROM productos p 
                    LEFT JOIN ofertas o ON p.id_oferta = o.id_oferta 
                    WHERE p.nombre LIKE '%$busqueda%'";
        } else {
            $sql = "SELECT p.*, o.porcentaje FROM productos p 
                    LEFT JOIN ofertas o ON p.id_oferta = o.id_oferta";
        }

        $resultado = $_conexion->query($sql);
        ?>

        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <?php
                    $precio = $fila["precio"];
                    $porcentaje = $fila["porcentaje"];

                    $hayOferta = false;
                    if (!is_null($porcentaje)) {
                        $hayOferta = true;
                        $precioFinal = $precio * (1 - $porcentaje / 100);
                    }
                    ?>
                    <div class="col">
                        <a href="ver_producto?id_producto=<?php echo $fila["id_producto"]; ?>" class="text-decoration-none text-dark">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative">

                                <?php if ($hayOferta): ?>
                                    <span class="position-absolute top-0 end-0 bg-danger text-white px-2 py-1 rounded-start">
                                        -<?php echo $porcentaje; ?>%
                                    </span>
                                <?php endif; ?>

                                <img class="img-fluid img-fija" src="../../img/productos/<?php echo $fila["img_producto"]; ?>"
                                    class="card-img-top img-fluid"
                                    style="object-fit: contain; height: 300px; background-color: #f8f8f8; padding: 10px;"
                                    alt="Imagen del producto <?php echo htmlspecialchars($fila["nombre"]); ?>">

                                <div class="card-body text-center">
                                    <h5 class="card-title fw-bold fs-5 mb-2"><?php echo $fila["nombre"]; ?></h5>

                                    <div class="card-text fs-5">
                                        <?php
                                        if ($hayOferta) {
                                        ?>
                                            <span class="text-muted text-decoration-line-through me-2">
                                                <?php echo number_format($precio, 2, ',', '.'); ?> €
                                            </span>
                                            <span class="text-success fw-semibold">
                                                <?php echo number_format($precioFinal, 2, ',', '.'); ?> €
                                            </span>
                                        <?php
                                        } else {
                                        ?>
                                            <span class="text-success fw-semibold">
                                                <?php echo number_format($precio, 2, ',', '.'); ?> €
                                            </span>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center" role="alert">
                No se encontraron productos.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Si la URL tiene ?focus=1, enfoca el input de búsqueda
        if (new URLSearchParams(window.location.search).get('focus') === '1') {
            const input = document.getElementById('input-busqueda');
            if (input) input.focus();
        }
    </script>
</body>

</html>