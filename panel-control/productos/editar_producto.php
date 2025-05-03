<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar categoria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <?php
    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    require('../../util/conexion.php');
    require('../../util/funciones/utilidades.php');

    define('IMG_PRODUCTOS', '/img/productos/');

    session_start();
    if (!isset($_SESSION["usuario"])) {
        header("location: ../../login/proveedores/iniciar_sesion_proveedor.php");
        exit;
    }
    ?>
    <style>
        .error {
            color: red;
        }

        body {
            background-color: #F7E5CB;
        }
    </style>
</head>

<body>
    <?php
    include('../layout/header.php');
    include('../layout/sidebar.php');

    define('PRODUCTOS', '/panel-control/productos/');

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
            $medidas = array('largo' => intval($largo_actual),'ancho' => intval($ancho_actual),'alto' => intval($alto_actual));
            $sql = "UPDATE productos SET medidas = '" . json_encode($medidas) . "' WHERE id_producto = '$id_producto'";
            $_conexion->query($sql);
        }

        if ($nuevo_nombre_imagen == "") {
            $err_foto_proveedor = "La img es obligatoria";
        } else {
            if (strlen($nuevo_nombre_imagen) > 60) {
                $err_foto_proveedor = "La ruta de la img no puede tener mas de 60 caracteres";
            } else {
                move_uploaded_file($ubicacion_temporal, to: $ubicacion_final);
                $img_actual = $nuevo_nombre_imagen;
                $sql = "UPDATE productos SET img_producto = '$img_actual' WHERE id_producto = $id_producto";
                $_conexion->query($sql);
            }
        }
    }

    ?>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow rounded-4">
                    <div class="card-body p-5">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="text-center mb-3">
                                <img src="<?php echo IMG_PRODUCTOS . $img_actual ?>"
                                    style="height: 260px; width: 60%; object-fit: cover;" alt="logo"
                                    class="card-img-top img-fluid" />
                                <input type="file" disabled hidden name="nueva_imagen" id="nueva_imagen"
                                    class="form-control mb-4" accept="image/*" />
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre</label>
                                    <input class="form-control" type="text" name="nuevo_nombre"
                                        value="<?php echo $nombre_actual ?>">
                                    <?php if (isset($err_nombre))
                                        echo "<span class='error'>$err_nombre</span>"; ?>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Categorias</label>
                                    <select class="form-select" name="nueva_categoria">
                                        <option value="<?php echo $categoria_actual ?>" selected>
                                            <?php echo $categoria_actual ?>
                                        </option>
                                        <?php
                                        foreach ($categorias as $categoria) { ?>
                                            <?php if ($categoria != $categoria_actual) { ?>
                                                <option value="<?php echo $categoria ?>">
                                                    <?php echo $categoria; ?>
                                                </option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                    <?php if (isset($err_categoria))
                                        echo "<span class='error'>$err_categoria</span>"; ?>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Precio</label>
                                    <input class="form-control" type="text" name="nuevo_precio"
                                        value="<?php echo $precio_actual ?>">
                                    <?php if (isset($err_precio))
                                        echo "<span class='error'>$err_precio</span>"; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Stock</label>
                                    <input class="form-control" type="text" name="nuevo_stock"
                                        value="<?php echo $stock_actual ?>">
                                    <?php if (isset($err_stock))
                                        echo "<span class='error'>$err_stock</span>"; ?>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Largo (cm)</label>
                                    <input class="form-control" type="number" min="0" name="largo"
                                        value="<?php echo $medidas['largo'] ?>">
                                    <?php if (isset($err_largo))
                                        echo "<span class='error'>$err_largo</span>"; ?>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Ancho (cm)</label>
                                    <input class="form-control" type="number" min="0" name="ancho"
                                        value="<?php echo $medidas['ancho'] ?>">
                                    <?php if (isset($err_ancho))
                                        echo "<span class='error'>$err_ancho</span>"; ?>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Alto (cm)</label>
                                    <input class="form-control" type="number" min="0" name="alto"
                                        value="<?php echo $medidas['alto'] ?>">
                                    <?php if (isset($err_alto))
                                        echo "<span class='error'>$err_alto</span>"; ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descripcion</label>
                                <textarea class="form-control"
                                    name="nueva_descripcion"><?php echo $descripcion_actual ?></textarea>
                                <?php if (isset($err_descripcion))
                                    echo "<span class='error'>$err_descripcion</span>"; ?>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Oferta</label>
                                    <select class="form-select" name="oferta">
                                        <option selected value="null">No tiene oferta</option>
                                        <?php
                                        foreach ($ofertas as $oferta) { ?>
                                            <option value="<?php echo $oferta['id_oferta']; ?>">
                                                <?php echo $oferta['nombre']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <?php if (isset($err_oferta))
                                        echo "<span class='error'>$err_oferta</span>"; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Imagen</label>
                                    <input class="form-control" type="file" name="img_producto" id="img_producto"
                                        accept="image/*" />
                                    <?php if (isset($err_imagen))
                                        echo "<span class='error'>$err_imagen</span>"; ?>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <input type="hidden" name="id_producto" value="<?php echo $id_producto ?>">
                                <a href="./index.php" class="btn btn-outline-secondary">Salir</a>
                                <button type="submit" class="btn btn-success">Confirmar cambio</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>