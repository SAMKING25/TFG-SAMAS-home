<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <?php
    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    require('util/conexion.php');

    // session_start();
    // if (isset($_SESSION["usuario"])) {
    //     echo "<h2>Bienvenid@ " . $_SESSION["usuario"] .  "</h2>"; 
    ?>
    <!-- <a class="btn btn-warning" href="usuario/cerrar_sesion.php">Cerrar sesion</a> 
            <a class="btn btn-primary" href="usuario/cambiar_credenciales.php?usuario=<?php // echo $_SESSION["usuario"] 
                                                                                        ?>">Cambiar credenciales</a> -->
    <?php  // } else { 
    ?>
    <!-- <a class="btn btn-warning" href="usuario/iniciar_sesion.php">Iniciar sesion</a> -->
    <?php // } 
    ?>
</head>

<body>
    <header class="p-3 bg-dark text-white">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                    <li><a href="#" class="nav-link px-2 text-secondary">Inicio</a></li>
                    <li><a href="#" class="nav-link px-2 text-white">Productos</a></li>
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
                <img src="imagenes/samas-home-logo.png" class="w-50" alt="">
                <span class="fs-4">SAMAS home</span>
            </a>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="#" class="nav-link active" aria-current="page">
                        <i class="bi bi-house-door me-2"></i>
                        Inicio
                    </a>
                </li>
                <li>
                    <a href="./productos/index.php" class="nav-link link-dark">
                        <i class="bi bi-archive me-2"></i>
                        Productos
                    </a>
                </li>
                <li>
                    <a href="./productos/nuevo_producto.php" class="nav-link link-dark">
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
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id_producto = $_POST["id_producto"];

            $sql = "DELETE FROM productos WHERE id_producto = '$id_producto'";
            $_conexion->query($sql);
        }

        $sql = "SELECT * FROM productos";
        $resultado = $_conexion->query($sql);
        ?>

        <div class="container py-5">
            <h1>Mis productos</h1>
            <div class="mb-3">
                <?php if (isset($_SESSION["usuario"])) { ?>
                    <a href="productos/index.php" class="btn btn-success">Ir a tabla de productos</a>
                <?php } ?>

            </div>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

            <div class="cards row">
                <?php
                while ($fila = $resultado->fetch_assoc()) { ?>

                <div class="col-md-4 mb-4">
                    <a href="./productos/ver_producto.php/?id_producto=<?php echo $fila['id_producto'] ?>" class="text-decoration-none">
                        <div class="card h-100 shadow-sm">
                            <img src="imagenes/<?php echo $fila['imagen']; ?>" class="card-img-top" alt="<?php echo $fila['nombre']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $fila['nombre']; ?></h5>
                                <p class="card-text"><?php echo $fila['descripcion']; ?></p>
                                <p class="card-text"><strong><?php echo number_format($fila['precio'], 2); ?>€</strong></p>
                            </div>
                        </div>
                    </a>
                </div>


                <!-- <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="imagenes/<?php echo $fila['imagen']; ?>" class="card-img-top" alt="<?php echo $fila['nombre']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $fila['nombre']; ?></h5>
                            <p class="card-text"><?php echo $fila['descripcion']; ?></p>
                            <p class="card-text"><strong><?php echo number_format($fila['precio'], 2); ?>€</strong></p>
                            <a href="#" class="btn btn-primary">Añadir al carrito</a>
                        </div>
                    </div>
                </div> -->

                <!-- <div class="card" style="max-width: 320px">
                    <img src="imagenes/<?php echo $fila["imagen"] ?>" class="card-img-top" alt="<?php echo $fila["nombre"] ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $fila["nombre"] ?></h5>
                        <p class="card-text"><?php echo $fila["descripcion"] ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0"><?php echo $fila["precio"] ?>€</span>
                            <div>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-half text-warning"></i>
                                <small class="text-muted">(4.5)</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between bg-light">
                        <button class="btn btn-primary btn-sm">Añadir al carrito</button>
                        <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-heart"></i></button>
                    </div>
                </div> -->
                <?php } ?>
            </div>
            <!-- <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Categoria</th>
                        <th>Stock</th>
                        <th>Descripcion</th>
                        <th>Medidas</th>
                        <th>Imagen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($fila = $resultado->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $fila["nombre"] . "</td>";
                        echo "<td>" . $fila["precio"] . "</td>";
                        echo "<td>" . $fila["categoria"] . "</td>";
                        echo "<td>" . $fila["stock"] . "</td>";
                    ?>
                        <td><?php echo $fila["descripcion"] ?></td>
                        <td>
                            <?php
                            echo $fila["largo"] . "cm x" . $fila["ancho"] . "cm x" . $fila["alto"] . "cm";
                            ?>
                        </td>
                        <td>
                            <img width="160" height="200" src="imagenes/<?php echo $fila["imagen"] ?>">
                        </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table> -->

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>