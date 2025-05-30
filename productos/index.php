<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require('../util/conexion.php');
session_start();

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
        .img-fija {
            width: 450px !important;
            height: 350px !important;
            object-fit: fill !important;
            display: block;
        }

        #btn-filtro-lateral {
            position: fixed;
            top: 140px;
            /* Más abajo para evitar el navbar */
            left: 0;
            z-index: 2000;
            width: 52px;
            height: 52px;
            padding: 0;
            margin-left: 8px;
            box-shadow: 0 2px 8px 0 #0001;
            transition: background 0.2s, color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #btn-filtro-lateral:hover,
        #btn-filtro-lateral:focus {
            background: #e9ecef;
            color: #2563eb;
        }

        @media (max-width: 600px) {
            #btn-filtro-lateral {
                top: 90px;
                left: 0;
                width: 44px;
                height: 44px;
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
            background: #ffc25a;
            /* Color igual al hover del navbar */
            color: #333 !important;
            border: none;
            box-shadow: 0 2px 8px 0 #fff3cd80;
            transition: background 0.2s, color 0.2s;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .search-btn-yellow:hover,
        .search-btn-yellow:focus {
            background: #ffb340;
            /* Un poco más oscuro para el hover */
            color: #222 !important;
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
                                        class="btn rounded-pill px-4 ms-2 fw-semibold text-dark search-btn-yellow"
                                        data-bs-toggle="offcanvas" data-bs-target="#offcanvasFiltro"
                                        aria-controls="offcanvasFiltro">
                                        <i class="bi bi-funnel-fill me-2"></i>Filtrar
                                    </button>
                                    <button type="submit"
                                        class="btn rounded-pill px-4 ms-2 fw-semibold text-dark search-btn-yellow">
                                        Buscar
                                    </button>
                                    <a href="./index.php"
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
                                background: #ffc25a;
                                /* Color igual al hover del navbar */
                                color: #333 !important;
                                border: none;
                                box-shadow: 0 2px 8px 0 #fff3cd80;
                                transition: background 0.2s, color 0.2s;
                                font-weight: 600;
                                letter-spacing: 0.5px;
                            }

                            .search-btn-yellow:hover,
                            .search-btn-yellow:focus {
                                background: #ffb340;
                                /* Un poco más oscuro para el hover */
                                color: #222 !important;
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
                                        <a href="ver_producto?id_producto=<?php echo $fila["id_producto"]; ?>"
                                            class="text-decoration-none text-dark">
                                            <div
                                                class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative">

                                                <?php if ($hayOferta): ?>
                                                    <span style="z-index:80;"
                                                        class="position-absolute top-0 end-0 bg-danger text-white px-2 py-1 rounded-start">
                                                        -<?php echo $porcentaje; ?>%
                                                    </span>
                                                <?php endif; ?>

                                                <div class="card-img-container">
                                                    <img class="img-fija"
                                                        src="../../img/productos/<?php echo $fila["img_producto"]; ?>"
                                                        alt="Imagen del producto <?php echo htmlspecialchars($fila["nombre"]); ?>">
                                                </div>

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
                                        </a>
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

    <script>
        // Si la URL tiene ?focus=1, enfoca el input de búsqueda
        if (new URLSearchParams(window.location.search).get('focus') === '1') {
            const input = document.getElementById('input-busqueda');
            if (input) input.focus();
        }

        document.addEventListener('DOMContentLoaded', function () {
            var offcanvas = document.getElementById('offcanvasFiltro');
            var btnFiltro = document.getElementById('btn-filtro-lateral');
            if (offcanvas && btnFiltro) {
                offcanvas.addEventListener('show.bs.offcanvas', function () {
                    btnFiltro.style.display = 'none';
                });
                offcanvas.addEventListener('hidden.bs.offcanvas', function () {
                    btnFiltro.style.display = '';
                });
            }
        });
    </script>
</body>

</html>