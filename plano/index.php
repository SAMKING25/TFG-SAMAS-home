<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require('../util/conexion.php');

session_start();
if (!isset($_SESSION["usuario"])) {
    header("location: ../suscripcion/");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAMAS HOME - plano</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link id="favicon" rel="shortcut icon" href="/img/logos/loguito_gris.png" />
    <!-- Archivo CSS personalizado -->
    <link rel="stylesheet" href="/css/landing.css" />
    <!--search-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.2.4/fabric.min.js"></script>
    <style>
        body {
            overflow: hidden;
        }

        #sidebar {
            width: 400px;
            min-width: 400px;
            max-width: 400px;
            height: 100vh;
            overflow-y: auto;
            background: #f8f9fa;
            transition: width 0.3s, min-width 0.3s, padding 0.3s;
        }

        #sidebar.collapsed {
            width: 0 !important;
            min-width: 0 !important;
            max-width: 0 !important;
            padding: 0 !important;
            border: none !important;
            overflow: hidden;
        }

        .productos-sidebar {
            display: flex;
            flex-direction: row;
            width: 100vw;
            height: 100vh;
            position: relative;
        }

        #productos .list-group-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            font-size: 1.2rem;
        }

        #productos .list-group-item img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }

        #canvas-container {
            flex-grow: 1;
            width: 100%;
            transition: none;
            position: relative;
            /* Elimina margin-left si lo tienes */
        }

        #sidebar.collapsed+#canvas-container {
            margin-left: 0 !important;
        }

        canvas {
            border: 1px solid #ccc;
            width: 100%;
            height: 60vh;
        }

        /* Ajusta el botón de toggle */
        #toggle-sidebar-btn {
            /* Ya está en position: absolute y left: 400px por defecto */
            transition: left 0.3s;
            /* Puedes ajustar el tamaño si quieres */
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            margin-right: 56px;
        }

        #sidebar.collapsed+#canvas-container {
            margin-left: 0 !important;
        }

        #sidebar.collapsed~canvas {
            width: 100vw !important;
            height: 100vh !important;
        }

        #sidebar:not(collapse)~canvas {
            width: 80vw !important;
            height: 80vh !important;
        }

        #canvas-buttons {
            margin-left: 56px !important;
            transition: margin-left 0.3s;
            
        }

        #canvas-buttons-2 {
            margin-right: 36px !important;
            transition: margin-right 0.3s;
        }



        /* Anula el hover cuando el botón está en outline (seleccionado) */
        #add-wall-button.btn-outline-primary:hover,
        #add-wall-button.btn-outline-primary:focus,
        #add-door-button.btn-outline-warning:hover,
        #add-door-button.btn-outline-warning:focus,
        #mouse-mode-btn.btn-outline-dark:hover,
        #mouse-mode-btn.btn-outline-dark:focus,
        #move-mode-btn.btn-outline-dark:hover,
        #move-mode-btn.btn-outline-dark:focus {
            background-color: transparent !important;
            color: inherit !important;
            border-color: inherit !important;
            box-shadow: none !important;
            filter: none !important;
            outline: none !important;
            transition: none !important;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php
    include('../navbar.php');

    $id_usuario = $_SESSION["usuario"];
    $query = $_conexion->prepare('
        SELECT p.*, c.cantidad, o.porcentaje 
        FROM carrito c
        JOIN productos p ON c.id_producto = p.id_producto
        LEFT JOIN ofertas o ON p.id_oferta = o.id_oferta
        WHERE c.id_usuario = ?
    ');
    $query->bind_param('i', $id_usuario);
    $query->execute();
    $result = $query->get_result();
    $productos = $result->fetch_all(MYSQLI_ASSOC);

    $total = 0;
    foreach ($productos as $producto) {
        // Si hay oferta y porcentaje > 0, aplica el descuento
        if (isset($producto['porcentaje']) && $producto['porcentaje'] > 0) {
            $precio = $producto['precio'] * (1 - ($producto['porcentaje'] / 100));
        } else {
            $precio = $producto['precio'];
        }
        $total += $precio * $producto['cantidad'];
    }

    ?>
    <!-- Contenedor principal -->
    <div class="d-flex productos-sidebar main-content" style="height: 100vh; position: relative;">
        <!-- Sidebar de productos -->
        <div id="sidebar" class="p-3">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="mb-0">Productos</h5>
                <!-- Botón para ocultar/mostrar sidebar SIEMPRE visible -->
                <button id="toggle-sidebar-btn" class="btn btn-outline-secondary btn-sm"
                    style="position: absolute; top: 24px; left: 400px; z-index: 1050; transition: left 0.3s;"
                    title="Ocultar barra">
                    <i class="bi bi-chevron-left" id="toggle-sidebar-icon"></i>
                </button>
            </div>
            <div id="productos" class="list-group">
                <?php if (empty($productos)): ?>
                    <div class="text-center py-5">
                        <p class="mb-3 text-muted fs-5">Carrito vacío</p>
                        <a href="/productos/" class="btn btn-primary">Añadir productos</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($productos as $producto): ?>
                        <div class="list-group-item list-group-item-action d-flex align-items-center" style="cursor:pointer;"
                            onclick="agregarProductoSidebar(this, '../../img/plano/<?php echo $producto['categoria']; ?>.png',
                            <?php echo htmlspecialchars(json_encode($producto['medidas'])); ?>)">
                            <img src="../img/productos/<?php echo $producto['img_producto']; ?>"
                                alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="me-2">
                            <span>
                                <?php echo htmlspecialchars($producto['nombre']); ?><br>
                                <small class="text-muted cantidad-label">Cantidad: <span
                                        class="cantidad-num"><?php echo $producto['cantidad']; ?></span></small>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <!-- Total abajo -->
            <div class="mt-4 border-top pt-3 text-end">
                <strong>Total: <?php echo number_format($total, 2, ',', '.'); ?>€</strong>
            </div>
        </div>

        <!-- Contenedor del canvas -->
        <div id="canvas-container" class="p-3 flex-grow-1">
            <div class="d-flex justify-content-between mb-3">
                <div id="canvas-buttons" class="d-flex text-start me-2">
                    <!-- Botón de agregar pared -->
                    <button id="add-wall-button" class="btn btn-primary rounded-circle shadow" onclick="agregarPared()"
                        style="width: 60px; height: 60px;" title="Agregar pared">
                        <i class="bi bi-square" style="font-size: 24px;"></i>
                    </button>
                    <!-- Botón de agregar puerta -->
                    <button id="add-door-button" class="btn btn-warning rounded-circle shadow ms-2"
                        onclick="agregarPuerta()" style="width: 60px; height: 60px;" title="Agregar puerta">
                        <i class="bi bi-door-open" style="font-size: 24px;"></i>
                    </button>
                    <!-- Botón de modo ratón -->
                    <button id="mouse-mode-btn" class="btn btn-dark rounded-circle shadow ms-2"
                        style="width: 60px; height: 60px;" title="Modo ratón">
                        <i class="bi bi-cursor" id="mouse-mode-icon"></i>
                    </button>
                    <!-- Botón de modo mover -->
                    <button id="move-mode-btn" class="btn btn-dark rounded-circle shadow ms-2"
                        style="width: 60px; height: 60px;" title="Modo mover">
                        <i class="bi bi-arrows-move" id="move-mode-icon"></i>
                    </button>
                    <!-- Botón de reset de vista -->
                    <button id="reset-view-btn" class="btn btn-secondary rounded-circle shadow ms-2"
                        style="width: 60px; height: 60px;" title="Vista inicial">
                        <i class="bi bi-aspect-ratio" style="font-size: 24px;"></i>
                    </button>
                    <!-- Mostrar/Ocultar medidas -->
                    <button id="toggle-measures" class="btn btn-secondary rounded-circle shadow ms-2"
                        style="width: 60px; height: 60px;" title="Ocultar/Mostrar medidas">
                        <i id="toggle-measures-icon" class="bi bi-eye"></i>
                    </button>
                </div>

                <div id="canvas-buttons-2" class="d-flex text-end me-2">
                    <!-- <button id="save-design" class="btn btn-success rounded-circle shadow me-2"
                        onclick="guardarCanvas()" style="width: 60px; height: 60px;">
                        <i class="bi bi-save" style="font-size: 24px;"></i>
                    </button> -->

                    <button id="import-json-btn" class="btn btn-info rounded-circle shadow me-2"
                        style="width: 60px; height: 60px;" title="Importar JSON">
                        <i class="bi bi-upload" style="font-size: 24px;"></i>
                    </button>
                    <div class="btn-group me-2">
                        <button id="export-dropdown" type="button"
                            class="btn btn-success rounded-circle shadow dropdown-toggle" data-bs-toggle="dropdown"
                            aria-expanded="false" style="width: 60px; height: 60px;">
                            <i class="bi bi-save" style="font-size: 24px;"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" id="export-png">Exportar PNG</a></li>
                            <li><a class="dropdown-item" href="#" id="export-json">Exportar JSON</a></li>
                        </ul>
                    </div>
                    <input type="file" id="import-json-input" accept=".json" style="display:none;">

                    <button id="delete-button" class="btn btn-danger rounded-circle shadow" onclick="borrarObjeto()"
                        style="width: 60px; height: 60px;">
                        <i class="bi bi-trash" style="font-size: 24px;"></i>
                    </button>
                </div>
            </div>

            <canvas id="canvas"></canvas>
        </div>
    </div>

    <?php include('../udify-bot.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/hammerjs@2.0.8/hammer.min.js"></script>
    <script src="JS/funcionalidades.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggle-sidebar-btn');
        const icon = document.getElementById('toggle-sidebar-icon');
        const canvasButtons = document.getElementById('canvas-buttons');

        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            if (sidebar.classList.contains('collapsed')) {
                toggleBtn.style.left = '10px';
                icon.classList.remove('bi-chevron-left');
                icon.classList.add('bi-chevron-right');
                canvasButtons.style.marginLeft = '56px'; // Ajusta según el ancho del botón
            } else {
                toggleBtn.style.left = '400px';
                icon.classList.remove('bi-chevron-right');
                icon.classList.add('bi-chevron-left');
                canvasButtons.style.marginLeft = '0';
            }
        });
    </script>
</body>

</html>