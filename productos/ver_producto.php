<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require('../util/conexion.php');
session_start();

// Procesar solicitud POST para añadir al carrito
if (isset($_GET["id_producto"])) {
    $id = intval($_GET["id_producto"]);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!isset($_SESSION["usuario"])) {
            header("Location: ../login/usuario/iniciar_sesion_usuario.php");
            exit;
        }

        $id_producto = intval($_GET["id_producto"]);
        $id_usuario = $_SESSION["usuario"];
        $cantidad = $_POST["cantidad"];

        $stmt = $_conexion->prepare("INSERT INTO carrito (id_usuario, id_producto, cantidad) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $id_usuario, $id_producto, $cantidad);

        if ($stmt->execute()) {
            $mensaje = "success";
        } else {
            $mensaje = "error";
            $errorMsg = $stmt->error;
        }

        $stmt->close();
    }

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
        die("Producto no encontrado.");
    }
} else {
    die("ID no válido.");
}

$hayOferta = !is_null($porcentaje);
if ($hayOferta) {
    $precioFinal = $precio * (1 - $porcentaje / 100);
}
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
    <!-- Archivo CSS personalizado -->
    <link rel="stylesheet" href="/css/landing.css" />
    <!--search-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />

    <style>
        .img-fija {
            width: 550px !important;
            height: 450px !important;
            object-fit: fill !important;
            display: block;
        }

        .img-similar {
            width: 100%;
            height: 250px;
            object-fit: contain;
            background-color: #f8f8f8;
            padding: 10px;
            display: block;
            margin: 0 auto;
        }

        .img-similar-wrapper {
            width: 100%;
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f8f8;
            border-radius: 12px;
            overflow: hidden;
        }
    </style>
</head>

<body>
    <?php include('../navbar.php'); ?>

    <div class="container">
        <?php
        if (isset($mensaje)) {
            if ($mensaje == "success") {
                echo '<div class="alert alert-success mt-3" role="alert">Producto añadido al carrito.</div>';
            } else {
                echo '<div class="alert alert-danger mt-3" role="alert">Error al añadir el producto al carrito: ' . $errorMsg . '</div>';
            }
        }
        ?>

        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <div class="col-md-6 position-relative">
                    <img class="img-fija" src="../../img/productos/<?php echo $producto["img_producto"]; ?>"
                        class="img-fluid rounded shadow"
                        style="object-fit: contain; max-height: 500px; background-color: #f9f9f9; padding: 20px;">

                    <?php if ($hayOferta): ?>
                        <div class="badge bg-danger text-white fs-6 py-2 px-3 rounded-3 position-absolute top-0 start-0 mt-3 ms-3">
                            ¡Descuento <?php echo $porcentaje; ?>%!
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <h1 class="fw-bold mb-3"><?php echo $producto["nombre"]; ?></h1>
                    <p class="fs-5 text-muted"><?php echo $producto["descripcion"]; ?></p>

                    <div class="d-flex align-items-center mb-4">
                        <div>
                            <?php if ($hayOferta): ?>
                                <span class="text-muted text-decoration-line-through me-2 fs-5">
                                    <?php echo number_format($precio, 2, ',', '.'); ?> €
                                </span>
                                <span class="text-success fw-semibold fs-4">
                                    <?php echo number_format($precioFinal, 2, ',', '.'); ?> €
                                </span>
                            <?php else: ?>
                                <span class="text-success fw-semibold fs-4">
                                    <?php echo number_format($precio, 2, ',', '.'); ?> €
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <ul class="list-unstyled fs-5 mt-4">
                        <li><strong>Categoría:</strong> <?php echo $producto["categoria"]; ?></li>
                        <li><strong>Stock:</strong>
                            <?php echo $producto["stock"] > 0 ? "Disponible" : "No hay stock actualmente"; ?>
                        </li>
                        <li><strong>Medidas:</strong> <?php echo "{$medidas['largo']}cm × {$medidas['ancho']}cm × {$medidas['alto']}cm"; ?></li>
                    </ul>

                    <form action="" method="post">
                        <select name="cantidad" id="cantidad" class="form-select form-select-lg w-auto">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                        <button type="submit" class="btn btn-warning btn-lg mt-4">Añadir al carrito</button>
                        <a href="./" class="btn btn-outline-secondary mt-4">← Volver a productos</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
<!-- Productos similares -->
<div class="container mt-5">
    <h3 class="mb-4">Productos similares</h3>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
        <?php
        $categoria = $producto["categoria"];
        $id_actual = $producto["id_producto"];

        $sql_similares = "SELECT p.*, o.porcentaje 
                          FROM productos p 
                          LEFT JOIN ofertas o ON p.id_oferta = o.id_oferta 
                          WHERE p.categoria = ? AND p.id_producto != ?";

        $stmt_similares = $_conexion->prepare($sql_similares);
        $stmt_similares->bind_param("si", $categoria, $id_actual);
        $stmt_similares->execute();
        $result_similares = $stmt_similares->get_result();

        while ($sim = $result_similares->fetch_assoc()):
            $hayOfertaSim = !is_null($sim["porcentaje"]);
            $precioFinalSim = $hayOfertaSim ? $sim["precio"] * (1 - $sim["porcentaje"] / 100) : $sim["precio"];
        ?>
            <div class="col">
                <a href="ver_producto.php?id_producto=<?php echo $sim["id_producto"]; ?>" class="text-decoration-none text-dark">
                    <div class="card h-100 shadow-sm border-0 rounded-4 position-relative">
                        <?php if ($hayOfertaSim): ?>
                            <span class="position-absolute top-0 end-0 bg-danger text-white px-2 py-1 rounded-start">
                                -<?php echo $sim["porcentaje"]; ?>%
                            </span>
                        <?php endif; ?>

                        <div class="img-similar-wrapper">
                            <img src="../../img/productos/<?php echo $sim["img_producto"]; ?>"
                                class="img-similar"
                                alt="Producto similar">
                        </div>


                        <div class="card-body text-center">
                            <h6 class="card-title fw-bold mb-2"><?php echo $sim["nombre"]; ?></h6>
                            <div class="card-text fs-6">
                                <?php if ($hayOfertaSim): ?>
                                    <span class="text-muted text-decoration-line-through me-2">
                                        <?php echo number_format($sim["precio"], 2, ',', '.'); ?> €
                                    </span>
                                    <span class="text-success fw-semibold">
                                        <?php echo number_format($precioFinalSim, 2, ',', '.'); ?> €
                                    </span>
                                <?php else: ?>
                                    <span class="text-success fw-semibold">
                                        <?php echo number_format($sim["precio"], 2, ',', '.'); ?> €
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endwhile; ?>
        <?php $stmt_similares->close(); ?>
    </div>
</div>

</html>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>