<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require('../../util/conexion.php');

session_start();
if (!isset($_SESSION["proveedor"])) {
    header("location: ../../login/proveedor/iniciar_sesion_proveedor");
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
    header("location: ../");
}

include("../layout/header.php");
include("../layout/sidebar.php");

$id_producto = $_GET['id_producto'];

$sql = $_conexion->prepare("SELECT * FROM productos WHERE id_producto = $id_producto");
$sql->execute();
$resultado = $sql->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link id="favicon" rel="shortcut icon" href="/img/logos/loguito_gris.png" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #F7E5CB;
            /* font-family: 'Playfair Display', serif; */
            color: #4B3A1E;
        }

        .product-card {
            background: #fff8ef;
            border-radius: 24px;
            max-width: 1100px;
            /* antes 800px */
            width: 100%;
            position: relative;
            overflow: visible;
            padding: 2.5rem 2.5rem;
            /* añade padding para que el contenido respire */
        }

        .product-image {
            box-shadow: 0 4px 24px rgba(163, 112, 48, 0.15);
            border: 4px solid #fff;
            background: #fff;
            padding: 10px;
            max-height: 420px;
            object-fit: contain;
            transition: transform 0.2s;
        }

        .product-image:hover {
            transform: scale(1.03);
            box-shadow: 0 8px 32px rgba(163, 112, 48, 0.25);
        }

        .btn-back {
            position: absolute;
            top: -18px;
            left: -18px;
            background: #fff8ef;
            border: 2px solid #A37030;
            color: #A37030;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            z-index: 2;
            transition: background 0.2s, color 0.2s, border 0.2s;
        }

        .btn-back:hover {
            background: #A37030;
            color: #fff;
            border-color: #7E5829;
        }

        .card-title {
            color: #A37030;
            font-weight: 700;
            letter-spacing: 1px;
            font-size: 2rem;
        }

        .precio-box {
            background: #F7E5CB;
            border-radius: 12px;
            display: inline-block;
            padding: 0.4rem 1.2rem;
        }

        .precio {
            color: #7E5829;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .label {
            color: #A37030;
            font-weight: 600;
            margin-right: 0.3rem;
        }

        .value {
            color: #4B3A1E;
            font-weight: 500;
        }

        .btn-primary {
            background-color: #A37030 !important;
            border: 1px solid #A37030 !important;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: background 0.2s, border 0.2s;
        }

        .btn-primary:hover {
            background-color: #7E5829 !important;
            border: 1px solid #7E5829 !important;
        }

        .btn-outline-danger {
            border-color: #A37030 !important;
            color: #A37030 !important;
            background: transparent !important;
            transition: background 0.2s, color 0.2s;
        }

        .btn-outline-danger:hover {
            background: #A37030 !important;
            color: #fff !important;
        }

        @media (max-width: 767px) {
            .product-card {
                border-radius: 14px;
                max-width: 98vw;
            }

            .btn-back {
                top: -10px;
                left: -10px;
                width: 40px;
                height: 40px;
                font-size: 1.1rem;
            }
        }
    </style>
</head>

<body>
    <div class="container my-5 d-flex justify-content-center align-items-center min-vh-80">
        <?php while ($fila = $resultado->fetch_assoc()) { ?>
            <div class="card product-card shadow-lg border-0">
                <!-- Botón flotante de volver -->
                <a href="../" class="btn btn-back shadow-sm">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div class="row g-0">
                    <!-- Imagen del producto -->
                    <div class="col-md-5 d-flex align-items-center justify-content-center p-4">
                        <img src="/img/productos/<?php echo $fila['img_producto'] ?>" alt="Producto"
                            class="img-fluid rounded product-image">
                    </div>
                    <!-- Detalles del producto -->
                    <div class="col-md-7">
                        <div class="card-body p-4">
                            <h2 class="card-title mb-2"><?php echo $fila['nombre'] ?></h2>
                            <p class="text-muted mb-2 small"><b>ID:</b> <?php echo $fila['id_producto'] ?></p>
                            <div class="precio-box mb-3">
                                <span class="precio"><?php echo $fila['precio'] ?>€</span>
                            </div>
                            <p class="mb-3"><?php echo $fila['descripcion'] ?></p>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <span class="label">Stock:</span>
                                    <span class="value"><?php echo $fila['stock'] ?></span>
                                </div>
                                <div class="col-6">
                                    <span class="label">Oferta:</span>
                                    <span class="value">
                                        <?php
                                        $idOferta = $fila['id_oferta'];
                                        if ($idOferta !== null) {
                                            $sql = "SELECT nombre FROM ofertas WHERE id_oferta = $idOferta";
                                            $resultadoOferta = $_conexion->query($sql);
                                            if ($resultadoOferta && $resultadoOferta->num_rows > 0) {
                                                $oferta = $resultadoOferta->fetch_assoc();
                                                $nombreOferta = $oferta['nombre'];
                                            } else {
                                                $nombreOferta = "No encontrado";
                                            }
                                        } else {
                                            $nombreOferta = "Sin oferta";
                                        }
                                        echo $nombreOferta;
                                        ?>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-4">
                                <span class="label">Dimensiones:</span>
                                <span class="value">
                                    <?php $medidas = json_decode($fila["medidas"], true); ?>
                                    <?php echo $medidas['largo'] ?>cm x
                                    <?php echo $medidas['ancho'] ?>cm x
                                    <?php echo $medidas['alto'] ?>cm
                                </span>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="../editar_producto?id_producto=<?php echo $fila["id_producto"] ?>"
                                    class="btn btn-primary flex-fill">
                                    <i class="bi bi-pencil-square"></i> Editar
                                </a>
                                <form action="" method="post" class="flex-fill m-0">
                                    <input type="hidden" name="id_producto" value="<?php echo $fila["id_producto"] ?>">
                                    <button class="btn btn-outline-danger w-100" type="submit">
                                        <i class="bi bi-trash3-fill"></i> Borrar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    </div>
    <?php include('../../cookies.php'); ?>
    <?php include('../../udify-bot.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>