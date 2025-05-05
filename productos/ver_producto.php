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
            $sql = "SELECT p.*, o.porcentaje 
                    FROM productos p
                    LEFT JOIN ofertas o ON p.id_oferta = o.id_oferta
                    WHERE p.id_producto = $id";
            $resultado = $_conexion->query($sql);

            if ($resultado->num_rows > 0) {
                $producto = $resultado->fetch_assoc();
                $medidas = json_decode($producto["medidas"], true);
                $precio = $producto["precio"];
                $porcentaje = $producto["porcentaje"];
            } else {
                echo "Producto no encontrado.";
                exit;
            }
        } else {
            echo "ID no válido.";
            exit;
        }

        // Verificar si hay oferta activa
        $hayOferta = false;
        if (!is_null($porcentaje)) {
            $hayOferta = true;
            $precioFinal = $precio * (1 - $porcentaje / 100);
        }
        ?>

        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <div class="col-md-6 position-relative">
                    <img src="../../img/productos/<?php echo $producto["img_producto"]; ?>"
                        class="img-fluid rounded shadow"
                        style="object-fit: contain; max-height: 500px; background-color: #f9f9f9; padding: 20px;">

                    <?php
                    if ($hayOferta) {
                    ?>
                        <div class="badge bg-danger text-white fs-6 py-2 px-3 rounded-3 position-absolute top-0 start-0 mt-3 ms-3">
                            ¡Descuento <?php echo $porcentaje; ?>%!
                        </div>
                    <?php
                    }
                    ?>
                </div>

                <div class="col-md-6">
                    <h1 class="fw-bold mb-3"><?php echo $producto["nombre"]; ?></h1>
                    <p class="fs-5 text-muted"><?php echo $producto["descripcion"]; ?></p>

                    <div class="d-flex align-items-center mb-4">
                        <div>
                            <?php
                            if ($hayOferta) {
                            ?>
                                <span class="text-muted text-decoration-line-through me-2 fs-5">
                                    <?php echo number_format($precio, 2, ',', '.'); ?> €
                                </span>
                                <span class="text-success fw-semibold fs-4">
                                    <?php echo number_format($precioFinal, 2, ',', '.'); ?> €
                                </span>
                            <?php
                            } else {
                            ?>
                                <span class="text-success fw-semibold fs-4">
                                    <?php echo number_format($precio, 2, ',', '.'); ?> €
                                </span>
                            <?php
                            }
                            ?>
                        </div>
                    </div>

                    <ul class="list-unstyled fs-5 mt-4">
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

                    <a href="#" class="btn btn-warning btn-lg mt-4">Agregar al carrito</a>

                    <a href="./" class="btn btn-outline-secondary mt-4">← Volver a productos</a>
                </div>
            </div>
        </div>

    </div>
</body>

</html>