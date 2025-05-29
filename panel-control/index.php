<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link id="favicon" rel="shortcut icon" href="/img/logos/loguito_gris.png"/>
    <?php
    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    require('../util/conexion.php');

    session_start();
    if (!isset($_SESSION["proveedor"])) {
        header("location: ../login/usuario/iniciar_sesion_usuario.php");
        exit;
    }
    ?>
    <style>
        body {
            background-color: #F7E5CB;
        }

        .card-img-top {
            height: 350px;
            object-fit: cover;
            width: 100%;
        }
    </style>
</head>

<body>
    <?php
    include('./layout/header.php');
    include('./layout/sidebar.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_producto = $_POST["id_producto"];

        $sql = "DELETE FROM productos WHERE id_producto = '$id_producto'";
        $_conexion->query($sql);
    }

    $sql = "SELECT * FROM productos WHERE id_proveedor = '" . $_SESSION['proveedor'] . "'";
    $resultado = $_conexion->query($sql);
    ?>

    <div class="container-fluid py-5">
        <h1 class="text-center mb-4">Mis productos</h1>
        <div class="cards row">
            <?php
            while ($fila = $resultado->fetch_assoc()) { ?>

                <div class="col-12 col-md-6 col-lg-4 col-xl-4 col-xxl-3 mb-4">
                    <a href="./productos/ver_producto.php/?id_producto=<?php echo $fila['id_producto'] ?>"
                        class="text-decoration-none">
                        <div class="card h-100 shadow-sm">
                            <img src="../img/productos/<?php echo $fila['img_producto']; ?>" class="card-img-top"
                                alt="<?php echo $fila['nombre']; ?>">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <?php echo $fila['nombre']; ?>
                                </h5>
                                <p class="card-text">
                                    <?php echo $fila['descripcion']; ?>
                                </p>
                                <p class="card-text"><strong>
                                        <?php echo number_format($fila['precio'], 2); ?>€
                                    </strong></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>

        </div>
    </div>
    <?php include('../cookies.php'); ?>
    <?php include('../udify-bot.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>