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
            background: #f5f3ef;
            font-family: 'Segoe UI', 'Arial', sans-serif;
            color: #3e2c18;
        }

        .productos-sidebar {
            display: flex;
            flex-direction: row;
            width: 100vw;
            height: 100vh;
            position: relative;
            background: #f5f3ef;
        }

        #sidebar {
            width: 400px;
            min-width: 400px;
            max-width: 400px;
            height: 100vh;
            overflow-y: auto;
            background: #fff8f1;
            border-right: 2px solid #e0d6c3;
            box-shadow: 2px 0 12px rgba(169, 124, 80, 0.07);
            transition: width 0.3s, min-width 0.3s, padding 0.3s;
        }

        #sidebar.collapsed {
            width: 0 !important;
            min-width: 0 !important;
            max-width: 0 !important;
            padding: 0 !important;
            border: none !important;
            overflow: hidden;
            box-shadow: none;
        }

        #toggle-sidebar-btn {
            background: #fff;
            border: 1.5px solid #a97c50;
            color: #a97c50;
            border-radius: 50%;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(169, 124, 80, 0.08);
            transition: left 0.3s, background 0.2s, color 0.2s;
        }

        #toggle-sidebar-btn:hover {
            background: #a97c50;
            color: #fff;
        }

        #productos .list-group-item {
            background: #fff;
            border: 1px solid #e0d6c3;
            border-radius: 10px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px 16px;
            font-size: 1.15rem;
            box-shadow: 0 2px 8px rgba(169, 124, 80, 0.04);
            transition: box-shadow 0.2s, border 0.2s;
        }

        #productos .list-group-item:hover {
            border-color: #a97c50;
            box-shadow: 0 4px 16px rgba(169, 124, 80, 0.10);
            background: #f9f6f2;
        }

        #productos .list-group-item img {
            width: 72px;
            height: 72px;
            object-fit: contain;
            border-radius: 8px;
            border: 1px solid #e0d6c3;
            background: #f5f3ef;
        }

        .cantidad-label {
            color: #a97c50 !important;
            font-weight: 500;
        }

        #detalle-producto-float {
            min-width: 320px;
            max-width: 350px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 8px 32px rgba(169, 124, 80, 0.18);
            border: 2px solid #e0d6c3;
            overflow: hidden;
            font-size: 1rem;
            pointer-events: none;
            transition: opacity 0.15s;
        }

        #detalle-producto-float .detalle-header {
            background: linear-gradient(90deg, #a97c50 60%, #e0d6c3 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 18px 0 10px 0;
        }

        #detalle-producto-float .detalle-header img {
            max-width: 90px;
            max-height: 90px;
            object-fit: contain;
            border-radius: 10px;
            border: 1.5px solid #e0d6c3;
            background: #fff8f1;
        }

        #detalle-producto-float .detalle-body {
            padding: 18px 20px 14px 20px;
        }

        #detalle-producto-float .detalle-titulo {
            font-weight: bold;
            font-size: 1.15rem;
            margin-bottom: 4px;
            color: #a97c50;
        }

        #detalle-producto-float .detalle-categoria {
            color: #bfa16b;
            font-size: 0.97em;
            margin-bottom: 8px;
        }

        #detalle-producto-float .detalle-precio {
            color: #198754;
            font-weight: bold;
            font-size: 1.1em;
            margin-bottom: 6px;
        }

        #detalle-producto-float .detalle-medidas {
            color: #3e2c18;
            font-size: 0.97em;
            margin-bottom: 6px;
        }

        #detalle-producto-float .detalle-desc {
            color: #444;
            font-size: 0.97em;
            margin-bottom: 0;
        }

        #canvas-container {
            flex-grow: 1;
            width: 100%;
            background: #f5f3ef;
            transition: none;
            position: relative;
        }

        #canvas-buttons {
            margin-left: 56px !important;
            transition: margin-left 0.3s;
        }

        #canvas-buttons-2 {
            margin-right: 36px !important;
            transition: margin-right 0.3s;
        }

        /* Botones principales del plano */
        #canvas-buttons .btn,
        #canvas-buttons-2 .btn {
            border: none;
            background: #fff;
            color: #a97c50;
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(169, 124, 80, 0.10);
            font-size: 1.35rem;
            margin-right: 10px;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
        }

        #canvas-buttons .btn-primary,
        #canvas-buttons .btn-warning {
            background: linear-gradient(135deg, #a97c50 80%, #bfa16b 100%);
            color: #fff;
            border: none;
        }

        #canvas-buttons .btn-primary:hover,
        #canvas-buttons .btn-warning:hover {
            background: #bfa16b;
            color: #fff;
            box-shadow: 0 4px 16px rgba(169, 124, 80, 0.18);
        }

        #canvas-buttons .btn-dark,
        #canvas-buttons .btn-secondary {
            background: #f5f3ef;
            color: #a97c50;
            border: 1.5px solid #e0d6c3;
        }

        #canvas-buttons .btn-dark:hover,
        #canvas-buttons .btn-secondary:hover {
            background: #a97c50;
            color: #fff;
            border-color: #a97c50;
        }

        #canvas-buttons .btn-success {
            background: #198754;
            color: #fff;
        }

        #canvas-buttons .btn-success:hover {
            background: #157347;
        }

        #canvas-buttons .btn-danger {
            background: #fff;
            color: #d9534f;
            border: 1.5px solid #e0d6c3;
        }

        #canvas-buttons .btn-danger:hover {
            background: #d9534f;
            color: #fff;
            border-color: #d9534f;
        }

        /* Tooltip canvas */
        #canvas-product-tooltip {
            display: none;
            position: fixed;
            z-index: 10000;
            background: #3e2c18;
            color: #fff;
            padding: 7px 16px;
            border-radius: 8px;
            font-size: 1rem;
            pointer-events: none;
            box-shadow: 0 2px 8px rgba(169, 124, 80, 0.18);
            border: 1.5px solid #a97c50;
        }

        /* Total abajo */
        .mt-4.border-top.pt-3.text-end {
            background: #fff8f1;
            border-top: 2px solid #e0d6c3 !important;
            padding-top: 18px !important;
            font-size: 1.25rem;
            color: #198754;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background-color: #a37030 !important;
            border: none !important;
        }

        .btn-primary:hover {
            background-color: rgb(133, 90, 38) !important;
        }
    </style>
</head>

<body>
    <script>
        // Detecta móviles (ancho menor a 768px o user agent típico de móvil)
        function esMovil() {
            const uaMobile = navigator.userAgent.toLowerCase();
            const esMobileUA = /android|iphone|ipod|blackberry|iemobile|opera mini/.test(uaMobile);
            const esPantallaPequenaMobile = window.innerWidth < 768;
            // Puedes ajustar el ancho según tus necesidades
            return esMobileUA && esPantallaPequenaMobile;
        }

        if (esMovil()) {
            window.location.href = "aviso-movil";
        }

        // Banear botones en Tablet
        function esTablet() {
            const uaTablet = navigator.userAgent.toLowerCase();
            const esTabletUA = /android|iphone|ipod|blackberry|iemobile|opera mini/.test(uaTablet);
            return esTabletUA;
        }

        if (esTablet()) {
            document.addEventListener('DOMContentLoaded', function () {
                document.getElementById('mouse-mode-btn')?.classList.add('d-none');
                document.getElementById('move-mode-btn')?.classList.add('d-none');
                document.getElementById('reset-view-btn')?.classList.add('d-none');
                document.getElementById('detalle-producto-float')?.classList.add('d-none');
            });
        }        
    </script>
    <?php
    include('../navbar.php');

    $id_usuario = $_SESSION["usuario"];
    $query = $_conexion->prepare('
        SELECT p.*, c.cantidad, o.porcentaje, pr.nombre_proveedor
        FROM carrito c
        JOIN productos p ON c.id_producto = p.id_producto
        LEFT JOIN ofertas o ON p.id_oferta = o.id_oferta
        LEFT JOIN proveedores pr ON p.id_proveedor = pr.id_proveedor
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
                    style="position: absolute; top: 24px; left: 400px; z-index: 50; transition: left 0.3s;"
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
                            data-id="<?php echo $producto['id_producto']; ?>" onclick="agregarProducto('../../img/plano/<?php echo $producto['categoria']; ?>.png',
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
            <!-- Tarjeta flotante de detalle de producto -->
            <div id="detalle-producto-float" style="display:none; position:fixed; z-index:9999;"></div>
            <!-- Nombre en el hover del producto del canvas-->
            <div id="canvas-product-tooltip"
                style="display:none; position:fixed; z-index:10000; background:#222; color:#fff; padding:6px 14px; border-radius:8px; font-size:1rem; pointer-events:none; box-shadow:0 2px 8px rgba(0,0,0,0.18);">
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
                        <i class="bi bi-bricks"></i>
                    </button>
                    <!-- Botón de agregar puerta -->
                    <button id="add-door-button" class="btn btn-warning rounded-circle shadow ms-2"
                        onclick="agregarPuerta()" style="width: 60px; height: 60px;" title="Agregar puerta">
                        <i class="bi bi-door-open"></i>
                    </button>
                    <button id="add-door-button" class="btn btn-warning rounded-circle shadow ms-2 botones-plano-iconos"
                        onclick="agregarVentana()" style="width: 60px; height: 60px;" title="Agregar ventana">
                        <i class="bi bi-layout-split"></i>
                    </button>
                    <!-- Botón de modo ratón -->
                    <button id="mouse-mode-btn" class="btn btn-dark rounded-circle shadow ms-2 botones-plano-iconos"
                        style="width: 60px; height: 60px;" title="Modo ratón">
                        <i class="bi bi-cursor" id="mouse-mode-icon"></i>
                    </button>
                    <!-- Botón de modo mover -->
                    <button id="move-mode-btn" class="btn btn-dark rounded-circle shadow ms-2 botones-plano-iconos"
                        style="width: 60px; height: 60px;" title="Modo mover">
                        <i class="bi bi-arrows-move" id="move-mode-icon"></i>
                    </button>
                    <!-- Botón de reset de vista -->
                    <button id="reset-view-btn"
                        class="btn btn-secondary rounded-circle shadow ms-2 botones-plano-iconos"
                        style="width: 60px; height: 60px;" title="Vista inicial">
                        <i class="bi bi-aspect-ratio"></i>
                    </button>
                    <!-- Mostrar/Ocultar medidas -->
                    <button id="toggle-measures"
                        class="btn btn-secondary rounded-circle shadow ms-2 botones-plano-iconos"
                        style="width: 60px; height: 60px;" title="Ocultar/Mostrar medidas">
                        <i id="toggle-measures-icon" class="bi bi-eye"></i>
                    </button>
                </div>

                <div id="canvas-buttons-2" class="d-flex text-end me-2">
                    <div class="btn-group me-2">
                        <button id="export-png" type="button"
                            class="btn btn-success rounded-circle shadow botones-plano-iconos"
                            style="width: 60px; height: 60px;">
                            <i class="bi bi-card-image"></i>
                        </button>

                    </div>
                    <input type="file" id="import-json-input" accept=".json" style="display:none;">

                    <button id="delete-button" class="btn btn-danger rounded-circle shadow botones-plano-iconos"
                        onclick="borrarObjeto()" style="width: 60px; height: 60px;">
                        <i class="bi bi-trash"></i>
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
        // Muestra la tarjeta de detalle del producto al pasar el mouse
        document.querySelectorAll('#productos .list-group-item').forEach(function (item) {
            item.addEventListener('mouseenter', function (e) {
                const id = this.getAttribute('data-id');
                const producto = <?php echo json_encode($productos); ?>.find(p =>
                    String(p.id_producto) === String(id)
                );
                if (!producto) return;

                let precio = Number(producto.precio);
                if (producto.porcentaje && Number(producto.porcentaje) > 0) {
                    precio = (Number(producto.precio) * (1 - Number(producto.porcentaje) / 100)).toFixed(2);
                } else {
                    precio = Number(producto.precio).toFixed(2);
                }

                let medidas = '';
                try {
                    const m = JSON.parse(producto.medidas);
                    medidas = `${m.ancho} × ${m.largo} cm`;
                } catch {
                    medidas = '-';
                }

                const detalle = document.getElementById('detalle-producto-float');
                detalle.innerHTML = `
                    <div class="detalle-header">
                        <img src="../img/productos/${producto.img_producto}" alt="${producto.nombre}">
                    </div>
                    <div class="detalle-body">
                        <div class="detalle-titulo">${producto.nombre}</div>
                        <div class="detalle-categoria"><i class="bi bi-tag"></i> ${producto.categoria}</div>
                        <div class="detalle-precio">Precio: <span style="color:#198754;font-weight:bold;">${precio}€</span></div>
                        <div class="detalle-medidas">Medidas: ${medidas}</div>
                        <div class="detalle-desc">${producto.descripcion ?? ''}</div>
                        <div class="detalle-empresa"><strong>${producto.nombre_proveedor ?? '-'}</strong></div>
                    </div>
                `;
                detalle.style.display = 'block';
                detalle.style.opacity = '1';

                function moveDetalle(ev) {
                    const x = ev.clientX + 18;
                    const y = ev.clientY - 10;
                    detalle.style.left = x + 'px';
                    detalle.style.top = y + 'px';
                }
                moveDetalle(e);
                item._moveDetalleHandler = moveDetalle;
                document.addEventListener('mousemove', moveDetalle);
            });

            item.addEventListener('mouseleave', function () {
                const detalle = document.getElementById('detalle-producto-float');
                detalle.style.display = 'none';
                detalle.innerHTML = '';
                if (item._moveDetalleHandler) {
                    document.removeEventListener('mousemove', item._moveDetalleHandler);
                    item._moveDetalleHandler = null;
                }
            });
        });
    </script>
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