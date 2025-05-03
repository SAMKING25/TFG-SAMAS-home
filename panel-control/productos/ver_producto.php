<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #F7E5CB;
        }

        .btn-primary {
            background-color: rgb(163, 112, 48) !important;
            border: 1px solid rgb(163, 112, 48) !important;
        }

        .btn-primary:hover {
            background-color: rgb(126, 88, 41) !important;
        }
    </style>
    <?php
    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    require('../../util/conexion.php');

    session_start();
    if (!isset($_SESSION["usuario"])) {
        header("location: ../../login/proveedores/iniciar_sesion_proveedor.php");
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_producto = $_POST["id_producto"];
        //  borrar el producto
        $sql = "DELETE FROM productos WHERE id_producto = '$id_producto'";
        $_conexion->query($sql);

        $sql = $_conexion->prepare("SELECT * FROM productos WHERE id_producto = $id_producto");
        $sql->execute();
        $resultado = $sql->get_result();

        while ($fila = $resultado->fetch_assoc()) {
            unlink("../../img/productos/" . $fila['img_producto']);
        }
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
            <div class="col-md-6 mb-4 bg-white">
                <img src="/img/productos/<?php echo $fila['img_producto'] ?>" alt="Product"
                    class="img-fluid rounded mb-3 product-image">
            </div>

            <!-- Detalles del producto -->
            <div class="col-md-6">
                <h2 class="mb-3">
                    <?php echo $fila['nombre'] ?>
                </h2>
                <p class="text-muted mb-4">ID:
                    <?php echo $fila['id_producto'] ?>
                </p>
                <div class="mb-3">
                    <span class="h4 me-2">
                        <?php echo $fila['precio'] ?>€
                    </span>
                </div>

                <p class="mb-4">
                    <?php echo $fila['descripcion'] ?>
                </p>

                <div class="mb-4">
                    <label for="quantity" class="form-label">Cantidad:</label>
                    <input type="number" class="form-control" id="quantity" value="<?php echo $fila['stock'] ?>"
                        style="width: 80px;" disabled>
                </div>

                <div class="mb-4">
                    <label for="dimensions" class="form-label">Dimensiones (largo,ancho,alto):</label>
                    <p id="dimensions">
                        <?php $medidas = json_decode($fila["medidas"], true); ?>    
                        <?php echo $medidas['largo'] ?>cm x
                        <?php echo $medidas['ancho'] ?>cm x
                        <?php echo $medidas['alto'] ?>cm
                    </p>
                </div>

                <div class="mb-4">
                    <label for="dimensions" class="form-label">Oferta:</label>
                    <?php
                        // Suponiendo que ya tienes $fila['id_oferta'] disponible
                        $idOferta = $fila['id_oferta'];

                        if ($idOferta !== null) {
                            $sql = "SELECT nombre FROM ofertas WHERE id_oferta = $idOferta";
                            $resultado = $_conexion->query($sql);

                            if ($resultado && $resultado->num_rows > 0) {
                                $oferta = $resultado->fetch_assoc();
                                $nombreOferta = $oferta['nombre'];
                            } else {
                                $nombreOferta = "No encontrado";
                            }
                        } else {
                            $nombreOferta = "Sin oferta";
                        }
                        ?>
                    <p id="dimensions">
                        <?php echo $nombreOferta; ?>
                    </p>
                </div>

                <form action="" method="post">
                    <a href="../editar_producto.php?id_producto=<?php echo $fila["id_producto"] ?>" class="btn
                        btn-primary btn-lg mb-3 me-2">
                        <i class=""></i> Editar
                    </a>
                    <button class="btn btn-outline-danger btn-lg mb-3 me-2" type="submit"><i
                            class="bi bi-trash3-fill"></i> Borrar</button>
                    <input type="hidden" name="id_producto" value="<?php echo $fila["id_producto"] ?>">
                </form>
            </div>
            <?php } ?>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>