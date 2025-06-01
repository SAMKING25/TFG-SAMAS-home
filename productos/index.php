<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require('../util/conexion.php');
session_start();

// --- PROCESAR AÑADIR AL CARRITO ANTES DE CUALQUIER HTML ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_to_cart_id"])) {
    $isAjax = (
        isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    );
    if ($isAjax) {
        header('Content-Type: application/json');
    }

    if (!isset($_SESSION["usuario"])) {
        if ($isAjax) {
            echo json_encode(['status' => 'login', 'url' => '../login/usuario/iniciar_sesion_usuario']);
            exit;
        } else {
            header("Location: ../login/usuario/iniciar_sesion_usuario");
            exit;
        }
    }
    $id_producto = intval($_POST["add_to_cart_id"]);
    $id_usuario = $_SESSION["usuario"];
    $cantidad = 1;

    // Consulta stock actual del producto
    $resStock = $_conexion->query("SELECT stock FROM productos WHERE id_producto = $id_producto");
    $rowStock = $resStock->fetch_assoc();
    $stock = $rowStock ? intval($rowStock["stock"]) : 0;

    // Consulta cuántos ya tiene el usuario en el carrito
    $stmtCarrito = $_conexion->prepare("SELECT cantidad FROM carrito WHERE id_usuario = ? AND id_producto = ?");
    $stmtCarrito->bind_param("ii", $id_usuario, $id_producto);
    $stmtCarrito->execute();
    $stmtCarrito->bind_result($cantidad_en_carrito);
    $stmtCarrito->fetch();
    $stmtCarrito->close();

    if (!isset($cantidad_en_carrito)) {
        $cantidad_en_carrito = 0;
    }

    if ($stock <= 0) {
        if ($isAjax) {
            echo json_encode(['status' => 'error', 'message' => 'No hay suficiente stock disponible.']);
            exit;
        } else {
            $mensaje = "error";
            $errorMsg = "No hay suficiente stock disponible.";
        }
    } elseif ($cantidad_en_carrito >= $stock) {
        if ($isAjax) {
            echo json_encode(['status' => 'error', 'message' => 'Ya tienes el máximo stock permitido en tu carrito.']);
            exit;
        } else {
            $mensaje = "error";
            $errorMsg = "Ya tienes el máximo stock permitido en tu carrito.";
        }
    } elseif (($cantidad + $cantidad_en_carrito) > $stock) {
        if ($isAjax) {
            echo json_encode(['status' => 'error', 'message' => "No puedes añadir más de $stock unidades en total."]);
            exit;
        } else {
            $mensaje = "error";
            $errorMsg = "No puedes añadir más de $stock unidades en total.";
        }
    } else {
        $stmt = $_conexion->prepare(
            "INSERT INTO carrito (id_usuario, id_producto, cantidad) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE cantidad = cantidad + VALUES(cantidad)"
        );
        $stmt->bind_param("iii", $id_usuario, $id_producto, $cantidad);

        if ($stmt->execute()) {
            if ($isAjax) {
                echo json_encode(['status' => 'success']);
                error_log("AJAX respuesta: " . json_encode(['status' => 'success']));
                exit;
            } else {
                $mensaje = "success";
            }
        } else {
            if ($isAjax) {
                echo json_encode(['status' => 'error', 'message' => $stmt->error]);
                exit;
            } else {
                $mensaje = "error";
                $errorMsg = $stmt->error;
            }
        }
        $stmt->close();
    }
} // <-- ESTA LLAVE ES IMPORTANTE
// --- FIN BLOQUE ---

// Obtener valores únicos para los filtros y guardarlos en arrays
function fetchAllValues($result, $key, $conexion)
{
    if ($result === false) {
        die("Error en la consulta: " . $conexion->error);
    }
    $values = [];
    while ($row = $result->fetch_assoc()) {
        $values[] = $row[$key];
    }
    return $values;
}

$categorias_arr = fetchAllValues($_conexion->query("SELECT DISTINCT categoria FROM productos ORDER BY categoria"), 'categoria', $_conexion);

// Obtener valores únicos de medidas desde el JSON
$anchos_arr = [];
$altos_arr = [];
$largos_arr = [];

$res = $_conexion->query("SELECT medidas FROM productos");
while ($row = $res->fetch_assoc()) {
    $medidas = json_decode($row['medidas'], true);
    if (isset($medidas['ancho']))
        $anchos_arr[] = $medidas['ancho'];
    if (isset($medidas['alto']))
        $altos_arr[] = $medidas['alto'];
    if (isset($medidas['largo']))
        $largos_arr[] = $medidas['largo'];
}
$anchos_arr = array_unique($anchos_arr);
$altos_arr = array_unique($altos_arr);
$largos_arr = array_unique($largos_arr);
sort($anchos_arr);
sort($altos_arr);
sort($largos_arr);

// --- AQUI DEBE IR EL BLOQUE DE CONTEO --

// Contar productos por categoría
$categorias_count = [];
$res = $_conexion->query("SELECT categoria, COUNT(*) as total FROM productos GROUP BY categoria");
while ($row = $res->fetch_assoc()) {
    $categorias_count[trim($row['categoria'])] = $row['total'];
}

// Contar productos por ancho, alto, largo (desde JSON)
$anchos_count = [];
$altos_count = [];
$largos_count = [];
$res = $_conexion->query("SELECT medidas FROM productos");
while ($row = $res->fetch_assoc()) {
    $medidas = json_decode($row['medidas'], true);
    if (isset($medidas['ancho'])) {
        $key = (string) $medidas['ancho'];
        $anchos_count[$key] = ($anchos_count[$key] ?? 0) + 1;
    }
    if (isset($medidas['alto'])) {
        $key = (string) $medidas['alto'];
        $altos_count[$key] = ($altos_count[$key] ?? 0) + 1;
    }
    if (isset($medidas['largo'])) {
        $key = (string) $medidas['largo'];
        $largos_count[$key] = ($largos_count[$key] ?? 0) + 1;
    }
}

// Definir array de filtros
$filtros = [
    'categoria' => isset($_GET['categoria']) ? $_GET['categoria'] : '',
    'precio_min' => isset($_GET['precio_min']) ? $_GET['precio_min'] : '',
    'precio_max' => isset($_GET['precio_max']) ? $_GET['precio_max'] : '',
    'ancho' => isset($_GET['ancho']) ? $_GET['ancho'] : '',
    'alto' => isset($_GET['alto']) ? $_GET['alto'] : '',
    'largo' => isset($_GET['largo']) ? $_GET['largo'] : '',
];
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
    <link id="favicon" rel="shortcut icon" href="/img/logos/loguito_gris.png" />
    <link rel="stylesheet" href="/css/landing.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />

    <style>
        body {
            background: linear-gradient(120deg, #f8f6f2 0%, #f4e5cc 100%);
            font-family: 'Segoe UI', 'Roboto', Arial, sans-serif;
            color: #222;
        }

        .main-content {
            padding-bottom: 3.5rem !important;
        }

        h1 {
            font-weight: 600;
            color: black;
            letter-spacing: 1px;
            text-shadow: 0 2px 8px #e5d6b6;
        }

        /* Tarjeta de producto */
        .card {
            border: none;
            border-radius: 2rem;
            box-shadow: 0 4px 24px 0 #bfa16a22;
            transition: box-shadow 0.25s, transform 0.2s;
            background: #fff;
            overflow: hidden;
            position: relative;
        }

        .card:hover,
        .card:focus-within {
            box-shadow: 0 8px 32px 0 #bfa16a44;
            transform: translateY(-4px) scale(1.02);
        }

        .card-img-container {
            width: 100%;
            height: 340px;
            /* Más grande en escritorio */
            background: #f8f8f8;
            display: flex;
            align-items: center;
            justify-content: center;
            border-top-left-radius: 2rem;
            border-top-right-radius: 2rem;
            position: relative;
            overflow: hidden;
        }

        .img-fija {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            display: block;
            margin: 0 auto;
            transition: transform 0.35s cubic-bezier(.4, 2, .3, 1), filter 0.3s;
            border-radius: 0;
        }

        @media (max-width: 991px) {
            .card-img-container {
                height: 260px;
            }
        }

        @media (max-width: 767.98px) {
            .card-img-container {
                height: 220px !important;
                padding: 0 !important;
            }

            .img-fija {
                width: 100% !important;
                height: 100% !important;
                object-fit: cover !important;
                margin: 0 !important;
                display: block;
                border-radius: 0 !important;
                background: #f8f8f8;
            }
        }

        .add-cart-btn {
            position: absolute;
            right: 18px;
            bottom: 18px;
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, #bfa16a 60%, #ffc25a 100%);
            color: #fff;
            border: none;
            border-radius: 50%;
            box-shadow: 0 2px 12px 0 #bfa16a55;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.7rem;
            transition: background 0.2s, color 0.2s, transform 0.15s;
            z-index: 10;
            padding: 0;
        }

        .add-cart-btn:hover,
        .add-cart-btn:focus {
            background: linear-gradient(135deg, #a88c54 60%, #ffb340 100%);
            color: #fff;
            transform: scale(1.12);
            outline: none;
        }

        .card-title {
            color: #222;
            font-weight: 700;
            font-size: 1.18rem;
            margin-bottom: 0.5rem;
        }

        .card-text .text-success {
            color: brown !important;
            font-weight: 700;
            font-size: 1.15rem;
        }

        .card-text .text-muted {
            color: #bfa16a !important;
            font-size: 1.05rem;
        }

        /* Etiqueta de oferta */
        .card .bg-danger {
            background: linear-gradient(90deg, #bfa16a 60%, #ffc25a 100%) !important;
            color: #fff !important;
            font-weight: 600;
            font-size: 1rem;
            border-top-right-radius: 0.8rem !important;
            border-bottom-left-radius: 0.8rem !important;
            box-shadow: 0 2px 8px #bfa16a33;
        }

        /* Filtros laterales */
        .offcanvas-body {
            background: #fff;
            border-radius: 1.5rem;
            box-shadow: 0 2px 16px #bfa16a22;
            padding: 2rem 1.5rem;
        }

        .offcanvas-title {
            color: #bfa16a;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .form-label {
            color: #222;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        .form-select,
        .form-control {
            border-radius: 1rem;
            border: 1.5px solid #e5e7eb;
            background: #f8f6f2;
            color: #222;
            font-size: 1rem;
            transition: border 0.2s;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: #bfa16a;
            background: #fffbe6;
            outline: none;
        }

        .btn-primary {
            background: linear-gradient(90deg, #bfa16a 60%, #ffc25a 100%) !important;
            border: none !important;
            color: #fff !important;
            font-weight: 700;
            border-radius: 2rem !important;
            box-shadow: 0 2px 8px #bfa16a33;
            letter-spacing: 0.5px;
            transition: background 0.2s, color 0.2s;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background: linear-gradient(90deg, #a88c54 60%, #ffb340 100%) !important;
            color: #fff !important;
        }

        /* Buscador */
        .search-bar-container {
            border-radius: 2rem;
            border: 1.5px solid #e5e7eb;
            background: #fff;
            box-shadow: 0 2px 8px #bfa16a22;
            padding: 0.7rem 1.2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .search-bar-container input[type="text"] {
            min-width: 220px;
            border-radius: 1.2rem;
            border: none;
            background: #f8f6f2;
            font-size: 1.08rem;
            color: #222;
            padding: 0.6rem 1rem;
            transition: background 0.2s;
        }

        .search-bar-container input[type="text"]:focus {
            background: #fffbe6;
            outline: none;
        }

        .search-btn-yellow {
            background: linear-gradient(90deg, #bfa16a 60%, #ffc25a 100%);
            color: #fff !important;
            border: none;
            border-radius: 2rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px #bfa16a33;
            transition: background 0.2s, color 0.2s;
        }

        .search-btn-yellow:hover,
        .search-btn-yellow:focus {
            background: linear-gradient(90deg, #a88c54 60%, #ffb340 100%);
            color: #fff !important;
        }

        .ver-todos-btn {
            border: 1.5px solid #e5e7eb;
            background: #fff;
            color: #64748b;
            font-weight: 500;
            border-radius: 2rem;
            letter-spacing: 0.2px;
            padding-left: 1.2rem !important;
            padding-right: 1.2rem !important;
            transition: background 0.2s, color 0.2s, border 0.2s;
        }

        .ver-todos-btn:hover {
            background: #f5f5f5;
            color: #2563eb;
            border-color: #bcd0ee;
            text-decoration: none;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .main-content {
                padding-bottom: 2rem !important;
            }

            .card-img-container {
                height: 220px;
            }

            .offcanvas-body {
                padding: 1.2rem 0.7rem;
            }
        }

        @media (max-width: 600px) {
            .search-bar-container {
                flex-direction: column;
                align-items: stretch;
                gap: 0.5rem;
                padding: 0.7rem 0.5rem;
            }

            .card-img-container {
                height: 160px;
            }

            .add-cart-btn {
                width: 42px;
                height: 42px;
                font-size: 1.2rem;
                right: 10px;
                bottom: 10px;
            }
        }

        @media (max-width: 767.98px) {

            /* Badge de oferta más grande y cuadrado */
            .card .bg-danger,
            .card .position-absolute.bg-danger {
                font-size: 1.2rem !important;
                padding: 0.5em 1.1em !important;
                border-top-right-radius: 1rem !important;
                border-bottom-left-radius: 1rem !important;
            }

            /* Botón añadir al carrito más grande y cuadrado */
            .add-cart-btn {
                width: 56px !important;
                height: 56px !important;
                font-size: 1.7rem !important;
                border-radius: 12px !important;
                right: 12px !important;
                bottom: 12px !important;
                min-width: 0 !important;
                justify-content: center !important;
                align-items: center !important;
                display: flex !important;
                padding: 0 !important;
            }

            /* Imagen de producto más grande en la card */
            .card-img-container {
                height: 220px !important;
                padding: 0 !important;
            }

            .img-fija {
                width: 100% !important;
                height: 100% !important;
                object-fit: cover !important;
                /* Aprovecha todo el espacio lateral */
                margin: 0 !important;
                display: block;
                border-radius: 0 !important;
                background: #f8f8f8;
            }

            .search-bar-container .bi-search {
                display: none !important;
            }
        }
    </style>

</head>

<body>
    <?php include('../navbar.php'); ?>

    <!-- Offcanvas Filtro Lateral (siempre disponible) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasFiltro" aria-labelledby="offcanvasFiltroLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasFiltroLabel"><i class="bi bi-funnel-fill me-2"></i>Filtrar</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
        </div>
        <div class="offcanvas-body">
            <form method="GET">
                <!-- Categoría -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Categoría</label>
                    <select name="categoria" class="form-select">
                        <option value="">Todas</option>
                        <?php foreach ($categorias_arr as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" <?= $filtros['categoria'] === $cat ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat) ?> (<?= $categorias_count[$cat] ?? 0 ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Precio (dos inputs normales) -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Precio (€)</label>
                    <div class="d-flex gap-2">
                        <input type="number" step="0.01" min="0" name="precio_min" class="form-control"
                            placeholder="Mín" value="<?= htmlspecialchars($filtros['precio_min']) ?>">
                        <input type="number" step="0.01" min="0" name="precio_max" class="form-control"
                            placeholder="Máx" value="<?= htmlspecialchars($filtros['precio_max']) ?>">
                    </div>
                </div>
                <!-- Medidas -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Ancho</label>
                    <select name="ancho" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($anchos_arr as $an): ?>
                            <option value="<?= htmlspecialchars($an) ?>" <?= $filtros['ancho'] === $an ? 'selected' : '' ?>>
                                <?= htmlspecialchars($an) ?> (<?= $anchos_count[(string) $an] ?? 0 ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Alto</label>
                    <select name="alto" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($altos_arr as $al): ?>
                            <option value="<?= htmlspecialchars($al) ?>" <?= $filtros['alto'] === $al ? 'selected' : '' ?>>
                                <?= htmlspecialchars($al) ?> (<?= $altos_count[(string) $al] ?? 0 ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Largo</label>
                    <select name="largo" class="form-select">
                        <option value="">Todos</option>
                        <?php foreach ($largos_arr as $la): ?>
                            <option value="<?= htmlspecialchars($la) ?>" <?= $filtros['largo'] === $la ? 'selected' : '' ?>>
                                <?= htmlspecialchars($la) ?> (<?= $largos_count[(string) $la] ?? 0 ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Mantener búsqueda si existe -->
                <?php if (isset($_GET['busqueda'])): ?>
                    <input type="hidden" name="busqueda" value="<?= htmlspecialchars($_GET['busqueda']) ?>">
                <?php endif; ?>
                <button type="submit" class="btn btn-primary w-100 fw-semibold rounded-pill mt-2">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
            </form>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <main class="col-12 d-flex justify-content-center">
                <div class="w-100" style="max-width:1200px">
                    <!-- Aquí va el resto de tu contenido de productos -->
                    <div class="container mb-4 main-content">
                        <h1 class="text-center mt-4 mb-4">Productos</h1>

                        <!-- Buscador -->
                        <form method="GET" class="row justify-content-center mb-4">
                            <div class="col-12 col-md-10 col-lg-8">
                                <div
                                    class="search-bar-container p-2 bg-white rounded-4 shadow-sm d-flex align-items-center gap-2 flex-nowrap">
                                    <span class="ps-2 pe-1 text-secondary">
                                        <i class="bi bi-search fs-4"></i>
                                    </span>
                                    <input type="text"
                                        class="form-control border-0 bg-white shadow-none px-2 flex-grow-1"
                                        name="busqueda" id="input-busqueda" placeholder="Buscar productos por nombre..."
                                        value="<?php echo isset($_GET['busqueda']) ? ($_GET['busqueda']) : ''; ?>"
                                        style="font-size: 1.1rem; min-width: 200px;" autocomplete="off">
                                    <button type="button"
                                        class="btn rounded-pill px-4 ms-2 fw-semibold search-btn-yellow"
                                        data-bs-toggle="offcanvas" data-bs-target="#offcanvasFiltro"
                                        aria-controls="offcanvasFiltro">
                                        <i class="bi bi-funnel-fill me-2"></i>Filtrar
                                    </button>
                                    <button type="submit"
                                        class="btn rounded-pill px-4 ms-2 fw-semibold search-btn-yellow">
                                        Buscar
                                    </button>
                                    <a href="./"
                                        class="btn btn-outline-secondary rounded-pill px-4 ms-2 ver-todos-btn">Ver
                                        todos</a>
                                </div>
                            </div>
                        </form>
                        <style>
                            @media (min-width: 992px) {
                                aside {
                                    min-width: 260px;
                                    max-width: 320px;
                                }
                            }

                            @media (max-width: 991px) {
                                aside {
                                    margin-bottom: 1.5rem;
                                }
                            }

                            .search-bar-container {
                                transition: box-shadow 0.2s;
                                border: 1.5px solid #e5e7eb;
                                overflow-x: auto;
                                background: #fff;
                            }

                            /* Quita el borde amarillo al enfocar */
                            .search-bar-container:focus-within {
                                box-shadow: none;
                                border-color: #e5e7eb;
                            }

                            .search-bar-container input:focus {
                                background: #fffbe6;
                            }

                            .search-btn-yellow {
                                background: #bfa16a;
                                color: #fff !important;
                                border: none;
                                box-shadow: 0 2px 8px 0 #cbbfae80;
                                transition: background 0.2s, color 0.2s;
                                font-weight: 600;
                                letter-spacing: 0.5px;
                            }

                            .search-btn-yellow:hover,
                            .search-btn-yellow:focus {
                                background: #a88c54;
                                color: #fff !important;
                            }

                            .ver-todos-btn {
                                white-space: nowrap;
                                border: 1.5px solid #e5e7eb;
                                background: #fff;
                                color: #64748b;
                                transition: background 0.2s, color 0.2s, border 0.2s;
                                font-weight: 500;
                                letter-spacing: 0.2px;
                                padding-left: 1.2rem !important;
                                padding-right: 1.2rem !important;
                            }

                            .ver-todos-btn:hover {
                                background: #f5f5f5;
                                color: #2563eb;
                                border-color: #bcd0ee;
                                text-decoration: none;
                            }

                            .search-bar-container input[type="text"] {
                                min-width: 250px;
                                max-width: 100%;
                                flex-grow: 1;
                            }

                            @media (max-width: 600px) {
                                .search-bar-container {
                                    flex-direction: column;
                                    align-items: stretch;
                                    gap: 0.5rem;
                                }

                                .search-btn-yellow,
                                .ver-todos-btn {
                                    width: 100%;
                                    margin-left: 0 !important;
                                }
                            }

                            /* Hover en imágen de carta */
                            .card-img-container {
                                width: 100%;
                                height: 350px;
                                /* igual que .img-fija */
                                overflow: hidden;
                                border-top-left-radius: 1.5rem;
                                border-top-right-radius: 1.5rem;
                                background: #f8f8f8;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            }

                            .img-fija {
                                width: 100%;
                                height: 100%;
                                object-fit: fill;
                                transition: transform 0.35s cubic-bezier(.4, 2, .3, 1), filter 0.3s;
                            }

                            .card:hover .img-fija,
                            .card:focus-within .img-fija {
                                transform: scale(1.08);
                                filter: brightness(1.04) saturate(1.1);
                            }

                            .add-cart-btn {
                                right: 16px;
                                bottom: 16px;
                                width: 44px;
                                height: 44px;
                                background: #ffc25a;
                                color: #333;
                                border: none;
                                border-radius: 12px;
                                box-shadow: 0 2px 8px 0 #fff3cd80;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                font-size: 1.4rem;
                                transition: background 0.2s, color 0.2s, transform 0.15s;
                                z-index: 10;
                                padding: 0;
                            }

                            .add-cart-btn:hover,
                            .add-cart-btn:focus {
                                background: #ffb340;
                                color: #222;
                                transform: scale(1.08);
                                outline: none;
                            }

                            .card-img-container {
                                position: relative;
                            }

                            .btn-primary {
                                background-color: #bfa16a !important;
                                border-color: #bfa16a !important;
                                color: #fff !important;
                            }

                            .btn-primary:hover,
                            .btn-primary:focus {
                                background-color: #a88c54 !important;
                                border-color: #a88c54 !important;
                                color: #fff !important;
                            }
                        </style>

                        <?php
                        $where = [];
                        if (isset($_GET['busqueda']) && !empty(trim($_GET['busqueda']))) {
                            $busqueda = $_conexion->real_escape_string($_GET['busqueda']);
                            $where[] = "p.nombre LIKE '%$busqueda%'";
                        }
                        if (!empty($filtros['categoria'])) {
                            $categoria = $_conexion->real_escape_string($filtros['categoria']);
                            $where[] = "p.categoria = '$categoria'";
                        }

                        // Cambia aquí: usa el precio final (con oferta si la hay)
                        if (!empty($filtros['precio_min'])) {
                            $precio_min = floatval($filtros['precio_min']);
                            $where[] = "(p.precio * (1 - IFNULL(o.porcentaje, 0)/100)) >= $precio_min";
                        }
                        if (!empty($filtros['precio_max'])) {
                            $precio_max = floatval($filtros['precio_max']);
                            $where[] = "(p.precio * (1 - IFNULL(o.porcentaje, 0)/100)) <= $precio_max";
                        }
                        if (!empty($filtros['ancho'])) {
                            $ancho = $_conexion->real_escape_string($filtros['ancho']);
                            $where[] = "JSON_EXTRACT(p.medidas, '$.ancho') = '$ancho'";
                        }
                        if (!empty($filtros['alto'])) {
                            $alto = $_conexion->real_escape_string($filtros['alto']);
                            $where[] = "JSON_EXTRACT(p.medidas, '$.alto') = '$alto'";
                        }
                        if (!empty($filtros['largo'])) {
                            $largo = $_conexion->real_escape_string($filtros['largo']);
                            $where[] = "JSON_EXTRACT(p.medidas, '$.largo') = '$largo'";
                        }

                        $sql = "SELECT p.*, o.porcentaje FROM productos p 
                                LEFT JOIN ofertas o ON p.id_oferta = o.id_oferta";
                        if ($where) {
                            $sql .= " WHERE " . implode(' AND ', $where);
                        }

                        $resultado = $_conexion->query($sql);
                        ?>

                        <?php if ($resultado && $resultado->num_rows > 0): ?>
                            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                                <?php while ($fila = $resultado->fetch_assoc()): ?>
                                    <?php
                                    $precio = $fila["precio"];
                                    $porcentaje = $fila["porcentaje"];

                                    $hayOferta = false;
                                    if (!is_null($porcentaje)) {
                                        $hayOferta = true;
                                        $precioFinal = $precio * (1 - $porcentaje / 100);
                                    }
                                    ?>
                                    <div class="col">
                                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative">
                                            <?php if ($hayOferta): ?>
                                                <span style="z-index:80;"
                                                    class="position-absolute top-0 end-0 bg-danger text-white px-2 py-1 rounded-start">
                                                    -<?php echo $porcentaje; ?>%
                                                </span>
                                            <?php endif; ?>

                                            <a href="ver_producto?id_producto=<?php echo $fila["id_producto"]; ?>"
                                                class="text-decoration-none text-dark">
                                                <div class="card-img-container position-relative">
                                                    <img class="img-fija"
                                                        src="../../img/productos/<?php echo $fila["img_producto"]; ?>"
                                                        alt="Imagen del producto <?php echo htmlspecialchars($fila["nombre"]); ?>">
                                                    <form method="post" class="add-to-cart-form">
                                                        <input type="hidden" name="add_to_cart_id"
                                                            value="<?php echo $fila["id_producto"]; ?>">
                                                        <button type="submit" class="btn add-cart-btn position-absolute">
                                                            <i class="bi bi-cart-plus"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </a>

                                            <div class="card-body text-center">
                                                <h5 class="card-title fw-bold fs-5 mb-2"><?php echo $fila["nombre"]; ?></h5>
                                                <div class="card-text fs-5">
                                                    <?php
                                                    if ($hayOferta) {
                                                    ?>
                                                        <span class="text-muted text-decoration-line-through me-2">
                                                            <?php echo number_format($precio, 2, ',', '.'); ?> €
                                                        </span>
                                                        <span class="text-success fw-semibold">
                                                            <?php echo number_format($precioFinal, 2, ',', '.'); ?> €
                                                        </span>
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <span class="text-success fw-semibold">
                                                            <?php echo number_format($precio, 2, ',', '.'); ?> €
                                                        </span>
                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning text-center" role="alert">
                                No se encontraron productos.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php include('../footer.php'); ?>
    <?php include('../udify-bot.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Si la URL tiene ?focus=1, enfoca el input de búsqueda
        if (new URLSearchParams(window.location.search).get('focus') === '1') {
            const input = document.getElementById('input-busqueda');
            if (input) input.focus();
        }

        document.addEventListener('DOMContentLoaded', function() {
            var offcanvas = document.getElementById('offcanvasFiltro');
            var btnFiltro = document.getElementById('btn-filtro-lateral');
            if (offcanvas && btnFiltro) {
                offcanvas.addEventListener('show.bs.offcanvas', function() {
                    btnFiltro.style.display = 'none';
                });
                offcanvas.addEventListener('hidden.bs.offcanvas', function() {
                    btnFiltro.style.display = '';
                });
            }
        });
    </script>

    <?php if (isset($mensaje) && $mensaje == "success"): ?>
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Producto añadido al carrito',
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
        </script>
    <?php elseif (isset($mensaje) && $mensaje == "error"): ?>
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: 'Error al añadir el producto al carrito',
                text: '<?= addslashes($errorMsg); ?>',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        </script>
    <?php endif; ?>
</body>

</html>