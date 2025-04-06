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
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_producto = $_POST["id_producto"];
        //  borrar el producto
        $sql = "DELETE FROM productos WHERE id_producto = '$id_producto'";
        $_conexion->query($sql);

        header("location: ../index.php");
    }
    ?>
</head>

<body>
    <?php
    include("../layout/header.php");
    include("../layout/sidebar.php");

    $id_producto = $_GET['id_producto'];

    $sql = $_conexion->prepare("SELECT * FROM productos WHERE id_producto = $id_producto");
    $sql->execute();
    $resultado = $sql->get_result();

    ?>
    <div class="container mt-5">
        <div class="row">
            <?php while ($fila = $resultado->fetch_assoc()) { ?>
                <!-- Imagen del producto -->
                <div class="col-md-6 mb-4">
                    <img src="../../imagenes/<?php echo $fila['imagen'] ?>" alt="Product" class="img-fluid rounded mb-3 product-image">
                </div>

                <!-- Detalles del producto -->
                <div class="col-md-6">
                    <h2 class="mb-3"><?php echo $fila['nombre'] ?></h2>
                    <p class="text-muted mb-4">ID: <?php echo $fila['id_producto'] ?></p>
                    <div class="mb-3">
                        <span class="h4 me-2"><?php echo $fila['precio'] ?>€</span>
                    </div>

                    <div class="mb-3">
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-half text-warning"></i>
                        <span class="ms-2">4.5 (120 reviews)</span>
                    </div>

                    <p class="mb-4"><?php echo $fila['descripcion'] ?></p>

                    <div class="mb-4">
                        <label for="quantity" class="form-label">Cantidad:</label>
                        <input type="number" class="form-control" id="quantity" value="<?php echo $fila['stock'] ?>" style="width: 80px;" disabled>
                    </div>

                    <div class="mb-4">
                        <label for="dimensions" class="form-label">Dimensiones (largo,ancho,alto):</label>
                        <p id="dimensions"><?php echo $fila['largo'] ?>cm x <?php echo $fila['ancho'] ?>cm x <?php echo $fila['alto'] ?>cm</p>
                    </div>

                    <form action="" method="post">
                        <a href="../editar_producto.php?id_producto=<?php echo $fila["id_producto"] ?>" class="btn btn-primary btn-lg mb-3 me-2">
                            <i class="bi bi-cart-plus"></i> Editar
                        </a>
                        <button class="btn btn-outline-danger btn-lg mb-3 me-2" type="submit"><i class="bi bi-trash3-fill"></i> Borrar</button>
                        <input type="hidden" name="id_producto" value="<?php echo $fila["id_producto"] ?>">
                    </form>
                </div>
            <?php } ?>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>