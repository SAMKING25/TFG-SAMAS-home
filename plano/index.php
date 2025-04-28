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
    </style>
    <?php
    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    require('../util/conexion.php');

    session_start();

    ?>
</head>

<body>
    <?php
    include('../navbar.php');

    $query = $_conexion->query('SELECT id_producto, nombre, imagen FROM productos');
    $productos = $query->fetch_all(MYSQLI_ASSOC);

    ?>
    <!-- Contenedor principal -->
    <div class="d-flex">
        <!-- Sidebar de productos -->
        <div id="sidebar" class="p-3">
            <h5>Productos</h5>
            <div id="productos" class="list-group">
                <?php foreach ($productos as $producto): ?>
                    <div class="list-group-item list-group-item-action d-flex align-items-center" style="cursor:pointer;" onclick="agregarProducto('../img/productos/<?php echo $producto['imagen']; ?>')">
                        <img src="../img/productos/<?php echo $producto['imagen']; ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" class="me-2">
                        <span><?php echo htmlspecialchars($producto['nombre']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Contenedor del canvas -->
        <div id="canvas-container" class="p-3">
            <div class="d-flex justify-content-between mb-3">
                <button id="add-wall-button" class="btn btn-primary rounded-circle shadow" onclick="agregarPared()" style="width: 60px; height: 60px;">
                    <i class="bi bi-square" style="font-size: 24px;"></i>
                </button>

                <div class="d-flex text-end me-2">
                    <button id="save-design" class="btn btn-success rounded-circle shadow me-2" onclick="guardarCanvas()" style="width: 60px; height: 60px;">
                        <i class="bi bi-save" style="font-size: 24px;"></i>
                    </button>
                    <button id="delete-button" class="btn btn-danger rounded-circle shadow" onclick="borrarObjeto()" style="width: 60px; height: 60px;">
                        <i class="bi bi-trash" style="font-size: 24px;"></i>
                    </button>
                </div>
            </div>
            <canvas id="canvas"></canvas>
        </div>
    </div>

    <script>
        const canvas = new fabric.Canvas('canvas', {
            backgroundColor: '#fcfcfc'
        });

        // Hacer que el canvas ocupe todo el contenedor
        canvas.setWidth(window.innerWidth - 400); // 400px del sidebar
        canvas.setHeight(window.innerHeight);

        // Ajustar cuando cambie el tamaño de la ventana
        window.addEventListener('resize', () => {
            canvas.setWidth(window.innerWidth - 400);
            canvas.setHeight(window.innerHeight);
        });

        function agregarProducto(imagenURL) {
            console.log(imagenURL);
            fabric.Image.fromURL(imagenURL, function(img) {
                const escala = 0.5;

                img.set({
                    left: 50,
                    top: 50,
                    scaleX: escala,
                    scaleY: escala,
                    hasControls: true,
                    lockScalingX: true,
                    lockScalingY: true,
                    lockSkewingX: true,
                    lockSkewingY: true,
                    lockScalingFlip: true,
                    lockRotation: false,
                });
                canvas.add(img);
                canvas.setActiveObject(img);
                canvas.renderAll();
            });
        }

        function borrarObjeto() {
            const activeObject = canvas.getActiveObject();
            if (activeObject) {
                if (confirm('¿Estás seguro de que quieres eliminar este objeto?')) {
                    canvas.remove(activeObject);
                }
            } else {
                alert('No hay ningún objeto seleccionado.');
            }
        };

        function agregarPared() {
            const factorConversion = 100; // 100px = 1 metro

            const pared = new fabric.Rect({
                left: 0,
                top: 0,
                fill: '#403f3f',
                width: 200,
                height: 22,
                selectable: false,
                originX: 'center',
                originY: 'center',
            });

            const textoMedida = new fabric.Text('2.00 m', {
                fontSize: 26,
                fill: '#000',
                backgroundColor: 'white',
                padding: 4,
                originX: 'center',
                originY: 'center',
                selectable: false,
                evented: false,
            });

            const grupo = new fabric.Group([pared, textoMedida], {
                left: 100,
                top: 100,
                selectable: true,
                lockScalingY: true,
                hasControls: true,
            });

            canvas.add(grupo);
            canvas.setActiveObject(grupo);

            // Función para actualizar la medida de la pared
            function actualizarMedida() {
                const anchoReal = pared.width * grupo.scaleX;
                const metrosActualizados = (anchoReal / factorConversion).toFixed(2) + ' m';
                textoMedida.text = metrosActualizados;

                // Mantener el texto sin escalar
                textoMedida.scaleX = 1 / grupo.scaleX;
                textoMedida.scaleY = 1 / grupo.scaleY;

                // Posicionar el texto encima de la pared
                textoMedida.top = pared.top - pared.height / 2 - 20; // 20px encima
                textoMedida.left = pared.left;

                canvas.requestRenderAll();
            }

            // Escuchar eventos para actualizar la medida
            grupo.on('scaling', actualizarMedida);
            grupo.on('modified', actualizarMedida);
            grupo.on('rotating', actualizarMedida);

            // Actualizar medida al principio
            actualizarMedida();
            canvas.renderAll();
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Delete' || event.key === 'Backspace') {
                borrarObjeto();
            }
        });

        function guardarCanvas() {
            const dataURL = canvas.toDataURL({
                format: 'png'
            });
            const link = document.createElement('a');
            link.href = dataURL;
            link.download = 'diseño.png';
            link.click();
        }

        window.addEventListener('wheel', (event) => {
            const zoomFactor = event.deltaY > 0 ? 0.9 : 1.1;
            canvas.setZoom(canvas.getZoom() * zoomFactor);
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>