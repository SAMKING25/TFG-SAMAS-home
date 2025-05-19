// ======================= CONFIGURACIÓN Y CONSTANTES =======================

// Canvas principal
const canvas = new fabric.Canvas('canvas', { backgroundColor: '#fcfcfc' });

// Altura de la zona bloqueada superior (donde está la escala)
const zonaBloqueadaAltura = 100;

// Conversión de píxeles a metros
const factorConversion = 100; // 100 px = 1 metro

// Estado de visibilidad de las medidas
let medidasVisibles = true;

// Variables para guías de alineación
const snapThreshold = 2;
let guiaX = null, guiaY = null;

// ======================= FUNCIONES DE UTILIDAD =======================

// Dibuja una guía vertical roja en x
function drawVerticalGuide(x) {
    guiaX = new fabric.Line([x, 0, x, canvas.getHeight()], {
        stroke: 'red', strokeWidth: 1,
        selectable: false, evented: false, excludeFromExport: true
    });
    canvas.add(guiaX);
}

// Dibuja una guía horizontal roja en y
function drawHorizontalGuide(y) {
    guiaY = new fabric.Line([0, y, canvas.getWidth(), y], {
        stroke: 'red', strokeWidth: 1,
        selectable: false, evented: false, excludeFromExport: true
    });
    canvas.add(guiaY);
}

// Aplica los handlers personalizados a paredes y puertas (fabric.Group)
function setGroupHandlers(obj) {
    obj.setControlsVisibility({
        tl: false, tr: false, bl: false, br: false,
        mt: false, mb: false, ml: true, mr: true, mtr: true
    });
}

// Aplica los handlers personalizados a productos (fabric.Image)
function setImageHandlers(obj) {
    obj.setControlsVisibility({
        tl: false, tr: false, bl: false, br: false,
        mt: false, mb: false, ml: false, mr: false, mtr: true
    });
}

// Determina si un objeto es la escala gráfica (regla)
function isEscalaGrafica(obj) {
    return obj.type === 'group' &&
        obj._objects &&
        obj._objects.some(o => o.type === 'line') &&
        obj._objects.some(o => o.type === 'text' && o.text && o.text.match(/^\d+ m$/));
}

// Determina si un objeto es la línea de bloqueo
function isLineaBloqueo(obj) {
    return obj.type === 'line' &&
        obj.stroke === '#ccc' &&
        Array.isArray(obj.strokeDashArray) &&
        obj.strokeDashArray[0] === 5;
}

// ======================= ELEMENTOS FIJOS DEL CANVAS =======================

// Crea el texto de escala fijo
function crearTextoEscala() {
    const escalaTexto = new fabric.Text(`Escala: ${factorConversion} px = 1 m`, {
        left: 20, top: 20, fontSize: 18, fill: '#333',
        backgroundColor: '#fff', padding: 6,
        excludeFromAlign: true, selectable: false, evented: false
    });
    canvas.add(escalaTexto);
    canvas.sendToBack(escalaTexto);
}

// Crea la escala gráfica (regla)
function crearEscalaGrafica() {
    const metros = 2;
    const linea = new fabric.Line([0, 0, metros * factorConversion, 0], {
        left: 20, top: 60, stroke: 'black', strokeWidth: 2,
        selectable: false, evented: false,
    });

    const marcas = [];
    for (let i = 0; i <= metros; i++) {
        const marca = new fabric.Line([0, -5, 0, 5], {
            left: 20 + i * factorConversion, top: 60,
            stroke: 'black', strokeWidth: 2,
            selectable: false, evented: false,
        });
        const texto = new fabric.Text(`${i} m`, {
            left: 20 + i * factorConversion, top: 70,
            fontSize: 14, originX: 'center',
            selectable: false, evented: false,
        });
        marcas.push(marca, texto);
    }

    const grupoEscala = new fabric.Group([linea, ...marcas], {
        excludeFromAlign: true, selectable: false, evented: false,
        left: 20, top: 50,
    });

    canvas.add(grupoEscala);
    canvas.sendToBack(grupoEscala);
}

// Crea la línea de bloqueo de la zona superior
function crearLineaBloqueo() {
    const lineaBloqueo = new fabric.Line([0, zonaBloqueadaAltura, canvas.getWidth(), zonaBloqueadaAltura], {
        stroke: '#ccc', strokeDashArray: [5, 5],
        selectable: false, evented: false
    });
    canvas.add(lineaBloqueo);
    canvas.sendToBack(lineaBloqueo);
}

// ======================= ELEMENTOS DINÁMICOS =======================

// Añade un producto (imagen) al canvas
function agregarProducto(imagenURL, medidas) {
    medidas = JSON.parse(medidas);
    const anchoPx = medidas.ancho;
    const altoPx = medidas.largo;

    fabric.Image.fromURL(imagenURL, function (img) {
        const scaleX = anchoPx / img.width;
        const scaleY = altoPx / img.height;

        img.set({
            left: 50, top: 50, scaleX, scaleY,
            hasControls: true,
            lockScalingX: true, lockScalingY: true,
            lockSkewingX: true, lockSkewingY: true,
            lockScalingFlip: true, lockRotation: false,
        });

        canvas.add(img);
        canvas.setActiveObject(img);
        setImageHandlers(img);
        canvas.renderAll();
    });
}

// Añade una pared al canvas
function agregarPared() {
    const pared = new fabric.Rect({
        left: 0, top: 0, fill: '#fhfhfh',
        width: 200, height: 15,
        stroke: '#000000', strokeWidth: 2,
        originX: 'center', originY: 'center',
        selectable: false
    });

    const grupo = new fabric.Group([pared], {
        left: 200, top: 200, hasControls: true,
        lockScalingY: true, lockRotation: false,
    });

    canvas.add(grupo);
    canvas.setActiveObject(grupo);
    setGroupHandlers(grupo);

    // Texto de medida
    const textoMedida = new fabric.Text('', {
        fontSize: 20, fill: '#000', backgroundColor: 'white',
        originX: 'center', originY: 'center',
        selectable: false, evented: false,
        excludeFromExport: true, visible: medidasVisibles
    });
    canvas.add(textoMedida);
    grupo.relatedTexts = [textoMedida];

    function actualizarMedida() {
        const anchoPx = pared.width * grupo.scaleX;
        const metros = (anchoPx / factorConversion).toFixed(2) + ' m';
        textoMedida.text = metros;

        const center = grupo.getCenterPoint();
        let angle = grupo.angle % 360;
        if (angle < 0) angle += 360;

        const offset = 30;
        let textX = center.x, textY = center.y;

        if (angle >= 0 && angle < 45 || angle >= 315 && angle < 360) {
            textY = center.y - (pared.height * grupo.scaleY) / 2 - offset;
        } else if (angle >= 45 && angle < 135) {
            textX = center.x + (pared.height * grupo.scaleY) / 2 + offset;
        } else if (angle >= 135 && angle < 225) {
            textY = center.y + (pared.height * grupo.scaleY) / 2 + offset;
        } else if (angle >= 225 && angle < 315) {
            textX = center.x - (pared.height * grupo.scaleY) / 2 - offset;
        }

        textoMedida.left = textX;
        textoMedida.top = textY;
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

// Añade una puerta al canvas
function agregarPuerta() {
    const puerta = new fabric.Rect({
        left: 0, top: 0, fill: '#9c9c9c',
        width: 100, height: 15,
        stroke: '#9c9c9c', strokeWidth: 2,
        selectable: false, originX: 'center', originY: 'center',
    });

    const grupo = new fabric.Group([puerta], {
        left: 150, top: 150, hasControls: true,
        lockScalingY: true, lockRotation: false,
    });

    canvas.add(grupo);
    canvas.setActiveObject(grupo);
    setGroupHandlers(grupo);

    const textoMedida = new fabric.Text('', {
        fontSize: 20, fill: '#000', backgroundColor: 'white',
        originX: 'center', originY: 'center',
        selectable: false, evented: false,
        excludeFromExport: true, visible: medidasVisibles
    });
    canvas.add(textoMedida);
    grupo.relatedTexts = [textoMedida];

    function actualizarMedida() {
        const anchoPx = puerta.width * grupo.scaleX;
        const metros = (anchoPx / factorConversion).toFixed(2) + ' m';
        textoMedida.text = metros;

        const center = grupo.getCenterPoint();
        let angle = grupo.angle % 360;
        if (angle < 0) angle += 360;

        const offset = 30;
        let textX = center.x, textY = center.y;

        if (angle >= 0 && angle < 45 || angle >= 315 && angle < 360) {
            textY = center.y - (puerta.height * grupo.scaleY) / 2 - offset;
        } else if (angle >= 45 && angle < 135) {
            textX = center.x + (puerta.height * grupo.scaleY) / 2 + offset;
        } else if (angle >= 135 && angle < 225) {
            textY = center.y + (puerta.height * grupo.scaleY) / 2 + offset;
        } else if (angle >= 225 && angle < 315) {
            textX = center.x - (puerta.height * grupo.scaleY) / 2 - offset;
        }

        textoMedida.left = textX;
        textoMedida.top = textY;
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

// ======================= FUNCIONES DE BORRADO =======================

// Borra el objeto seleccionado o todo el canvas si no hay selección
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
        // Confirmación antes de borrar todo el plano
        if (confirm("No hay ningún objeto seleccionado. ¿Quieres borrar TODO el plano?")) {
            canvas.getObjects().forEach(obj => {
                // Borra todo excepto la escala gráfica y textos fijos
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

// ======================= HANDLERS DE SELECCIÓN MÚLTIPLE =======================

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

// ======================= FUNCIONES DE MEDIDAS =======================

// Alterna la visibilidad de los textos de medida
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

// ======================= RESTRICCIONES DE ZONA BLOQUEADA =======================

// Restringe que ningún objeto cruce la zona bloqueada superior
function restringirZonaBloqueada(obj) {
    // No restringir la escala ni la regla gráfica
    if (obj.text && obj.text.startsWith('Escala:')) return;
    if (isEscalaGrafica(obj)) return;
    if (isLineaBloqueo(obj)) return;

    // Bounding box real (con rotación y escala)
    obj.setCoords();
    const boundingRect = obj.getBoundingRect(true, true);

    // Si el borde superior sube de la zona bloqueada, reajusta el top
    if (boundingRect.top < zonaBloqueadaAltura) {
        const delta = zonaBloqueadaAltura - boundingRect.top;
        obj.top += delta;
        obj.setCoords();
    }
}

// ======================= EVENTOS DEL CANVAS =======================

// Guías de alineación al mover
canvas.on('object:moving', function (e) {
    const movingObj = e.target;
    const a = movingObj.getBoundingRect();

    // Eliminar guías anteriores
    if (guiaX) canvas.remove(guiaX);
    if (guiaY) canvas.remove(guiaY);
    guiaX = guiaY = null;

    let snappedX = false, snappedY = false;
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
    restringirZonaBloqueada(movingObj);
});

// Restringe al mover, escalar y rotar
canvas.on('object:moving', e => restringirZonaBloqueada(e.target));
canvas.on('object:scaling', e => restringirZonaBloqueada(e.target));
canvas.on('object:rotating', e => restringirZonaBloqueada(e.target));

// Limpia guías tras modificar
canvas.on('object:modified', function () {
    if (guiaX) { canvas.remove(guiaX); guiaX = null; }
    if (guiaY) { canvas.remove(guiaY); guiaY = null; }
    canvas.renderAll();
});

// Mantiene el texto de medida con escala inversa al escalar pared/puerta
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

// ======================= EXPORTAR E IMPORTAR =======================

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
                // Elimina y recrea elementos fijos
                canvas.getObjects('text').forEach(obj => {
                    if (obj.text && obj.text.startsWith('Escala:')) canvas.remove(obj);
                });
                canvas.getObjects('group').forEach(obj => {
                    if (isEscalaGrafica(obj)) canvas.remove(obj);
                });
                canvas.getObjects('line').forEach(obj => {
                    if (isLineaBloqueo(obj)) canvas.remove(obj);
                });
                crearTextoEscala();
                crearEscalaGrafica();
                crearLineaBloqueo();

                // Reaplica handlers personalizados a cada objeto
                canvas.getObjects().forEach(obj => {
                    if (obj.type === 'group' && (obj._objects?.[0]?.fill === '#fhfhfh' || obj._objects?.[0]?.fill === '#9c9c9c')) {
                        setGroupHandlers(obj);
                    }
                    if (obj.type === 'image') {
                        setImageHandlers(obj);
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

// ======================= INICIALIZACIÓN =======================

// Crea los elementos fijos al cargar
crearTextoEscala();
crearEscalaGrafica();
crearLineaBloqueo();

// Ajusta el canvas al tamaño de la ventana
canvas.setWidth(window.innerWidth - 400); // 400px del sidebar
canvas.setHeight(window.innerHeight - 230);
window.addEventListener('resize', () => {
    canvas.setWidth(window.innerWidth - 400);
    canvas.setHeight(window.innerHeight - 230);
});

// Atajo para borrar con la tecla Delete
document.addEventListener('keydown', function (event) {
    if (event.key === 'Delete') borrarObjeto();
});

// Mostrar/Ocultar medidas
document.getElementById('toggle-measures').addEventListener('click', toggleMedidas);

// Zoom con la rueda del mouse sobre el canvas
canvas.on('mouse:wheel', (event) => {
    const delta = event.e.deltaY;
    const zoom = canvas.getZoom();
    const zoomFactor = 0.1;
    let newZoom = zoom + (delta < 0 ? zoomFactor : -zoomFactor);
    newZoom = Math.max(0.2, Math.min(3, newZoom));
    canvas.setZoom(newZoom);
    event.e.preventDefault();
    event.e.stopPropagation();
});