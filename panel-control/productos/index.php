<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require('../../util/conexion.php');

session_start();
if (!isset($_SESSION["proveedor"])) {
    header("location: ../../login/proveedor/iniciar_sesion_proveedor");
    exit;
}
?>
<?php
include('../layout/header.php');
include('../layout/sidebar.php');

$producto_eliminado = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_producto = $_POST["id_producto"];
    $sql = "DELETE FROM productos WHERE id_producto = '$id_producto'";
    $_conexion->query($sql);
    $producto_eliminado = true;
}

// Si se hace una búsqueda (GET)
$filtro = "";
if (isset($_GET["busqueda"]) && $_GET["busqueda"] !== "") {
    $busqueda = $_GET["busqueda"];
    // Si es numérico, busca por id_producto. Si no, busca por nombre
    if (is_numeric($busqueda)) {
        $filtro = "AND id_producto = '$busqueda'";
    } else {
        $busqueda = $_conexion->real_escape_string($busqueda); // protección básica
        $filtro = "AND nombre LIKE '%$busqueda%'";
    }
}


// Consulta SQL
$sql = "SELECT * FROM productos WHERE id_proveedor = '" . $_SESSION['proveedor'] . "' $filtro";;
$resultado = $_conexion->query($sql);
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
        }

        .main-header {
            background: rgba(120, 80, 40, 0.92);
            color: #fff;
            border-radius: 0 0 1rem 1rem;
            box-shadow: 0 2px 8px rgba(120, 80, 40, 0.08);
        }

        .btn-gold {
            background: #a37030;
            color: #fff;
            border: none;
        }

        .btn-gold:hover {
            background: #7c5522;
            color: #fff;
        }

        .card-producto {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 2px 12px rgba(120, 80, 40, 0.10);
            transition: transform 0.15s;
        }

        .card-producto:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 4px 20px rgba(120, 80, 40, 0.15);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
            border-radius: 1rem 1rem 0 0;
        }

        .empty-state {
            color: #a37030;
        }

        .list-unstyled li strong {
            color: #a37030;
        }

        .valor-marron {
            color: rgb(87, 86, 85);
        }
    </style>
</head>

<body>

    <div class="container py-5">
        <div class="main-header p-4 mb-5 text-center shadow-sm">
            <h1 class="mb-1">Gestión de Productos</h1>
            <p class="mb-0">Administra, busca y edita tus productos fácilmente</p>
        </div>

        <form method="GET" class="row g-2 mb-4 justify-content-center">
            <div class="col-md-4">
                <input type="text" class="form-control" name="busqueda" placeholder="Buscar por ID o nombre"
                    value="<?php echo isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : ''; ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-gold">Buscar</button>
            </div>
            <div class="col-auto">
                <a href="./" class="btn btn-outline-secondary">Ver todos</a>
            </div>
        </form>

        <div class="text-end mb-4">
            <a href="nuevo_producto" class="btn btn-gold px-4"><i class="bi bi-plus-circle"></i> Nuevo Producto</a>
        </div>

        <?php if ($resultado->num_rows === 0): ?>
            <div class="d-flex flex-column align-items-center justify-content-center" style="height: 40vh;">
                <p class="fs-4 mb-4 text-center empty-state">
                    <i class="bi bi-box-seam fs-1 mb-2"></i><br>
                    No se encontraron productos.<br>
                    ¡Crea tu primer producto!
                </p>
                <a href="nuevo_producto" class="btn btn-gold px-4 py-2">
                    <i class="bi bi-plus-circle"></i> Nuevo producto
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <?php $medidas = json_decode($fila["medidas"], true); ?>
                    <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                        <div class="card card-producto h-100">
                            <a href="./ver_producto/?id_producto=<?php echo $fila['id_producto'] ?>"
                                class="text-decoration-none">
                                <img src="../../img/productos/<?php echo $fila["img_producto"]; ?>" class="card-img-top"
                                    alt="Imagen del producto">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title text-dark"><?php echo $fila["nombre"]; ?></h5>
                                    <p class="card-text text-muted"><?php echo $fila["descripcion"]; ?></p>
                                    <ul class="list-unstyled mb-3">
                                        <li><strong>ID:</strong> <span
                                                class="valor-marron"><?php echo $fila["id_producto"]; ?></span></li>
                                        <li><strong>Precio:</strong> <span
                                                class="valor-marron"><?php echo $fila["precio"]; ?>€</span></li>
                                        <li><strong>Categoría:</strong> <span
                                                class="valor-marron"><?php echo $fila["categoria"]; ?></span></li>
                                        <li><strong>Stock:</strong> <span
                                                class="valor-marron"><?php echo $fila["stock"]; ?></span></li>
                                        <li><strong>Medidas:</strong>
                                            <span class="valor-marron">
                                                <?php echo $medidas['largo'] . "×" . $medidas['ancho'] . "×" . $medidas['alto'] . "cm"; ?>
                                            </span>
                                        </li>
                                        <li><strong>Oferta:</strong>
                                            <span class="valor-marron">
                                                <?php
                                                if (!empty($fila['id_oferta'])) {
                                                    $sql_oferta = "SELECT nombre FROM ofertas WHERE id_oferta = {$fila['id_oferta']}";
                                                    $resultado_oferta = $_conexion->query($sql_oferta);
                                                    if ($resultado_oferta && $resultado_oferta->num_rows > 0) {
                                                        $oferta = $resultado_oferta->fetch_assoc();
                                                        echo $oferta["nombre"];
                                                    } else {
                                                        echo "Sin oferta";
                                                    }
                                                } else {
                                                    echo "Sin oferta";
                                                }
                                                ?>
                                            </span>
                                        </li>
                                    </ul>
                                    <div class="mt-auto d-flex justify-content-between">
                                        <a href="editar_producto?id_producto=<?php echo $fila["id_producto"]; ?>"
                                            class="btn btn-sm btn-gold">Editar</a>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="id_producto" value="<?php echo $fila["id_producto"]; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar"><i
                                                    class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>

    </div>
    <?php include('../../cookies.php'); ?>
    <?php include('../../udify-bot.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if (isset($_GET['creado']) && $_GET['creado'] === 'ok'): ?>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Producto creado correctamente',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                background: '#f4e5cc',
                color: '#333',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            // Limpia el parámetro creado=ok de la URL sin recargar
            if (window.history.replaceState) {
                const url = new URL(window.location);
                url.searchParams.delete('creado');
                window.history.replaceState({}, document.title, url);
            }
        <?php endif; ?>

        <?php if (isset($_GET['editado']) && $_GET['editado'] === 'ok'): ?>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Producto editado correctamente',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                background: '#f4e5cc',
                color: '#333',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            // Limpia el parámetro editado=ok de la URL sin recargar
            if (window.history.replaceState) {
                const url = new URL(window.location);
                url.searchParams.delete('editado');
                window.history.replaceState({}, document.title, url);
            }
        <?php endif; ?>

        <?php if (!empty($producto_eliminado)): ?>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Producto eliminado correctamente',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                background: '#f4e5cc',
                color: '#333',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        <?php endif; ?>
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>