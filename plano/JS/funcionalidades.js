// =======================
// INICIALIZACIÓN DEL CANVAS Y CONSTANTES
// =======================

// Se crea el canvas de Fabric.js y se definen constantes de configuración
const canvas = new fabric.Canvas('canvas', {
    backgroundColor: '#fcfcfc'
});

const zonaBloqueadaAltura = 100; // Altura de la zona bloqueada superior en píxeles
const factorConversion = 100; // 100 px = 1 metro
const ZOOM_MIN = 0.6;         // Zoom mínimo permitido
const ZOOM_MAX = 2;           // Zoom máximo permitido
const ZOOM_INICIAL = 0.6;     // Zoom inicial
const VIEWPORT_INICIAL = [1, 0, 0, 1, 0, 0]; // Transformación inicial del viewport

// Variables de estado globales
let medidasVisibles = true; // Controla si las medidas están visibles
let guiaX = null;           // Línea guía vertical
let guiaY = null;           // Línea guía horizontal
let moveMode = false;       // Modo mover (pan)
let lastPan = { x: 0, y: 0 }; // Última posición del mouse para pan
let objetoCopiado = null;   // Objeto copiado para pegar
let controlesCopiados = null; // Controles copiados
let controlesVisibilidadCopiados = null; // Visibilidad de controles copiados
let textosRelacionadosCopiados = null;   // Textos relacionados copiados

// =======================
// ESCALA GRÁFICA
// =======================

// Dibuja la regla de escala gráfica en el canvas
function crearEscalaGrafica() {
    const factorConversion = 100; // 100 px = 1 m
    const metros = 2; // longitud total de la regla en metros

    // Línea base de la regla
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
        // Marca vertical de la regla
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

    // Agrupa la línea y las marcas
    const grupoEscala = new fabric.Group([linea, ...marcas], {
        excludeFromAlign: true,
        selectable: false,
        evented: false,
        left: 20,
        top: 50,
    });

    canvas.add(grupoEscala);
}

// Texto de la escala
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

// Dibuja la escala gráfica
crearEscalaGrafica();

// =======================
// AJUSTE DE CANVAS SEGÚN SIDEBAR Y EVENTOS DE VENTANA
// =======================

// Ajusta el tamaño del canvas según el estado del sidebar y el tamaño de la ventana
function ajustarCanvasSegunSidebar() {
    const sidebar = document.getElementById('sidebar');
    const sidebarVisible = !sidebar.classList.contains('collapsed');
    const sidebarWidth = sidebarVisible ? 440 : 40;
    const ancho = window.innerWidth - sidebarWidth;
    canvas.setWidth(ancho);
    canvas.setHeight(window.innerHeight - 230);
    canvas.calcOffset && canvas.calcOffset();
    canvas.requestRenderAll();
}

// Eventos para ajustar el canvas cuando cambia el tamaño de la ventana o el sidebar
window.addEventListener('resize', ajustarCanvasSegunSidebar);
document.getElementById('toggle-sidebar-btn').addEventListener('click', function () {
    setTimeout(ajustarCanvasSegunSidebar);
});
ajustarCanvasSegunSidebar();

// =======================
// ICONOS Y FUNCIONES DE RENDER DE CONTROLES
// =======================

// Carga los iconos para los controles personalizados (rotar y clonar)
const rotateIcon = "/img/plano/voltear.png";
const cloneIcon = "/img/plano/duplicar.png";
const rotateImg = document.createElement('img');
rotateImg.src = rotateIcon;
const cloneImg = document.createElement('img');
cloneImg.src = cloneIcon;

// Función para renderizar un icono en un control de Fabric.js
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

// =======================
// FUNCIONES DE AGREGADO DE OBJETOS (PRODUCTO, PARED, VENTANA, PUERTA)
// =======================

// Agrega un producto (imagen) al canvas con controles personalizados
function agregarProducto(imagenURL, medidas) {
    medidas = JSON.parse(medidas);
    const anchoPx = medidas.ancho;
    const altoPx = medidas.largo;

    fabric.Image.fromURL(imagenURL, function (img) {
        const scaleX = anchoPx / img.width;
        const scaleY = altoPx / img.height;

        img.set({
            left: 50,
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
            isProducto: true
        });

        // Control para rotar
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

        // Control para clonar
        img.controls.cloneControl = new fabric.Control({
            x: -0.5,
            y: -0.5,
            offsetY: -16,
            offsetX: -16,
            cursorStyle: 'pointer',
            mouseUpHandler: clonar,
            render: renderIcon(cloneImg),
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

// Agrega una pared al canvas
function agregarPared() {
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
        selectable: false,
    });

    // Control para rotar
    pared.controls.rotateControl = new fabric.Control({
        x: 0.5,
        y: -0.5,
        offsetY: -16,
        offsetX: 16,
        cursorStyle: 'pointer',
        mouseUpHandler: rotarImagen,
        render: renderIcon(rotateImg),
        cornerSize: 24,
    });

    // Control para clonar
    pared.controls.cloneControl = new fabric.Control({
        x: -0.5,
        y: -0.5,
        offsetY: -16,
        offsetX: -16,
        cursorStyle: 'pointer',
        mouseUpHandler: clonar,
        render: renderIcon(cloneImg),
        cornerSize: 24,
    });

    // Agrupa la pared
    const grupo = new fabric.Group([pared], {
        left: 200,
        top: 200,
        hasControls: true,
        lockScalingY: true,
        lockRotation: false,
    });

    canvas.add(grupo);
    canvas.setActiveObject(grupo);

    // Visibilidad de controles
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

    // Texto de medida asociado a la pared
    const textoMedida = new fabric.Text('', {
        fontSize: 20,
        fill: '#000',
        backgroundColor: 'white',
        originX: 'center',
        originY: 'bottom',
        selectable: false,
        evented: false,
        excludeFromExport: false,
        visible: medidasVisibles,
        isPared: true
    });

    canvas.add(textoMedida);

    grupo.relatedTexts = [textoMedida];

    // Función para actualizar la medida de la pared
    grupo.actualizarMedida = function () {
        actualizarMedidaComun(pared, grupo, textoMedida, 10);
    };

    grupo.on('scaling', grupo.actualizarMedida);
    grupo.on('modified', grupo.actualizarMedida);
    grupo.on('moving', grupo.actualizarMedida);
    grupo.on('rotating', grupo.actualizarMedida);

    grupo.actualizarMedida();
}

// Agrega una ventana al canvas
function agregarVentana() {
    const ventana = new fabric.Rect({
        left: 0,
        top: 0,
        fill: '#ffffff',
        width: 120,
        height: 13,
        stroke: '#000000',
        strokeWidth: 2,
        originX: 'center',
        originY: 'center',
        selectable: false,
    });

    // Control para rotar
    ventana.controls.rotateControl = new fabric.Control({
        x: 0.5,
        y: -0.5,
        offsetY: -16,
        offsetX: 16,
        cursorStyle: 'pointer',
        mouseUpHandler: rotarImagen,
        render: renderIcon(rotateImg),
        cornerSize: 24,
    });

    // Control para clonar
    ventana.controls.cloneControl = new fabric.Control({
        x: -0.5,
        y: -0.5,
        offsetY: -16,
        offsetX: -16,
        cursorStyle: 'pointer',
        mouseUpHandler: clonar,
        render: renderIcon(cloneImg),
        cornerSize: 24,
    });

    // Agrupa la ventana
    const grupo = new fabric.Group([ventana], {
        left: 200,
        top: 200,
        hasControls: true,
        lockScalingY: true,
        lockRotation: false,
    });

    canvas.add(grupo);
    canvas.setActiveObject(grupo);

    // Visibilidad de controles
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

    // Texto de medida asociado a la ventana
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

    // Función para actualizar la medida de la ventana
    grupo.actualizarMedida = function () {
        actualizarMedidaComun(ventana, grupo, textoMedida, 10);
    };

    grupo.on('scaling', grupo.actualizarMedida);
    grupo.on('modified', grupo.actualizarMedida);
    grupo.on('moving', grupo.actualizarMedida);
    grupo.on('rotating', grupo.actualizarMedida);

    grupo.actualizarMedida();
}

// Agrega una puerta al canvas
function agregarPuerta() {
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

    // Control para rotar
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

    // Control para clonar
    puerta.controls.cloneControl = new fabric.Control({
        x: -0.5,
        y: -0.5,
        offsetY: -16,
        offsetX: -16,
        cursorStyle: 'pointer',
        mouseUpHandler: clonar,
        render: renderIcon(cloneImg),
        cornerSize: 24,
    });

    // Carga la imagen de la puerta y la agrupa con el rectángulo
    fabric.Image.fromURL("../../img/plano/Puerta.png", function (img_puerta) {
        img_puerta.set({
            left: 0,
            top: 0,
            originX: 'center',
            originY: 'center',
            scaleX: 100 / img_puerta.width,
            scaleY: 100 / img_puerta.height
        });

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

        // Texto de medida asociado a la puerta
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

        // Función para actualizar la medida de la puerta
        grupo.actualizarMedida = function () {
            actualizarMedidaComun(puerta, grupo, textoMedida, 33);
        };

        grupo.on('scaling', grupo.actualizarMedida);
        grupo.on('modified', grupo.actualizarMedida);
        grupo.on('moving', grupo.actualizarMedida);
        grupo.on('rotating', grupo.actualizarMedida);

        grupo.actualizarMedida();
    });
}

// =======================
// FUNCIONES DE MEDIDAS Y ACTUALIZACIÓN DE TEXTOS
// =======================

// Actualiza el texto de medida de un objeto (pared, ventana, puerta)
function actualizarMedidaComun(objRect, grupo, textoMedida, offset) {
    const factorConversion = 100; // 100 px = 1 metro
    const anchoPx = objRect.width * grupo.scaleX;
    const metros = (anchoPx / factorConversion).toFixed(2) + ' m';
    textoMedida.text = metros;

    const center = grupo.getCenterPoint();
    let angle = grupo.angle % 360;
    if (angle < 0) angle += 360;
    const radio = (objRect.height * grupo.scaleY) / 2 + 25;
    const rad = fabric.util.degreesToRadians(angle - 90);
    textoMedida.left = center.x + (radio + offset) * Math.cos(rad);
    textoMedida.top = center.y + (radio + offset) * Math.sin(rad);

    textoMedida.scaleX = 1;
    textoMedida.scaleY = 1;

    canvas.requestRenderAll();
}

// =======================
// FUNCIONES DE BORRADO Y CONTROL DE SELECCIÓN
// =======================

// Borra el objeto seleccionado o todo el plano si no hay selección
function borrarObjeto() {
    const activeObject = canvas.getActiveObject();

    if (activeObject) {
        if (activeObject.type === 'activeSelection') {
            activeObject.forEachObject(function (obj) {
                canvas.remove(obj);
                if (obj.relatedTexts) {
                    obj.relatedTexts.forEach(txt => canvas.remove(txt));
                }
            });
            canvas.discardActiveObject();
        } else {
            if (activeObject.relatedTexts) {
                activeObject.relatedTexts.forEach(txt => canvas.remove(txt));
            }
            canvas.remove(activeObject);
        }
        canvas.requestRenderAll();
    } else {
        if (confirm("No hay ningún objeto seleccionado. ¿Quieres borrar TODO el plano?")) {
            canvas.getObjects().forEach(obj => {
                if (
                    (!obj.excludeFromAlign && !obj.excludeFromExport) ||
                    (obj.type === 'text' && obj.excludeFromExport)
                ) {
                    canvas.remove(obj);
                }
            });
            canvas.discardActiveObject();
            canvas.requestRenderAll();
        }
    }
}

// Oculta los controles de selección múltiple
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

// =======================
// FUNCIONES DE ROTACIÓN Y CLONADO
// =======================

// Invierte horizontalmente el objeto (flipX) al rotar
function rotarImagen(eventData, transform) {
    const target = transform.target;
    if (target) {
        target.flipX = !target.flipX;
        target.canvas.requestRenderAll();
    }
    return false;
}

// Clona el objeto seleccionado
function clonar() {
    copiarObjeto();
    setTimeout(() => {
        pegarObjeto();
    }, 10);
}

// =======================
// FUNCIONES DE COPIAR Y PEGAR OBJETOS
// =======================

// Copia el objeto seleccionado y guarda sus controles y textos relacionados
function copiarObjeto() {
    const activeObject = canvas.getActiveObject();
    if (activeObject) {
        activeObject.clone((cloned) => {
            objetoCopiado = cloned;
            controlesCopiados = activeObject.controls ? { ...activeObject.controls } : null;
            controlesVisibilidadCopiados = activeObject._controlsVisibility ? { ...activeObject._controlsVisibility } : null;
            if (activeObject.relatedTexts) {
                textosRelacionadosCopiados = activeObject.relatedTexts.map(txt => txt);
            } else {
                textosRelacionadosCopiados = null;
            }
        });
    }
}

// Pega el objeto copiado en el canvas, ajustando posición y textos relacionados
function pegarObjeto() {
    if (objetoCopiado) {
        objetoCopiado.clone((clonedObj) => {
            clonedObj.left += 20;
            clonedObj.top += 20;
            if (controlesCopiados) clonedObj.controls = { ...controlesCopiados };
            if (controlesVisibilidadCopiados && clonedObj.setControlsVisibility) {
                clonedObj.setControlsVisibility(controlesVisibilidadCopiados);
            }

            // Si es producto con texto de medida
            if (clonedObj.isProducto && objetoCopiado.medidaTexto) {
                objetoCopiado.medidaTexto.clone((clonedMedida) => {
                    clonedMedida.left += 20;
                    clonedMedida.top += 20;
                    canvas.add(clonedMedida);
                    clonedObj.medidaTexto = clonedMedida;

                    clonedObj.actualizarMedida = function () {
                        const ancho = clonedObj.width * clonedObj.scaleX;
                        const metros = (ancho / 100).toFixed(2) + ' m';
                        clonedMedida.text = metros;
                        clonedMedida.left = clonedObj.left + (clonedObj.width * clonedObj.scaleX) / 2;
                        clonedMedida.top = clonedObj.top + (clonedObj.height * clonedObj.scaleY) + 10;
                        canvas.requestRenderAll();
                    };

                    clonedObj.on('scaling', clonedObj.actualizarMedida);
                    clonedObj.on('modified', clonedObj.actualizarMedida);
                    clonedObj.on('moving', clonedObj.actualizarMedida);
                    clonedObj.on('rotating', clonedObj.actualizarMedida);

                    clonedObj.actualizarMedida();

                    canvas.add(clonedObj);
                    canvas.setActiveObject(clonedObj);
                    canvas.requestRenderAll();
                });
                return;
            }

            // Si tiene textos relacionados (pared, ventana, puerta)
            if (textosRelacionadosCopiados && Array.isArray(textosRelacionadosCopiados)) {
                clonedObj.relatedTexts = [];
                let textosClonados = 0;
                textosRelacionadosCopiados.forEach((txt, idx) => {
                    txt.clone((clonedTxt) => {
                        clonedTxt.left += 20;
                        clonedTxt.top += 20;
                        clonedTxt.selectable = false;
                        clonedTxt.evented = false;
                        canvas.add(clonedTxt);
                        clonedObj.relatedTexts.push(clonedTxt);
                        textosClonados++;
                        if (textosClonados === textosRelacionadosCopiados.length) {
                            let baseRect = null;
                            if (clonedObj.type === 'group' && clonedObj._objects && clonedObj._objects.length > 0) {
                                baseRect = clonedObj._objects.find(o => o.type === 'rect');
                            }
                            let offset = 10;
                            if (baseRect && baseRect.fill === 'transparent') offset = 33;
                            else if (baseRect && baseRect.width === 120) offset = 10;

                            clonedObj.actualizarMedida = function () {
                                if (baseRect && clonedObj.relatedTexts[0]) {
                                    actualizarMedidaComun(baseRect, clonedObj, clonedObj.relatedTexts[0], offset);
                                }
                            };

                            clonedObj.on('scaling', clonedObj.actualizarMedida);
                            clonedObj.on('modified', clonedObj.actualizarMedida);
                            clonedObj.on('moving', clonedObj.actualizarMedida);
                            clonedObj.on('rotating', clonedObj.actualizarMedida);

                            clonedObj.actualizarMedida();

                            canvas.add(clonedObj);
                            canvas.discardActiveObject();
                            canvas.setActiveObject(clonedObj);
                            canvas.requestRenderAll();
                        }
                    });
                });
                return;
            }

            canvas.add(clonedObj);
            canvas.setActiveObject(clonedObj);
            canvas.requestRenderAll();
        });
    }
}

// =======================
// EVENTOS DE TECLADO Y BOTONES
// =======================

// Borrar objeto con tecla Delete
document.addEventListener('keydown', function (event) {
    if (event.key === 'Delete') {
        borrarObjeto();
    }
});
// Copiar objeto con Ctrl+C
document.addEventListener('keydown', function (e) {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'c') {
        copiarObjeto();
    }
});
// Pegar objeto con Ctrl+V
document.addEventListener('keydown', function (e) {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'v') {
        pegarObjeto();
    }
});

// =======================
// EXPORTAR PNG Y GUARDAR CANVAS
// =======================

// Guarda el canvas como imagen PNG
function guardarCanvas() {
    const dataURL = canvas.toDataURL({
        format: 'png'
    });
    const link = document.createElement('a');
    link.href = dataURL;
    link.download = 'diseño.png';
    link.click();
}

// Evento para exportar el canvas como PNG
document.getElementById('export-png').addEventListener('click', function (e) {
    e.preventDefault();
    const dataURL = canvas.toDataURL({ format: 'png' });
    const link = document.createElement('a');
    link.href = dataURL;
    link.download = 'diseño.png';
    link.click();
});

// =======================
// ZOOM Y PAN
// =======================

// Controla el zoom con la rueda del ratón
canvas.on('mouse:wheel', (event) => {
    const delta = event.e.deltaY;
    let zoom = canvas.getZoom();
    const zoomFactor = 0.1;

    if (delta < 0) {
        zoom += zoomFactor;
    } else {
        zoom -= zoomFactor;
    }

    zoom = Math.max(ZOOM_MIN, Math.min(ZOOM_MAX, zoom));
    canvas.setZoom(zoom);

    // Si el zoom es mínimo, centra el viewport
    if (zoom === ZOOM_MIN) {
        canvas.viewportTransform[4] = 0;
        canvas.viewportTransform[5] = 0;
        canvas.requestRenderAll();
    }

    event.e.preventDefault();
    event.e.stopPropagation();
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
    const contenidoWidth = 2250;
    const contenidoHeight = canvas.getHeight();

    vpt[4] += e.clientX - lastPan.x;
    vpt[5] += e.clientY - lastPan.y;

    // Limita el pan para no salir del área de contenido
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

// =======================
// MODOS DE INTERACCIÓN (MOUSE Y MOVER)
// =======================

// Inicializa los botones de modo mouse y mover
document.getElementById('mouse-mode-btn').classList.remove('btn-dark');
document.getElementById('mouse-mode-btn').classList.add('btn-outline-dark');
document.getElementById('move-mode-btn').classList.remove('btn-outline-dark');
document.getElementById('move-mode-btn').classList.add('btn-dark');
canvas.defaultCursor = 'default';
canvas.selection = true;
canvas.skipTargetFind = false;

// Evento para activar el modo mouse (selección)
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

// Evento para activar el modo mover (pan)
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

// Botón para restablecer la vista inicial
document.getElementById('reset-view-btn').addEventListener('click', function () {
    canvas.setZoom(ZOOM_INICIAL);
    canvas.viewportTransform = VIEWPORT_INICIAL.slice();
    canvas.requestRenderAll();
});

// =======================
// MOSTRAR/OCULTAR MEDIDAS
// =======================

// Alterna la visibilidad de las medidas en el canvas
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

// Evento para alternar la visibilidad de las medidas
document.getElementById('toggle-measures').addEventListener('click', toggleMedidas);

// =======================
// ALINEACIÓN Y GUÍAS
// =======================

const snapThreshold = 2; // Umbral de alineación para snapping

// Dibuja una guía vertical en la posición dada
function drawVerticalGuide(screenX) {
    const canvasX = canvas.viewportTransform
        ? (screenX - canvas.viewportTransform[4]) / canvas.getZoom()
        : screenX;
    guiaX = new fabric.Line([canvasX, 0, canvasX, canvas.getHeight() / canvas.getZoom()], {
        stroke: 'red',
        strokeWidth: 1 / canvas.getZoom(),
        selectable: false,
        evented: false,
        excludeFromExport: true
    });
    canvas.add(guiaX);
}

// Dibuja una guía horizontal en la posición dada
function drawHorizontalGuide(screenY) {
    const canvasY = canvas.viewportTransform
        ? (screenY - canvas.viewportTransform[5]) / canvas.getZoom()
        : screenY;
    guiaY = new fabric.Line([0, canvasY, canvas.getWidth() / canvas.getZoom(), canvasY], {
        stroke: 'red',
        strokeWidth: 1 / canvas.getZoom(),
        selectable: false,
        evented: false,
        excludeFromExport: true
    });
    canvas.add(guiaY);
}

// Evento de movimiento de objeto para mostrar guías y snapping
canvas.on('object:moving', function (e) {
    const movingObj = e.target;
    const a = movingObj.getBoundingRect();

    if (guiaX) canvas.remove(guiaX);
    if (guiaY) canvas.remove(guiaY);
    guiaX = guiaY = null;

    let snappedX = false;
    let snappedY = false;

    const objetosAConsiderar = canvas.getObjects().filter(obj => !obj.excludeFromAlign);

    objetosAConsiderar.forEach(obj => {
        if (obj === movingObj) return;

        const b = obj.getBoundingRect();

        // Snapping en X
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

        // Snapping en Y
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

// =======================
// RESTRICCIONES DE ZONA BLOQUEADA
// =======================

// Restringe el movimiento de objetos para que no entren en la zona bloqueada superior
function restringirZonaBloqueada(obj) {
    if (obj.text && obj.text.startsWith('Escala:')) return;
    if (obj.type === 'group' && obj._objects && obj._objects.some(o => o.type === 'line' && o.top === 60)) return;

    obj.setCoords();
    const boundingRect = obj.getBoundingRect(true, true);

    if (boundingRect.top < zonaBloqueadaAltura) {
        const delta = zonaBloqueadaAltura - boundingRect.top;
        obj.top += delta;
        obj.setCoords();
    }
}

// Aplica la restricción en los eventos de mover, escalar y rotar
canvas.on('object:moving', function (e) {
    restringirZonaBloqueada(e.target);
});
canvas.on('object:scaling', function (e) {
    restringirZonaBloqueada(e.target);
});
canvas.on('object:rotating', function (e) {
    restringirZonaBloqueada(e.target);
});

// =======================
// ACTUALIZACIÓN DE MEDIDAS EN SELECCIÓN Y MODIFICACIÓN
// =======================

// Actualiza las medidas de los objetos seleccionados o modificados
canvas.on('object:moving', function (e) {
    const target = e.target;
    if (target && target.type === 'activeSelection') {
        target.forEachObject(function (obj) {
            if (typeof obj.actualizarMedida === 'function') {
                obj.actualizarMedida();
            }
        });
    } else if (target && typeof target.actualizarMedida === 'function') {
        target.actualizarMedida();
    }
    canvas.requestRenderAll();
});

canvas.on('object:modified', function (e) {
    const target = e.target;
    if (target && target.type === 'activeSelection') {
        target.forEachObject(function (obj) {
            if (typeof obj.actualizarMedida === 'function') {
                obj.actualizarMedida();
            }
        });
    } else if (target && typeof target.actualizarMedida === 'function') {
        target.actualizarMedida();
    }
    canvas.requestRenderAll();
});

// =======================
// SNAPPING DE ÁNGULOS Y LIMPIEZA DE GUÍAS
// =======================

// Ajusta el ángulo del objeto a 0, 90, 180, 270 o 360 si está cerca y limpia las guías
canvas.on('object:modified', function (e) {
    const obj = e.target;

    if (obj.angle != null) {
        const snapAngles = [0, 90, 180, 270, 360];
        const tolerance = 10;
        const currentAngle = obj.angle % 360;

        for (let angle of snapAngles) {
            if (Math.abs(currentAngle - angle) < tolerance) {
                obj.angle = angle;
                canvas.requestRenderAll();
                break;
            }
        }
    }

    if (guiaX) {
        canvas.remove(guiaX);
        guiaX = null;
    }
    if (guiaY) {
        canvas.remove(guiaY);
        guiaY = null;
    }
});

// =======================
// ESCALADO DE TEXTOS DE MEDIDA
// =======================

// Mantiene el texto de medida con tamaño constante al escalar paredes o puertas
canvas.on('object:scaling', function (e) {
    const obj = e.target;
    if (obj.isPared || obj.isPuerta) {
        if (obj.medidaTexto) {
            obj.medidaTexto.scaleX = 1 / obj.scaleX;
            obj.medidaTexto.scaleY = 1 / obj.scaleY;
            obj.medidaTexto.setCoords();
        }
    }
});