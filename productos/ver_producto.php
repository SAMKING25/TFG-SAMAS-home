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
    <!-- Archivo CSS personalizado -->
    <link rel="stylesheet" href="/css/landing.css" />
    <!--search-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <!--conexion con BD-->
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
        <?php
        if (isset($_GET["id_producto"])) {
            $id = intval($_GET["id_producto"]);
            $sql = "SELECT * FROM productos WHERE id_producto = $id";
            $resultado = $_conexion->query($sql);

            if ($resultado->num_rows > 0) {
                $producto = $resultado->fetch_assoc();
                $medidas = json_decode($producto["medidas"], true);
            } else {
                echo "Producto no encontrado.";
                exit;
            }
        } else {
            echo "ID no válido.";
            exit;
        }
        ?>

        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <div class="col-md-6">
                    <img src="../../img/productos/<?php echo $producto["img_producto"]; ?>"
                        class="img-fluid rounded shadow"
                        style="object-fit: contain; max-height: 500px; background-color: #f9f9f9; padding: 20px;">
                </div>

                <div class="col-md-6">
                    <h1 class="fw-bold mb-3"><?php echo $producto["nombre"]; ?></h1>
                    <p class="fs-5 text-muted"><?php echo $producto["descripcion"]; ?></p>

                    <h2 class="text-success fw-semibold mb-4"><?php echo number_format($producto["precio"], 2); ?> €</h2>

                    <ul class="list-unstyled fs-5">
                        <li><strong>Categoría:</strong> <?php echo $producto["categoria"]; ?></li>
                        <li><strong>Stock:</strong>
                            <?php
                            if ($producto["stock"] > 0) {
                                echo "Disponible";
                            } else {
                                echo "No hay stock actualmente";
                            }
                            ?>
                        </li>

                        <li><strong>Medidas:</strong> <?php echo "{$medidas['largo']}cm × {$medidas['ancho']}cm × {$medidas['alto']}cm"; ?></li>
                    </ul>

                    <a href="./" class="btn btn-outline-secondary mt-4">← Volver a productos</a>
                </div>
            </div>
        </div>

    </div>
</body>

</html>