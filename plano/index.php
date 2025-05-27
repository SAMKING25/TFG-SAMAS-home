<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAMAS HOME - plano</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="shortcut icon" href="/img/logos/logo-marron-nobg.ico" />
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
            height: 100vh;
            overflow-y: auto;
            background: #f8f9fa;
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
            position: relative;
        }

        canvas {
            border: 1px solid #ccc;
            width: 100%;
            height: 60vh;
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
    <?php
    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    require('../util/conexion.php');

    session_start();
    if (!isset($_SESSION["usuario"])) {
        header("location: ../login/usuario/iniciar_sesion_usuario.php");
        exit;
    }
    ?>
</head>

<body>
    <?php
    include('../navbar.php');

    $query = $_conexion->query('SELECT * FROM productos');
    $productos = $query->fetch_all(MYSQLI_ASSOC);

    ?>
    <!-- Contenedor principal -->
    <div class="d-flex productos-sidebar">
        <!-- Sidebar de productos -->
        <div id="sidebar" class="p-3">
            <h5>Productos</h5>
            <div id="productos" class="list-group">
                <?php foreach ($productos as $producto): ?>
                    <div class="list-group-item list-group-item-action d-flex align-items-center" style="cursor:pointer;"
                        onclick="agregarProducto('../../img/plano/<?php echo $producto['categoria']; ?>.png',
                        <?php echo htmlspecialchars(json_encode($producto['medidas'])); ?>)">
                        <img src="../img/productos/<?php echo $producto['img_producto']; ?>"
                            alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="me-2">
                        <span>
                            <?php echo htmlspecialchars($producto['nombre']); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Contenedor del canvas -->
        <div id="canvas-container" class="p-3">
            <div class="d-flex justify-content-between mb-3">
                <div class="d-flex text-start me-2">
                    <!-- Botón de agregar pared -->
                    <button id="add-wall-button" class="btn btn-primary rounded-circle shadow" onclick="agregarPared()"
                        style="width: 60px; height: 60px;" title="Agregar pared">
                        <i class="bi bi-square" style="font-size: 24px;"></i>
                    </button>
                    <!-- Botón de agregar puerta -->
                    <button id="add-door-button" class="btn btn-warning rounded-circle shadow ms-2" onclick="agregarPuerta()"
                        style="width: 60px; height: 60px;" title="Agregar puerta">
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

                <div class="d-flex text-end me-2">
                    <!-- <button id="save-design" class="btn btn-success rounded-circle shadow me-2"
                        onclick="guardarCanvas()" style="width: 60px; height: 60px;">
                        <i class="bi bi-save" style="font-size: 24px;"></i>
                    </button> -->

                    <button id="import-json-btn" class="btn btn-info rounded-circle shadow me-2" style="width: 60px; height: 60px;" title="Importar JSON">
                        <i class="bi bi-upload" style="font-size: 24px;"></i>
                    </button>
                    <div class="btn-group me-2">
                        <button id="export-dropdown" type="button" class="btn btn-success rounded-circle shadow dropdown-toggle"
                            data-bs-toggle="dropdown" aria-expanded="false" style="width: 60px; height: 60px;">
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
    <?php include('../footer.php'); ?>
    <?php include('../udify-bot.php'); ?>

    <script src="JS/funcionalidades.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>