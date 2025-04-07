<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        
        body {
            background-color: #F7E5CB;
        }
        .btn-outline-primary {
            color:rgb(163, 112, 48) !important;
            border:1px solid rgb(163, 112, 48) !important;
        }
        .btn-outline-primary:hover {
            background-color: rgb(163, 112, 48);
            color: white !important;
            border:1px solid white !important;
        }
    </style>
    <?php
    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    require('../util/conexion.php');

    session_start();
    if (!isset($_SESSION["usuario"])) { 
        header("location: ../../usuario/iniciar_sesion_proveedor.php");
        exit;
    }
    ?>
</head>

<body>

    <?php
    include('../layout/header.php');
    include('../layout/sidebar.php');

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

    <div class="container-fluid py-5" style="margin-left: 280px;">
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
            <div class="row g-3">
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <div class="col-12 col-md-6 col-xxl-4">
                        <div class="card h-100 shadow-sm">
                            <a href="./ver_producto.php/?id_producto=<?php echo $fila['id_producto'] ?>" class="text-decoration-none">
                                <img src="../imagenes/<?php echo $fila["imagen"]; ?>" class="card-img-top" style="height: 260px; object-fit: cover;" alt="Imagen del producto">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo $fila["nombre"]; ?></h5>
                                <p class="card-text text-muted"><?php echo $fila["descripcion"]; ?></p>
                                <ul class="list-unstyled mb-3">
                                    <li><strong>Id:</strong> <?php echo $fila["id_producto"]; ?></li>
                                    <li><strong>Precio:</strong> <?php echo $fila["precio"]; ?>€</li>
                                    <li><strong>Categoría:</strong> <?php echo $fila["categoria"]; ?></li>
                                    <li><strong>Stock:</strong> <?php echo $fila["stock"]; ?></li>
                                    <li><strong>Medidas:</strong> <?php echo $fila["largo"] . "cm × " . $fila["ancho"] . "cm × " . $fila["alto"] . "cm"; ?></li>
                                </ul>
                                <div class="mt-auto d-flex justify-content-between">
                                    <a href="editar_producto.php?id_producto=<?php echo $fila["id_producto"]; ?>" class="btn btn-outline-primary ">Editar</a>
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
    </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>