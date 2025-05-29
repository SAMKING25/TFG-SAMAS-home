
const canvas = new fabric.Canvas('canvas', {
    backgroundColor: '#fcfcfc'
});

//Hammer.js para gestos táctiles
// const tacto = new Hammer(document.getElementById('canvas'));

// // Habilita el reconocimiento de pinch
// tacto.get('pinch').set({ enable: true });

// let lastScale = 1;
// let lastZoom = canvas.getZoom();

// tacto.on("pinchstart", function(ev) {
//     lastScale = ev.scale;
//     lastZoom = canvas.getZoom();
// });

// tacto.on("pinchmove", function(ev) {
//     // Calcula el nuevo zoom relativo al zoom anterior
//     let newZoom = lastZoom * ev.scale / lastScale;
//     // Limita el zoom
//     newZoom = Math.max(0.6, Math.min(2, newZoom));
//     canvas.setZoom(newZoom);
//     canvas.requestRenderAll();
// });

const zonaBloqueadaAltura = 100; // píxeles desde arriba

crearEscalaGrafica();

function ajustarCanvasSegunSidebar() {
    const sidebar = document.getElementById('sidebar');
    const sidebarVisible = !sidebar.classList.contains('collapsed');
    const sidebarWidth = sidebarVisible ? 440 : 40;
    // Calcula el ancho disponible restando el sidebar si está visible
    const ancho = window.innerWidth - sidebarWidth;
    canvas.setWidth(ancho);
    canvas.setHeight(window.innerHeight - 230);
    canvas.calcOffset && canvas.calcOffset();
    canvas.requestRenderAll();
}

// Llama al ajustar tamaño de ventana
window.addEventListener('resize', ajustarCanvasSegunSidebar);

// Llama también cuando se colapsa o expande el sidebar
document.getElementById('toggle-sidebar-btn').addEventListener('click', function () {
    setTimeout(ajustarCanvasSegunSidebar); 
});

// Llama al cargar la página
ajustarCanvasSegunSidebar();

const rotateIcon =
    "/img/plano/voltear.png";

const rotateImg = document.createElement('img');
rotateImg.src = rotateIcon;

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
            // Asegura que la imagen se cree justo debajo de la zona bloqueada
            top: Math.max(zonaBloqueadaAltura + 10, 50),
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

        img.controls.rotateControl = new fabric.Control({
            x: 0.5,
            y: -0.5,
            offsetY: -16,
            offsetX: 16,
            cursorStyle: 'pointer',
            mouseUpHandler: rotarImagen,
            render: renderIcon(rotateImg),
            cornerSize: 24,
        });

        canvas.add(img);
        canvas.setActiveObject(img);
        img.setControlsVisibility({
            tl: false,
            tr: false,
            bl: false,
            br: false,
            mt: false,
            mb: false,
            ml: false,
            mr: false,
            mtr: true
        });
        canvas.renderAll();
    });
}

function agregarProductoSidebar(element, imagenURL, medidas) {
    agregarProducto(imagenURL, medidas);

    // Restar cantidad visualmente
    const cantidadNum = element.querySelector('.cantidad-num');
    let cantidad = parseInt(cantidadNum.textContent, 10);
    cantidad--;
    // Guarda los datos del producto en el objeto fabric
    const lastObj = canvas.getObjects().slice(-1)[0];
    if (lastObj) {
        lastObj.productoSidebarData = {
            nombre: element.querySelector('span').childNodes[0].textContent.trim(),
            img: element.querySelector('img').getAttribute('src'),
            categoria: imagenURL.split('/').pop().split('.')[0],
            medidas: medidas
        };
    }
    if (cantidad <= 0) {
        element.remove();
    } else {
        cantidadNum.textContent = cantidad;
    }
}

function rotarImagen(eventData, transform) {
    const target = transform.target;
    if (target) {
        target.flipX = !target.flipX; // Voltea horizontalmente
        target.canvas.requestRenderAll();
    }
    return false;
}

function renderIcon(icon) {
  return function (ctx, left, top, _styleOverride, fabricObject) {
    const size = this.cornerSize;
    ctx.save();
    ctx.translate(left, top);
    ctx.rotate(fabric.util.degreesToRadians(fabricObject.angle));
    ctx.drawImage(icon, -size / 2, -size / 2, size, size);
    ctx.restore();
  };
}

function borrarObjeto() {
    const activeObject = canvas.getActiveObject();

    function restaurarSidebar(obj) {
        if (obj.type === 'image' && obj.productoSidebarData) {
            const data = obj.productoSidebarData;
            const productosSidebar = document.querySelectorAll('#productos .list-group-item');
            let encontrado = false;

            productosSidebar.forEach(item => {
                const img = item.querySelector('img');
                const nombre = item.querySelector('span').childNodes[0].textContent.trim();
                if (img && img.src === location.origin + data.img.replace('..', '') && nombre === data.nombre) {
                    const cantidadNum = item.querySelector('.cantidad-num');
                    let cantidad = parseInt(cantidadNum.textContent, 10);
                    cantidadNum.textContent = cantidad + 1;
                    encontrado = true;
                }
            });

            if (!encontrado) {
                const productosDiv = document.getElementById('productos');
                const div = document.createElement('div');
                div.className = "list-group-item list-group-item-action d-flex align-items-center";
                div.style.cursor = "pointer";
                div.onclick = function() {
                    agregarProductoSidebar(this, '../../img/plano/' + data.categoria + '.png', data.medidas);
                };
                div.setAttribute('data-medidas', data.medidas);

                div.innerHTML = `
                    <img src="${data.img}" alt="${data.nombre}" class="me-2">
                    <span>
                        ${data.nombre}<br>
                        <small class="text-muted cantidad-label">Cantidad: <span class="cantidad-num">1</span></small>
                    </span>
                `;
                productosDiv.appendChild(div);
            }
        }
    }

    if (activeObject) {
        let objetos = [];
        if (activeObject.type === 'activeSelection') {
            objetos = activeObject.getObjects().slice();
        } else {
            objetos = [activeObject];
        }

        // Descarta la selección activa antes de eliminar objetos
        canvas.discardActiveObject();

        objetos.forEach(function (obj) {
            restaurarSidebar(obj);
            if (obj.relatedTexts) {
                obj.relatedTexts.forEach(txt => canvas.remove(txt));
            }
            canvas.remove(obj);
        });

        canvas.requestRenderAll();
    }
}

// Limita los handlers visibles en selección múltiple
fabric.ActiveSelection.prototype.controls = {
    tl: new fabric.Control({ visible: false }),
    tr: new fabric.Control({ visible: false }),
    bl: new fabric.Control({ visible: false }),
    br: new fabric.Control({ visible: false }),
    mt: new fabric.Control({ visible: false }),
    mb: new fabric.Control({ visible: false }),
    ml: new fabric.Control({ visible: false }),
    mr: new fabric.Control({ visible: false }),
    // mtr: new fabric.Control({ visible: true }),
};

function agregarPared() {
    const factorConversion = 100; // 100px = 1 metro

    const pared = new fabric.Rect({
        left: 0,
        top: 0,
        fill: '#fhfhfh',
        width: 200,
        height: 15,
        stroke: '#000000',
        strokeWidth: 2,
        originX: 'center',
        originY: 'center',
        selectable: false
    });

    const grupo = new fabric.Group([pared], {
        left: 200,
        top: 200,
        hasControls: true,
        lockScalingY: true,
        lockRotation: false,
    });

    canvas.add(grupo);
    canvas.setActiveObject(grupo);

    grupo.setControlsVisibility({
        tl: false,
        tr: false,
        bl: false,
        br: false,
        mt: false,
        mb: false,
        ml: true,
        mr: true,
        mtr: true
    });

    // Texto que indica la longitud
    const textoMedida = new fabric.Text('', {
        fontSize: 20,
        fill: '#000',
        backgroundColor: 'white',
        originX: 'center',
        originY: 'bottom',
        selectable: false,
        evented: false,
        excludeFromExport: false,
        visible: medidasVisibles
    });

    canvas.add(textoMedida);

    grupo.relatedTexts = [textoMedida];

    function actualizarMedida() {
        // Calcula la medida real
        const anchoPx = pared.width * grupo.scaleX;
        const metros = (anchoPx / factorConversion).toFixed(2) + ' m';
        textoMedida.text = metros;

        // Calcula el centro del grupo
        const center = grupo.getCenterPoint();

        // Calcula el ángulo de rotación
        let angle = grupo.angle % 360;
        if (angle < 0) angle += 360;

        // Offset pequeño para que el texto quede pegado al rectángulo
        const offset = 18; // Puedes ajustar este valor para acercar/alejar el texto

        // Radio exterior del grupo (mitad del alto del rectángulo)
        const radio = (pared.height * grupo.scaleY) / 2 + 10;

        // Calcula la posición fuera del grupo, en la parte superior (según rotación)
        const rad = fabric.util.degreesToRadians(angle - 90); // -90 para ponerlo arriba
        textoMedida.left = center.x + (radio + offset) * Math.cos(rad);
        textoMedida.top = center.y + (radio + offset) * Math.sin(rad);

        textoMedida.scaleX = 1;
        textoMedida.scaleY = 1;

        canvas.requestRenderAll();
    }

    grupo.on('scaling', actualizarMedida);
    grupo.on('modified', actualizarMedida);
    grupo.on('moving', actualizarMedida);
    grupo.on('rotating', actualizarMedida);

    actualizarMedida();
}

function agregarPuerta() {
    const factorConversion = 100; // 100px = 1 metro

    // Crea el rectángulo de la puerta (invisible, solo para agrupar)
    const puerta = new fabric.Rect({
        left: 0,
        top: 0,
        fill: 'transparent',
        width: 100,
        height: 15,
        stroke: 'transparent',
        strokeWidth: 2,
        selectable: false,
        originX: 'center',
        originY: 'center',
    });

    puerta.controls.rotateControl = new fabric.Control({
        x: 0.5,
        y: -0.5,
        offsetY: -16,
        offsetX: 16,
        cursorStyle: 'pointer',
        mouseUpHandler: rotarImagen,
        render: renderIcon(rotateImg),
        cornerSize: 24,
    });

    // Carga la imagen de la puerta y crea el grupo
    fabric.Image.fromURL("../../img/plano/Puerta.png", function (img_puerta) {
        img_puerta.set({
            left: 0,
            top: 0,
            originX: 'center',
            originY: 'center',
            scaleX: 100 / img_puerta.width,
            scaleY: 100 / img_puerta.height
        });

        // Crea el grupo con el rectángulo y la imagen
        const grupo = new fabric.Group([puerta, img_puerta], {
            left: 150,
            top: 150,
            hasControls: true,
            lockScalingY: false,
            lockRotation: false,
        });

        canvas.add(grupo);
        canvas.setActiveObject(grupo);
        grupo.setControlsVisibility({
            tl: true,
            tr: true,
            bl: true,
            br: true,
            mt: false,
            mb: false,
            ml: false,
            mr: false,
            mtr: true
        });

        // Texto de medida (fuera del grupo)
        const textoMedida = new fabric.Text('', {
            fontSize: 20,
            fill: '#000',
            backgroundColor: 'white',
            originX: 'center',
            originY: 'bottom',
            selectable: false,
            evented: false,
            excludeFromExport: false,
            visible: medidasVisibles
        });

        canvas.add(textoMedida);

        grupo.relatedTexts = [textoMedida];

        function actualizarMedida() {
            // Calcula la medida real
            const anchoPx = puerta.width * grupo.scaleX;
            const metros = (anchoPx / factorConversion).toFixed(2) + ' m';
            textoMedida.text = metros;

            // Calcula el centro del grupo
            const center = grupo.getCenterPoint();

            // Calcula el ángulo de rotación
            let angle = grupo.angle % 360;
            if (angle < 0) angle += 360;

            // Calcula la posición del texto: por encima del grupo, centrado horizontalmente
            // Puedes ajustar el offset para que quede más separado
            const offset = 30;
            // Calcula el radio exterior del grupo (mitad del ancho del grupo)
            const radio = (puerta.width * grupo.scaleX) / 2;

            // Calcula la posición fuera del grupo, en la parte superior (según rotación)
            const rad = fabric.util.degreesToRadians(angle - 90); // -90 para ponerlo arriba
            textoMedida.left = center.x + (radio + offset) * Math.cos(rad);
            textoMedida.top = center.y + (radio + offset) * Math.sin(rad);

            textoMedida.scaleX = 1;
            textoMedida.scaleY = 1;

            canvas.requestRenderAll();
        }

        grupo.on('scaling', actualizarMedida);
        grupo.on('modified', actualizarMedida);
        grupo.on('moving', actualizarMedida);
        grupo.on('rotating', actualizarMedida);

        actualizarMedida();
    });
}

document.addEventListener('keydown', function (event) {
    if (event.key === 'Delete') {
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

const ZOOM_MIN = 0.6;
const ZOOM_MAX = 2;

canvas.on('mouse:wheel', (event) => {
    const delta = event.e.deltaY;
    let zoom = canvas.getZoom();
    const zoomFactor = 0.1;

    if (delta < 0) {
        // Rueda hacia adelante: acercar (zoom in)
        zoom += zoomFactor;
    } else {
        // Rueda hacia atrás: alejar (zoom out)
        zoom -= zoomFactor;
    }

    // Limitar el zoom a un rango razonable
    zoom = Math.max(ZOOM_MIN, Math.min(ZOOM_MAX, zoom));
    canvas.setZoom(zoom);

    // Si el zoom es mínimo, centra el canvas y bloquea el pan
    if (zoom === ZOOM_MIN) {
        canvas.viewportTransform[4] = 0;
        canvas.viewportTransform[5] = 0;
        canvas.requestRenderAll();
    }

    // Prevenir el scroll de la página
    event.e.preventDefault();
    event.e.stopPropagation();
});

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

canvas.on('object:moving', function (e) {
    const obj = e.target;
    // No restringir la escala ni la regla gráfica
    if (obj.text && obj.text.startsWith('Escala:')) return;
    if (obj.type === 'group' && obj._objects && obj._objects.some(o => o.type === 'line' && o.top === 60)) return;

    // Calcula el borde superior del objeto
    const top = obj.top;
    // Si el objeto se mueve a la zona bloqueada, lo regresa justo debajo
    if (top < zonaBloqueadaAltura) {
        obj.top = zonaBloqueadaAltura;
    }
});

// const lineaBloqueo = new fabric.Line([0, zonaBloqueadaAltura, 2250, zonaBloqueadaAltura], {
//     stroke: '#ccc',
//     strokeDashArray: [5, 5],
//     selectable: false,
//     evented: false,
//     excludeFromExport: true
// });
// canvas.add(lineaBloqueo);
// canvas.sendToBack(lineaBloqueo);


// Mostrar/Ocultar medidas
let medidasVisibles = true;

function toggleMedidas() {
    medidasVisibles = !medidasVisibles;
    canvas.getObjects().forEach(obj => {
        if (obj.type === 'text' && obj.excludeFromExport) {
            obj.visible = medidasVisibles;
        }
        if (obj.relatedTexts) {
            obj.relatedTexts.forEach(txt => {
                txt.visible = medidasVisibles;
            });
        }
    });
    canvas.requestRenderAll();
    const icon = document.getElementById('toggle-measures-icon');
    if (icon) {
        icon.className = medidasVisibles ? 'bi bi-eye' : 'bi bi-eye-slash';
    }
}

document.getElementById('toggle-measures').addEventListener('click', toggleMedidas);

// Al cargar la página, asegúrate de que el icono sea correcto
// document.addEventListener('DOMContentLoaded', function() {
//     const icon = document.getElementById('toggle-measures-icon');
//     if (icon) {
//         icon.className = medidasVisibles ? 'bi bi-eye' : 'bi bi-eye-slash';
//     }
// });


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

canvas.on('object:scaling', function (e) {
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

function restringirZonaBloqueada(obj) {
    // No restringir la escala ni la regla gráfica
    if (obj.text && obj.text.startsWith('Escala:')) return;
    if (obj.type === 'group' && obj._objects && obj._objects.some(o => o.type === 'line' && o.top === 60)) return;

    // Bounding box real (con rotación y escala)
    obj.setCoords();
    const boundingRect = obj.getBoundingRect(true, true);

    // Si el borde superior sube de la zona bloqueada, reajusta el top
    if (boundingRect.top < zonaBloqueadaAltura) {
        // Calcula cuánto se ha pasado y baja el objeto
        const delta = zonaBloqueadaAltura - boundingRect.top;
        obj.top += delta;
        obj.setCoords();
    }
}

// Restringe al escalar
canvas.on('object:scaling', function (e) {
    restringirZonaBloqueada(e.target);
});

// Restringe al rotar
canvas.on('object:rotating', function (e) {
    restringirZonaBloqueada(e.target);
});

// También al mover (por si acaso)
canvas.on('object:moving', function (e) {
    restringirZonaBloqueada(e.target);
});

// Exportar PNG
document.getElementById('export-png').addEventListener('click', function (e) {
    e.preventDefault();
    const dataURL = canvas.toDataURL({ format: 'png' });
    const link = document.createElement('a');
    link.href = dataURL;
    link.download = 'diseño.png';
    link.click();
});

// Exportar JSON
document.getElementById('export-json').addEventListener('click', function (e) {
    e.preventDefault();
    const json = canvas.toDatalessJSON([
        'relatedTexts',
        'excludeFromAlign',
        'excludeFromExport'
    ]);
    const blob = new Blob([JSON.stringify(json, null, 2)], { type: 'application/json' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'diseño.json';
    link.click();
});

// Importar JSON
document.getElementById('import-json-btn').addEventListener('click', function () {
    document.getElementById('import-json-input').click();
});

document.getElementById('import-json-input').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (evt) {
        try {
            const json = JSON.parse(evt.target.result);
            canvas.loadFromJSON(json, () => {
                // Elimina cualquier texto de escala existente
                canvas.getObjects('text').forEach(obj => {
                    if (obj.text && obj.text.startsWith('Escala:')) {
                        canvas.remove(obj);
                    }
                });
                // Elimina cualquier grupo de escala gráfica existente
                canvas.getObjects('group').forEach(obj => {
                    if (
                        obj._objects &&
                        obj._objects.some(o => o.type === 'line') &&
                        obj._objects.some(o => o.type === 'text' && o.text && o.text.match(/^\d+ m$/))
                    ) {
                        canvas.remove(obj);
                    }
                });
                // Elimina cualquier línea de bloqueo antigua
                canvas.getObjects('line').forEach(obj => {
                    if (
                        obj.stroke === '#ccc' &&
                        Array.isArray(obj.strokeDashArray) &&
                        obj.strokeDashArray[0] === 5
                    ) {
                        canvas.remove(obj);
                    }
                });
                // Vuelve a crear la escala fija
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
                canvas.sendToBack(escalaTexto);

                // Vuelve a crear la escala gráfica
                crearEscalaGrafica();

                // --- NUEVO: restaurar relatedTexts ---
                canvas.getObjects().forEach(obj => {
                    if (obj.relatedTexts && Array.isArray(obj.relatedTexts)) {
                        obj.relatedTexts.forEach(txt => {
                            // Si el texto no está ya en el canvas, añádelo
                            if (!canvas.getObjects().includes(txt)) {
                                canvas.add(txt);
                            }
                            // Asegura que la visibilidad sea la correcta
                            txt.visible = medidasVisibles;
                        });
                    }
                });

                // Reaplica los handlers personalizados a cada objeto
                canvas.getObjects().forEach(obj => {
                    // Paredes y puertas (fabric.Group)
                    if (obj.type === 'group' && (obj._objects?.[0]?.fill === '#fhfhfh' || obj._objects?.[0]?.fill === '#9c9c9c')) {
                        obj.setControlsVisibility({
                            tl: false,
                            tr: false,
                            bl: false,
                            br: false,
                            mt: false,
                            mb: false,
                            ml: true,
                            mr: true,
                            mtr: true
                        });

                        // Ya no necesitas volver a buscar los textos, solo asegúrate de que estén en el canvas
                        if (obj.relatedTexts) {
                            obj.relatedTexts.forEach(txt => {
                                txt.visible = medidasVisibles;
                                if (!canvas.getObjects().includes(txt)) {
                                    canvas.add(txt);
                                }
                            });
                        }
                    }
                    // Productos (fabric.Image)
                    if (obj.type === 'image') {
                        obj.setControlsVisibility({
                            tl: false,
                            tr: false,
                            bl: false,
                            br: false,
                            mt: false,
                            mb: false,
                            ml: false,
                            mr: false,
                            mtr: true
                        });
                    }
                });

                canvas.renderAll();
            });
        } catch (err) {
            alert('Archivo JSON inválido.');
        }
    };
    reader.readAsText(file);
});

let moveMode = false;
let lastPan = { x: 0, y: 0 };

// Inicialmente, modo ratón activo
document.getElementById('mouse-mode-btn').classList.remove('btn-dark');
document.getElementById('mouse-mode-btn').classList.add('btn-outline-dark');
document.getElementById('move-mode-btn').classList.remove('btn-outline-dark');
document.getElementById('move-mode-btn').classList.add('btn-dark');
canvas.defaultCursor = 'default';
canvas.selection = true;
canvas.skipTargetFind = false;

// Cambia a modo ratón
document.getElementById('mouse-mode-btn').addEventListener('click', function () {
    moveMode = false;
    this.classList.remove('btn-dark');
    this.classList.add('btn-outline-dark');
    document.getElementById('move-mode-btn').classList.remove('btn-outline-dark');
    document.getElementById('move-mode-btn').classList.add('btn-dark');
    canvas.defaultCursor = 'default';
    canvas.selection = true;
    canvas.skipTargetFind = false;
    canvas.discardActiveObject();
    canvas.requestRenderAll();
});

// Cambia a modo mover
document.getElementById('move-mode-btn').addEventListener('click', function () {
    moveMode = true;
    this.classList.remove('btn-dark');
    this.classList.add('btn-outline-dark');
    document.getElementById('mouse-mode-btn').classList.remove('btn-outline-dark');
    document.getElementById('mouse-mode-btn').classList.add('btn-dark');
    canvas.defaultCursor = 'grab';
    canvas.selection = false;
    canvas.skipTargetFind = true;
    canvas.discardActiveObject();
    canvas.requestRenderAll();
});

// Pan con el ratón cuando el modo mover está activo
canvas.on('mouse:down', function (opt) {
    if (!moveMode) return;
    if (canvas.getZoom() === ZOOM_MIN) {
        canvas.isDragging = false;
        return;
    }
    lastPan = { x: opt.e.clientX, y: opt.e.clientY };
    canvas.isDragging = true;
    canvas.selection = false;
    canvas.defaultCursor = 'grabbing';
});

canvas.on('mouse:move', function (opt) {
    if (!moveMode || !canvas.isDragging) return;
    if (canvas.getZoom() === ZOOM_MIN) return;
    const e = opt.e;
    const vpt = canvas.viewportTransform;

    const zoom = canvas.getZoom();
    const contenidoWidth = 2250; // O el ancho real de tu contenido
    const contenidoHeight = canvas.getHeight();

    vpt[4] += e.clientX - lastPan.x;
    vpt[5] += e.clientY - lastPan.y;

    const minPanX = Math.min(0, canvas.getWidth() - contenidoWidth * zoom);
    const maxPanX = 0;
    vpt[4] = Math.max(minPanX, Math.min(vpt[4], maxPanX));

    const minPanY = Math.min(0, canvas.getHeight() - contenidoHeight * zoom);
    const maxPanY = 0;
    vpt[5] = Math.max(minPanY, Math.min(vpt[5], maxPanY));

    canvas.requestRenderAll();
    lastPan = { x: e.clientX, y: e.clientY };
});

canvas.on('mouse:up', function () {
    if (!moveMode) return;
    canvas.isDragging = false;
    canvas.selection = false;
    canvas.defaultCursor = 'grab';
});

// Guarda los valores iniciales de zoom y viewport
const ZOOM_INICIAL = 0.6;
const VIEWPORT_INICIAL = [1, 0, 0, 1, 0, 0];

// Botón para restablecer la vista inicial
document.getElementById('reset-view-btn').addEventListener('click', function () {
    canvas.setZoom(ZOOM_INICIAL);
    canvas.viewportTransform = VIEWPORT_INICIAL.slice();
    canvas.requestRenderAll();
});

let clipboard = null;

// Copiar (Ctrl+C)
document.addEventListener('keydown', function (e) {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'c') {
        const activeObject = canvas.getActiveObject();
        if (activeObject) {
            activeObject.clone(function (cloned) {
                clipboard = cloned;
            });
            e.preventDefault();
        }
    }
});

// Pegar (Ctrl+V)
document.addEventListener('keydown', function (e) {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'v') {
        if (clipboard) {
            clipboard.clone(function (clonedObj) {
                canvas.discardActiveObject();
                clonedObj.set({
                    left: clonedObj.left + 20,
                    top: clonedObj.top + 20,
                    evented: true
                });
                if (clonedObj.type === 'activeSelection') {
                    // Multi-selection
                    clonedObj.canvas = canvas;
                    clonedObj.forEachObject(function (obj) {
                        canvas.add(obj);
                    });
                    // Group to selection
                    clonedObj.setCoords();
                } else {
                    canvas.add(clonedObj);
                }
                canvas.setActiveObject(clonedObj);
                canvas.requestRenderAll();
            });
            e.preventDefault();
        }
    }
});