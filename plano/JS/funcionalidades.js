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
    fabric.Image.fromURL(imagenURL, function (img) {
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
        if (confirm('¿Estás seguro de que quieres eliminar el(los) objeto(s) seleccionado(s)?')) {
            if (activeObject.type === 'activeSelection') {
                activeObject.forEachObject(function (obj) {
                    canvas.remove(obj);
                });
                canvas.discardActiveObject();
            } else {
                canvas.remove(activeObject);
            }
            canvas.requestRenderAll();
        }
    } else {
        alert('No hay ningún objeto seleccionado.');
    }
}

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

function agregarPuerta() {
    const factorConversion = 100; // 100px = 1 metro

    const puerta = new fabric.Rect({
        left: 0,
        top: 0,
        fill: '#8b5a2b', // color marrón para distinguir
        width: 90,
        height: 22,
        selectable: false,
        originX: 'center',
        originY: 'center',
    });

    const textoMedida = new fabric.Text('0.90 m', {
        fontSize: 20,
        fill: '#000',
        backgroundColor: 'white',
        padding: 4,
        originX: 'center',
        originY: 'center',
        selectable: false,
        evented: false,
    });

    const grupo = new fabric.Group([puerta, textoMedida], {
        left: 150,
        top: 150,
        selectable: true,
        lockScalingY: true,
        hasControls: true,
    });

    canvas.add(grupo);
    canvas.setActiveObject(grupo);

    function actualizarMedida() {
        const anchoReal = puerta.width * grupo.scaleX;
        const metrosActualizados = (anchoReal / factorConversion).toFixed(2) + ' m';
        textoMedida.text = metrosActualizados;

        textoMedida.scaleX = 1 / grupo.scaleX;
        textoMedida.scaleY = 1 / grupo.scaleY;

        textoMedida.top = puerta.top - puerta.height / 2 - 20;
        textoMedida.left = puerta.left;

        canvas.requestRenderAll();
    }

    grupo.on('scaling', actualizarMedida);
    grupo.on('modified', actualizarMedida);
    grupo.on('rotating', actualizarMedida);

    actualizarMedida();
    canvas.renderAll();
}

document.addEventListener('keydown', function (event) {
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

let aligningLine;

function drawAligningLine(coords, orientation = 'vertical') {
    if (aligningLine) {
        canvas.remove(aligningLine);
    }

    aligningLine = new fabric.Line(coords, {
        stroke: 'red',
        strokeWidth: 1,
        selectable: false,
        evented: false,
        excludeFromExport: true,
    });

    canvas.add(aligningLine);
    canvas.renderAll();
}

let guiaX = null;
let guiaY = null;

canvas.on('object:moving', function (e) {
    const movingObj = e.target;
    const center = movingObj.getCenterPoint();
    const snapThreshold = 10;

    // Eliminar guías anteriores si existen
    if (guiaX) {
        canvas.remove(guiaX);
        guiaX = null;
    }
    if (guiaY) {
        canvas.remove(guiaY);
        guiaY = null;
    }

    let snappedX = false;
    let snappedY = false;

    canvas.getObjects().forEach(obj => {
        if (obj === movingObj) return;

        const otherCenter = obj.getCenterPoint();

        // Alinear centro horizontal
        if (!snappedX && Math.abs(center.x - otherCenter.x) < snapThreshold) {
            movingObj.left += otherCenter.x - center.x;
            guiaX = new fabric.Line([otherCenter.x, 0, otherCenter.x, canvas.height], {
                stroke: 'red',
                strokeWidth: 1,
                selectable: false,
                evented: false,
                excludeFromExport: true
            });
            canvas.add(guiaX);
            snappedX = true;
        }

        // Alinear centro vertical
        if (!snappedY && Math.abs(center.y - otherCenter.y) < snapThreshold) {
            movingObj.top += otherCenter.y - center.y;
            guiaY = new fabric.Line([0, otherCenter.y, canvas.width, otherCenter.y], {
                stroke: 'red',
                strokeWidth: 1,
                selectable: false,
                evented: false,
                excludeFromExport: true
            });
            canvas.add(guiaY);
            snappedY = true;
        }
    });

    canvas.renderAll();
});


canvas.on('object:modified', function () {
    if (guiaX) {
        canvas.remove(guiaX);
        guiaX = null;
    }
    if (guiaY) {
        canvas.remove(guiaY);
        guiaY = null;
    }
    canvas.renderAll();
});

canvas.on('object:modified', function (e) {
    const obj = e.target;

    if (obj.angle != null) {
        const snapAngles = [0, 90, 180, 270, 360];
        const tolerance = 10; // grados
        const currentAngle = obj.angle % 360;

        for (let angle of snapAngles) {
            if (Math.abs(currentAngle - angle) < tolerance) {
                obj.angle = angle;
                canvas.requestRenderAll();
                break;
            }
        }
    }

    // Limpieza de líneas guía, si usas
    if (guiaX) {
        canvas.remove(guiaX);
        guiaX = null;
    }
    if (guiaY) {
        canvas.remove(guiaY);
        guiaY = null;
    }
});






