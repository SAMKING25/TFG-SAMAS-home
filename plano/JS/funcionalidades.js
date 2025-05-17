const canvas = new fabric.Canvas('canvas', {
    backgroundColor: '#fcfcfc'
});

crearEscalaGrafica();

// Hacer que el canvas ocupe todo el contenedor
canvas.setWidth(window.innerWidth - 400); // 400px del sidebar
canvas.setHeight(window.innerHeight - 230);

// Ajustar cuando cambie el tamaño de la ventana
window.addEventListener('resize', () => {
    canvas.setWidth(window.innerWidth - 400);
    canvas.setHeight(window.innerHeight - 230);
});

function agregarProducto(imagenURL, medidas) {
    medidas = JSON.parse(medidas);
    
    const anchoPx = medidas.ancho;
    const altoPx = medidas.largo;

    fabric.Image.fromURL(imagenURL, function (img) {
        // Escala para que la imagen tenga el tamaño en px correspondiente a las medidas
        const scaleX = anchoPx / img.width;
        const scaleY = altoPx / img.height;

        img.set({
            left: 50,
            top: 50,
            scaleX: scaleX,
            scaleY: scaleY,
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
            if (activeObject.type === 'activeSelection') {
                activeObject.forEachObject(function (obj) {
                    canvas.remove(obj);
                });
                canvas.discardActiveObject();
            } else {
                canvas.remove(activeObject);
            }
            canvas.requestRenderAll();
    } else {
        alert('No hay ningún objeto seleccionado.');
    }
}

function agregarPared() {
    const factorConversion = 100; // 100px = 1 metro

    const pared = new fabric.Rect({
        left: 0,
        top: 0,
        fill: '#fhfhfh',
        width: 200,
        height: 15,
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
        evented: true,
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
        fill: '#9c9c9c', // color marrón para distinguir
        width: 100,
        height: 15,
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

//Scroll del mouse para hacer zoom
/* window.addEventListener('wheel', (event) => {
    const zoomFactor = event.deltaY > 0 ? 0.9 : 1.1;
    canvas.setZoom(canvas.getZoom() * zoomFactor);
}); */

//Scroll de mouse para hacer zoom, pero solo si el mouse está sobre el canvas
window.addEventListener('wheel', (event) => {
    const productosSidebar = document.querySelector('.productos-sidebar'); // Ajusta esta clase si tiene otro nombre
    const esEnSidebar = productosSidebar.contains(event.target);

    if (!esEnSidebar) {
        event.preventDefault(); // Previene el scroll general solo si es en el canvas
        const zoomFactor = event.deltaY > 0 ? 0.9 : 1.1;
        canvas.setZoom(canvas.getZoom() * zoomFactor);
    }
}, { passive: false });

const snapThreshold = 2;
let guiaX = null;
let guiaY = null;

canvas.on('object:moving', function (e) {
    const movingObj = e.target;
    const a = movingObj.getBoundingRect();

    // Eliminar guías anteriores
    if (guiaX) canvas.remove(guiaX);
    if (guiaY) canvas.remove(guiaY);
    guiaX = guiaY = null;

    let snappedX = false;
    let snappedY = false;

    const objetosAConsiderar = canvas.getObjects().filter(obj => !obj.excludeFromAlign);

    objetosAConsiderar.forEach(obj => {
        if (obj === movingObj) return;

        const b = obj.getBoundingRect();

        // BORDES HORIZONTALES
        if (!snappedX) {
            if (Math.abs(a.left - b.left) < snapThreshold) {
                movingObj.left += b.left - a.left;
                drawVerticalGuide(b.left);
                snappedX = true;
            } else if (Math.abs(a.left + a.width - (b.left + b.width)) < snapThreshold) {
                movingObj.left += (b.left + b.width) - (a.left + a.width);
                drawVerticalGuide(b.left + b.width);
                snappedX = true;
            } else if (Math.abs(a.left + a.width / 2 - (b.left + b.width / 2)) < snapThreshold) {
                movingObj.left += (b.left + b.width / 2) - (a.left + a.width / 2);
                drawVerticalGuide(b.left + b.width / 2);
                snappedX = true;
            }
        }

        // BORDES VERTICALES
        if (!snappedY) {
            if (Math.abs(a.top - b.top) < snapThreshold) {
                movingObj.top += b.top - a.top;
                drawHorizontalGuide(b.top);
                snappedY = true;
            } else if (Math.abs(a.top + a.height - (b.top + b.height)) < snapThreshold) {
                movingObj.top += (b.top + b.height) - (a.top + a.height);
                drawHorizontalGuide(b.top + b.height);
                snappedY = true;
            } else if (Math.abs(a.top + a.height / 2 - (b.top + b.height / 2)) < snapThreshold) {
                movingObj.top += (b.top + b.height / 2) - (a.top + a.height / 2);
                drawHorizontalGuide(b.top + b.height / 2);
                snappedY = true;
            }
        }
    });

    canvas.requestRenderAll();
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

// Funciones auxiliares para dibujar guías
function drawVerticalGuide(x) {
    guiaX = new fabric.Line([x, 0, x, canvas.getHeight()], {
        stroke: 'red',
        strokeWidth: 1,
        selectable: false,
        evented: false,
        excludeFromExport: true
    });
    canvas.add(guiaX);
}

function drawHorizontalGuide(y) {
    guiaY = new fabric.Line([0, y, canvas.getWidth(), y], {
        stroke: 'red',
        strokeWidth: 1,
        selectable: false,
        evented: false,
        excludeFromExport: true
    });
    canvas.add(guiaY);
}

const factorConversion = 100; // 100 px = 1 metro

const escalaTexto = new fabric.Text(`Escala: ${factorConversion} px = 1 m`, {
    left: 20,
    top: 20,
    fontSize: 18,
    fill: '#333',
    backgroundColor: '#fff',
    padding: 6,
    excludeFromAlign: true,
    selectable: false,
    evented: false
});
canvas.add(escalaTexto);

function crearEscalaGrafica() {
    const factorConversion = 100; // 100 px = 1 m
    const metros = 2; // longitud total de la regla en metros

    // Línea base
    const linea = new fabric.Line([0, 0, metros * factorConversion, 0], {
        left: 20,
        top: 60,
        stroke: 'black',
        strokeWidth: 2,
        selectable: false,
        evented: false,
    });

    const marcas = [];
    for (let i = 0; i <= metros; i++) {
        // Marca vertical
        const marca = new fabric.Line([0, -5, 0, 5], {
            left: 20 + i * factorConversion,
            top: 60,
            stroke: 'black',
            strokeWidth: 2,
            selectable: false,
            evented: false,
        });

        // Texto de la marca
        const texto = new fabric.Text(`${i} m`, {
            left: 20 + i * factorConversion,
            top: 70,
            fontSize: 14,
            originX: 'center',
            selectable: false,
            evented: false,
        });

        marcas.push(marca, texto);
    }

    // Agrupar todo
    const grupoEscala = new fabric.Group([linea, ...marcas], {
        excludeFromAlign: true,
        selectable: false,
        evented: false,
        left: 20,
        top: 50,
    });

    canvas.add(grupoEscala);
}

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

canvas.on('object:scaling', function(e) {
  const obj = e.target;

  // Si el objeto tiene una propiedad isMedida (o algo para identificarlo)
  if (obj.isPared || obj.isPuerta) {
    // Suponemos que el texto de medida es un objeto hijo o propiedad del objeto
    if (obj.medidaTexto) {
      // Ajustamos el scaleX y scaleY del texto para que sean el inverso del objeto
      obj.medidaTexto.scaleX = 1 / obj.scaleX;
      obj.medidaTexto.scaleY = 1 / obj.scaleY;

      // También opcionalmente reajustar posición del texto si quieres que siga un borde fijo
      // obj.medidaTexto.left = ...
      // obj.medidaTexto.top = ...

      obj.medidaTexto.setCoords(); // actualizar bounds
    }
  }
});

