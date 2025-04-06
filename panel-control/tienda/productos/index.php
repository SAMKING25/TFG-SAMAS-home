<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <?php
    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    require('../util/conexion.php');

    // session_start();
    // if (isset($_SESSION["usuario"])) { 
    ?>
    <!-- <h2>Bienvenid@ <?php // echo $_SESSION["usuario"] 
                        ?> </h2>
            <a class="btn btn-warning" href="../usuario/cerrar_sesion.php">Cerrar sesion</a>
            <a class="btn btn-primary" href="../usuario/cambiar_credenciales.php?usuario=<?php // echo $_SESSION["usuario"] 
                                                                                            ?>">Cambiar credenciales</a> -->
    <?php
    // } else {
    //     header("location: ../usuario/iniciar_sesion.php");
    //     exit;
    // } 
    ?>
</head>

<body>
    <header class="p-3 bg-dark text-white">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                    <li><a href="#" class="nav-link px-2 text-white">Inicio</a></li>
                    <li><a href="#" class="nav-link px-2 text-secondary">Productos</a></li>
                    <li><a href="#" class="nav-link px-2 text-white">Pricing</a></li>
                    <li><a href="#" class="nav-link px-2 text-white">FAQs</a></li>
                    <li><a href="#" class="nav-link px-2 text-white">About</a></li>
                </ul>

                <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3">
                    <input type="search" class="form-control form-control-dark" placeholder="Buscar..." aria-label="Search">
                </form>

                <div class="text-end">
                    <a type="button" class="btn btn-outline-light me-2" href="./usuario/iniciar_sesion.php">Login</a>
                    <a type="button" class="btn btn-outline-info rounded-circle" href="./usuario/registro.php"><i class="bi bi-person-circle fs-5"></i></a>
                </div>
            </div>
        </div>
    </header>
    <div class="d-flex">
        <div class="d-flex flex-column flex-shrink-0 p-3 bg-light sidebar" style="width: 280px; position: fixed; top: 0; left: 0; height: 100vh; overflow-y: auto;">
            <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
                <img src="../imagenes/samas-home-logo.png" class="w-50" alt="">
                <span class="fs-4">SAMAS home</span>
            </a>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="../index.php" class="nav-link link-dark" aria-current="page">
                        <i class="bi bi-house-door me-2"></i>
                        Inicio
                    </a>
                </li>
                <li>
                    <a href="./productos/index.php" class="nav-link active">
                        <i class="bi bi-archive me-2"></i>
                        Gestión de productos
                    </a>
                </li>
                <li>
                    <a href="./nuevo_producto.php" class="nav-link link-dark">
                        <i class="bi bi-folder-plus me-2"></i>
                        Nuevo producto
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link link-dark">
                        <i class="bi bi-cart me-2"></i>
                        Products
                    </a>
                </li>
                <li>
                    <a href="#" class="nav-link link-dark">
                        <i class="bi bi-gear me-2"></i>
                        Ajustes
                    </a>
                </li>
            </ul>
            <hr>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://github.com/justmanuva.png" alt="" width="32" height="32" class="rounded-circle me-2">
                    <strong>justmanuva</strong>
                </a>
                <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser2">
                    <li><a class="dropdown-item" href="#">New project...</a></li>
                    <li><a class="dropdown-item" href="#">Settings</a></li>
                    <li><a class="dropdown-item" href="#">Profile</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="#">Sign out</a></li>
                </ul>
            </div>
        </div>
        <?php

        // Si se hace un POST (para borrar)
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id_producto = $_POST["id_producto"];
            $sql = "DELETE FROM productos WHERE id_producto = '$id_producto'";
            $_conexion->query($sql);
        }

        // Si se hace una búsqueda (GET)
        $filtro = "";
        if (isset($_GET["busqueda"]) && $_GET["busqueda"] !== "") {
            $busqueda = $_GET["busqueda"];
            // Si es numérico, busca por id_producto. Si no, busca por nombre
            if (is_numeric($busqueda)) {
                $filtro = "WHERE id_producto = '$busqueda'";
            } else {
                $busqueda = $_conexion->real_escape_string($busqueda); // protección básica
                $filtro = "WHERE nombre LIKE '%$busqueda%'";
            }
        }


        // Consulta SQL
        $sql = "SELECT * FROM productos $filtro";
        $resultado = $_conexion->query($sql);
        ?>

        <div class="container-fluid" style="margin-left: 280px; padding-top: 80px;">
            <h1 class="text-center mb-4">Gestión de Productos</h1>

            <!-- Buscador -->
            <form method="GET" class="row g-2 mb-4 justify-content-center">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="busqueda" placeholder="Buscar por ID o nombre">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-primary">Buscar</button>
                </div>
                <div class="col-auto">
                    <a href="index.php" class="btn btn-outline-secondary">Ver todos</a>
                </div>
            </form>

            <div class="text-end mb-4">
                <a href="nuevo_producto.php" class="btn btn-success">+ Nuevo Producto</a>
            </div>

            <?php if ($resultado->num_rows > 0): ?>
                <div class="row g-4">
                    <?php while ($fila = $resultado->fetch_assoc()): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm">
                                <a href="./ver_producto.php/?id_producto=<?php echo $fila['id_producto'] ?>" class="text-decoration-none">
                                    <img src="../imagenes/<?php echo $fila["imagen"]; ?>" class="card-img-top" style="height: 260px; object-fit: cover;" alt="Imagen del producto">
                                </a>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo $fila["nombre"]; ?></h5>
                                    <p class="card-text text-muted"><?php echo $fila["descripcion"]; ?></p>
                                    <ul class="list-unstyled mb-3">
                                        <li><strong>Id:</strong> <?php echo $fila["id_producto"]; ?></li>
                                        <li><strong>Precio:</strong> $<?php echo $fila["precio"]; ?></li>
                                        <li><strong>Categoría:</strong> <?php echo $fila["categoria"]; ?></li>
                                        <li><strong>Stock:</strong> <?php echo $fila["stock"]; ?></li>
                                        <li><strong>Medidas:</strong> <?php echo $fila["largo"] . "cm × " . $fila["ancho"] . "cm × " . $fila["alto"] . "cm"; ?></li>
                                    </ul>
                                    <div class="mt-auto d-flex justify-content-between">
                                        <a href="editar_producto.php?id_producto=<?php echo $fila["id_producto"]; ?>" class="btn btn-outline-primary">Editar</a>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="id_producto" value="<?php echo $fila["id_producto"]; ?>">
                                            <button type="submit" class="btn btn-outline-danger">Borrar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center" role="alert">
                    No se encontraron productos con ese criterio.
                </div>
            <?php endif; ?>

            <div class="text-center mt-5">
                <a href="../index.php" class="btn btn-outline-secondary">Volver a inicio</a>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>