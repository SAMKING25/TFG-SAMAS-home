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

    <?php
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
    require('../util/conexion.php');
    session_start();
    ?>
</head>

<body>
    <?php include('../navbar.php'); ?>

    <div class="container">
        <h1 class="text-center mb-4">Productos</h1>

        <!-- Buscador -->
        <form method="GET" class="row g-2 mb-4 justify-content-center">
            <div class="col-md-4">
                <input type="text" class="form-control" name="busqueda" placeholder="Buscar por nombre"
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
        // ✅ ESTA ES LA PARTE CAMBIADA: usar lo que el usuario buscó
        if (isset($_GET['busqueda']) && !empty(trim($_GET['busqueda']))) {
            $busqueda = $_conexion->real_escape_string($_GET['busqueda']);
            $sql = "SELECT * FROM productos WHERE nombre LIKE '%$busqueda%'";
        } else {
            $sql = "SELECT * FROM productos";
        }

        $resultado = $_conexion->query($sql);
        ?>

        <!-- Mostrar productos -->
        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <div class="col">
                        <a href="ver_producto.php?id_producto=<?php echo $fila["id_producto"]; ?>" class="text-decoration-none text-dark">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                <img src="../../img/productos/<?php echo $fila["img_producto"]; ?>"
                                     class="card-img-top img-fluid"
                                     style="object-fit: contain; height: 300px; background-color: #f8f8f8; padding: 10px;"
                                     alt="Imagen del producto">

                                <div class="card-body text-center">
                                    <h5 class="card-title fw-bold fs-5 mb-1"><?php echo $fila["nombre"]; ?></h5>
                                    <p class="card-text text-success fs-5"><?php echo number_format($fila["precio"], 2); ?> €</p>
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
</body>

</html>
