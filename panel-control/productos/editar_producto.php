<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require('../../util/conexion.php');
require('../../util/funciones/utilidades.php');
define('IMG_PRODUCTOS', '/img/productos/');

session_start();
if (!isset($_SESSION["proveedor"])) {
    header("location: ../../login/proveedor/iniciar_sesion_proveedor");
    exit;
}

// --- TODA LA LÓGICA DE CONSULTA Y PROCESAMIENTO DEL FORMULARIO AQUÍ ---
// Incluye la lógica de edición, validaciones, updates, y el header("Location: ...")

$id_producto = $_GET["id_producto"];
$sql = "SELECT * FROM productos WHERE id_producto = '$id_producto'";
$resultado = $_conexion->query($sql);

while ($datos_actuales = $resultado->fetch_assoc()) {
    $nombre_actual = $datos_actuales["nombre"];
    $precio_actual = $datos_actuales["precio"];
    $categoria_actual = $datos_actuales["categoria"];
    $stock_actual = $datos_actuales["stock"];
    $descripcion_actual = $datos_actuales["descripcion"];
    $medidas = json_decode($datos_actuales["medidas"], true);
    $img_actual = $datos_actuales["img_producto"];
    $oferta_actual = $datos_actuales["id_oferta"];
}

$sql = "SELECT * FROM categorias ORDER BY nombre_categoria";
$resultado = $_conexion->query($sql);
$categorias = [];

while ($fila = $resultado->fetch_assoc()) {
    array_push($categorias, $fila["nombre_categoria"]);
}

$sql = "SELECT id_oferta, nombre FROM ofertas ORDER BY id_oferta";
$resultado = $_conexion->query($sql);
$ofertas = [];

while ($fila = $resultado->fetch_assoc()) {
    $ofertas[] = $fila;  // Guarda todo el array con id_oferta y nombre
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nuevo_nombre = depurar($_POST["nuevo_nombre"]);
    $nuevo_precio = depurar($_POST["nuevo_precio"]);
    if (isset($_POST["nueva_categoria"]))
        $nueva_categoria = depurar($_POST["nueva_categoria"]);
    else
        $nueva_categoria = "";
    $nuevo_stock = depurar($_POST["nuevo_stock"]);
    $nueva_descripcion = depurar($_POST["nueva_descripcion"]);
    $nuevo_largo = depurar($_POST["largo"]);
    $nuevo_ancho = depurar($_POST["ancho"]);
    $nuevo_alto = depurar($_POST["alto"]);
    $nueva_oferta = depurar($_POST["oferta"]);

    $nuevo_nombre_imagen = $_FILES["img_producto"]["name"];
    $ubicacion_temporal = $_FILES["img_producto"]["tmp_name"];
    $ubicacion_final = "../../img/productos/$nuevo_nombre_imagen";

    if ($nuevo_nombre == '') {
        $err_nombre = "El nombre es obligatorio";
    } else {
        if (strlen($nuevo_nombre) > 50 || strlen($nuevo_nombre) < 3) {
            $err_nombre = "El nombre es de 50 caracteres maximo y 3 minimo";
        } else {
            $patron = "/^[0-9a-zA-Z áéíóúÁÉÍÓÚ]+$/";
            if (!preg_match($patron, $nuevo_nombre)) {
                $err_nombre = "El nombre solo puede tener letras, numeros y espacios";
            } else {
                // Modifica el nombre
                $sql = "UPDATE productos SET nombre = '$nuevo_nombre' WHERE id_producto = '$id_producto'";
                $_conexion->query($sql);
                $nombre_actual = $nuevo_nombre;
            }
        }
    }

    if ($nuevo_precio == '') {
        $err_precio = "El precio es obligatorio";
    } else {
        if (!filter_var($nuevo_precio, FILTER_VALIDATE_FLOAT)) {
            $err_precio = "El precio tiene que ser un numero";
        } else {
            $patron = "/^[0-9]{1,4}(\.[0-9]{1,2})?$/";
            if (!preg_match($patron, $nuevo_precio)) {
                $err_precio = "El precio solo puede tener 6 de los cuales 2 decimales";
            } else {
                // Modifica el precio
                $sql = "UPDATE productos SET precio = '$nuevo_precio' WHERE id_producto = '$id_producto'";
                $_conexion->query($sql);
                $precio_actual = $nuevo_precio;
            }
        }
    }

    if ($nueva_categoria == '') {
        $err_categoria = "La categoria es obligatoria";
    } else {
        if (strlen($nueva_categoria) > 30) {
            $err_categoria = "La categoria no puede tener mas de 30 caracteres";
        } else {
            // Modifica la categoria
            $sql = "UPDATE productos SET categoria = '$nueva_categoria' WHERE id_producto = '$id_producto'";
            $_conexion->query($sql);
            $categoria_actual = $nueva_categoria;
        }
    }

    if ($nuevo_stock == '' || $nuevo_stock == 0) {
        $stock_actual = 0;
    } else {
        if (!filter_var($nuevo_stock, FILTER_VALIDATE_INT)) {
            $err_stock = "El stock tiene que ser un numero entero";
        } else {
            if ($nuevo_stock < 0 || $nuevo_stock > 2147483647) {
                $err_stock = "El stock tiene que ser como maximo 2147483647";
            } else {
                // Modifica el stock
                $sql = "UPDATE productos SET stock = $nuevo_stock WHERE id_producto = '$id_producto'";
                $_conexion->query($sql);
                $stock_actual = $nuevo_stock;
            }
        }
    }

    if ($nueva_descripcion == "") {
        $err_descripcion = "La descripcion es obligatoria";
    } else {
        if (strlen($nueva_descripcion) > 255) {
            $err_descripcion = "La descripcion no puede tener mas de 255 caracteres";
        } else {
            // Modifica la descripcion
            $sql = "UPDATE productos SET descripcion = '$nueva_descripcion' WHERE id_producto = '$id_producto'";
            $_conexion->query($sql);
            $descripcion_actual = $nueva_descripcion;
        }
    }

    if ($nuevo_largo == '') {
        $err_largo = "El largo es obligatorio";
    } else {
        if (!filter_var($nuevo_largo, FILTER_VALIDATE_INT)) {
            $err_largo = "El largo tiene que ser un numero entero en centimetros";
        } else {
            if ($nuevo_largo < 0 || $nuevo_largo > 2147483647) {
                $nuevo_largo = "El largo tiene que ser como maximo 2147483647";
            } else {
                $largo_actual = $nuevo_largo;
            }
        }
    }

    if ($nuevo_ancho == '') {
        $err_ancho = "El ancho es obligatorio";
    } else {
        if (!filter_var($nuevo_ancho, FILTER_VALIDATE_INT)) {
            $err_ancho = "El ancho tiene que ser un numero entero en centimetros";
        } else {
            if ($nuevo_ancho < 0 || $nuevo_ancho > 2147483647) {
                $err_ancho = "El ancho tiene que ser como maximo 2147483647";
            } else {
                $ancho_actual = $nuevo_ancho;
            }
        }
    }

    if ($nuevo_alto == '') {
        $err_alto = "El alto es obligatorio";
    } else {
        if (!filter_var($nuevo_alto, FILTER_VALIDATE_INT)) {
            $err_alto = "El alto tiene que ser un numero entero en centimetros";
        } else {
            if ($nuevo_alto < 0 || $nuevo_alto > 2147483647) {
                $err_alto = "El alto tiene que ser como maximo 2147483647";
            } else {
                $alto_actual = $nuevo_alto;
            }
        }
    }

    if (isset($largo_actual) && isset($ancho_actual) && isset($alto_actual)) {
        $medidas = array('largo' => intval($largo_actual), 'ancho' => intval($ancho_actual), 'alto' => intval($alto_actual));
        $sql = "UPDATE productos SET medidas = '" . json_encode($medidas) . "' WHERE id_producto = '$id_producto'";
        $_conexion->query($sql);
    }

    if ($nuevo_nombre_imagen == "") {
        // No se subió una nueva imagen, NO marcar error, solo mantener la actual
        // $img_actual ya tiene el valor correcto
    } else {
        if (strlen($nuevo_nombre_imagen) > 60) {
            $err_foto_proveedor = "La ruta de la img no puede tener mas de 60 caracteres";
        } else {
            move_uploaded_file($ubicacion_temporal, $ubicacion_final);
            $img_actual = $nuevo_nombre_imagen;
            $sql = "UPDATE productos SET img_producto = '$img_actual' WHERE id_producto = $id_producto";
            $_conexion->query($sql);
        }
    }

    if ($nueva_oferta === "" || $nueva_oferta === null) {
        // Sin oferta seleccionada, guarda NULL en la base de datos
        $sql = "UPDATE productos SET id_oferta = NULL WHERE id_producto = $id_producto";
        $_conexion->query($sql);
        $oferta_actual = null;
    } else {
        $sql = "UPDATE productos SET id_oferta = $nueva_oferta WHERE id_producto = $id_producto";
        $_conexion->query($sql);
        $oferta_actual = $nueva_oferta;
    }

    // Al final de todas las validaciones y actualizaciones:
    if (
        !isset($err_nombre) && !isset($err_precio) && !isset($err_categoria) &&
        !isset($err_stock) && !isset($err_descripcion) &&
        !isset($err_largo) && !isset($err_ancho) && !isset($err_alto) &&
        !isset($err_foto_proveedor) && !isset($err_oferta)
    ) {
        header("Location: /panel-control/productos/?editado=ok");
        exit;
    }
}

// SOLO DESPUÉS DE TODO LO ANTERIOR, INCLUYE EL HEADER Y EL HTML:
include('../layout/header.php');
include('../layout/sidebar.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar categoria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link id="favicon" rel="shortcut icon" href="/img/logos/loguito_gris.png" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f7e5cb 0%, #f3f0e5 100%);
            min-height: 100vh;
        }

        .card-modern {
            border-radius: 18px;
            box-shadow: 0 6px 32px 0 rgba(80, 60, 30, 0.10);
            border: none;
            background: #fff;
        }

        .form-label {
            font-weight: 600;
            color: #7a5c2e;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1.5px solid #e0c9a6;
            background: #fdfaf6;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #bfa16b;
            box-shadow: 0 0 0 0.15rem #e7d3b1;
        }

        .input-group-text {
            background: #f7e5cb;
            border: none;
            color: #bfa16b;
        }

        .error {
            color: #c0392b;
            font-size: 0.95em;
            margin-top: 3px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .error::before {
            content: "\f071";
            font-family: "Bootstrap-icons";
            font-style: normal;
            font-weight: normal;
            font-size: 1.1em;
            margin-right: 3px;
            color: #c0392b;
        }

        .foto-producto-editable {
            position: relative;
            cursor: pointer;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid #e0c9a6;
            transition: box-shadow 0.2s;
            display: inline-block;
            /* Quita width y height fijos */
            background: #fdfaf6;
            padding: 8px;
            max-width: 100%;
            text-align: center;
        }

        .foto-producto-editable img {
            display: block;
            max-width: 100%;
            max-height: 300px;
            /* O el alto máximo que prefieras */
            margin: 0 auto;
            border-radius: 8px;
            object-fit: contain;
            /* Para que no deforme la imagen */
            background: #f7e5cb;
        }

        .foto-producto-editable:hover {
            box-shadow: 0 0 0 3px #e7d3b1;
        }

        .foto-producto-editable .edit-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(220, 180, 120, 0.75);
            color: #fff;
            text-align: center;
            padding: 8px 0;
            font-size: 1.05em;
            opacity: 0;
            transition: opacity 0.2s;
            pointer-events: none;
        }

        .foto-producto-editable:hover .edit-overlay {
            opacity: 1;
        }

        .btn-success {
            background: linear-gradient(90deg, #bfa16b 0%, #7a5c2e 100%);
            border: none;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .btn-outline-secondary {
            border-radius: 8px;
            border: 1.5px solid #bfa16b;
            color: #7a5c2e;
            background: #fff;
            font-weight: 500;
        }

        .btn-outline-secondary:hover {
            background: #f7e5cb;
            color: #bfa16b;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <div class="card card-modern shadow">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4" style="color:#7a5c2e;font-weight:700;">Editar producto</h2>
                        <form action="" method="post" enctype="multipart/form-data" autocomplete="off">
                            <div class="mb-4 text-center">
                                <div id="foto-producto-wrapper" class="foto-producto-editable mx-auto">
                                    <img id="foto-producto" src="<?php echo IMG_PRODUCTOS . $img_actual ?>"
                                        alt="Imagen producto" />
                                    <div class="edit-overlay">
                                        <i class="bi bi-camera"></i> Cambiar imagen
                                    </div>
                                </div>
                                <input type="file" name="img_producto" id="img_producto" class="form-control mt-2"
                                    accept="image/*" style="display: none;" />
                                <?php if (isset($err_imagen))
                                    echo "<span class='error'>$err_imagen</span>"; ?>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-7">
                                    <label class="form-label">Nombre</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                        <input class="form-control" type="text" name="nuevo_nombre" maxlength="50"
                                            value="<?php echo $nombre_actual ?>" required>
                                    </div>
                                    <?php if (isset($err_nombre))
                                        echo "<span class='error'>$err_nombre</span>"; ?>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Categoría</label>
                                    <select class="form-select" name="nueva_categoria" required>
                                        <option value="<?php echo $categoria_actual ?>" selected>
                                            <?php echo $categoria_actual ?>
                                        </option>
                                        <?php foreach ($categorias as $categoria) {
                                            if ($categoria != $categoria_actual) { ?>
                                                <option value="<?php echo $categoria ?>"><?php echo $categoria; ?></option>
                                        <?php }
                                        } ?>
                                    </select>
                                    <?php if (isset($err_categoria))
                                        echo "<span class='error'>$err_categoria</span>"; ?>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Precio</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                                        <input class="form-control" type="text" name="nuevo_precio" maxlength="7"
                                            value="<?php echo $precio_actual ?>" required>
                                    </div>
                                    <?php if (isset($err_precio))
                                        echo "<span class='error'>$err_precio</span>"; ?>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Stock</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-box-seam"></i></span>
                                        <input class="form-control" type="number" min="0" name="nuevo_stock"
                                            value="<?php echo $stock_actual ?>">
                                    </div>
                                    <?php if (isset($err_stock))
                                        echo "<span class='error'>$err_stock</span>"; ?>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Oferta</label>
                                    <select class="form-select" name="oferta">
                                        <option value="" <?php if ($oferta_actual === null) echo "selected"; ?>>Sin oferta
                                        </option>
                                        <?php foreach ($ofertas as $oferta) { ?>
                                            <option value="<?php echo $oferta['id_oferta']; ?>"
                                                <?php if ($oferta_actual == $oferta['id_oferta']) echo "selected"; ?>>
                                                <?php echo $oferta['nombre']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <?php if (isset($err_oferta))
                                        echo "<span class='error'>$err_oferta</span>"; ?>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Largo (cm)</label>
                                    <input class="form-control" type="number" min="0" name="largo"
                                        value="<?php echo $medidas['largo'] ?>" required>
                                    <?php if (isset($err_largo))
                                        echo "<span class='error'>$err_largo</span>"; ?>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Ancho (cm)</label>
                                    <input class="form-control" type="number" min="0" name="ancho"
                                        value="<?php echo $medidas['ancho'] ?>" required>
                                    <?php if (isset($err_ancho))
                                        echo "<span class='error'>$err_ancho</span>"; ?>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Alto (cm)</label>
                                    <input class="form-control" type="number" min="0" name="alto"
                                        value="<?php echo $medidas['alto'] ?>" required>
                                    <?php if (isset($err_alto))
                                        echo "<span class='error'>$err_alto</span>"; ?>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea class="form-control" name="nueva_descripcion" maxlength="255" rows="3"
                                    required><?php echo $descripcion_actual ?></textarea>
                                <?php if (isset($err_descripcion))
                                    echo "<span class='error'>$err_descripcion</span>"; ?>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="./" class="btn btn-outline-secondary px-4"><i class="bi bi-arrow-left"></i>
                                    Salir</a>
                                <button type="submit" class="btn btn-success px-4"><i class="bi bi-check-circle"></i>
                                    Guardar cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include('../../cookies.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fotoWrapper = document.getElementById('foto-producto-wrapper');
            const fotoProducto = document.getElementById('foto-producto');
            const inputFile = document.getElementById('img_producto');
            fotoWrapper.addEventListener('click', function() {
                inputFile.click();
            });
            inputFile.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        fotoProducto.src = ev.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
</body>

</html>