<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <?php
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
    require('../../util/conexion.php');
    require('../../util/funciones/utilidades.php');

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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $tmp_nombre = depurar($_POST["nombre"]);
        $tmp_precio = depurar($_POST["precio"]);
        if (isset($_POST["categoria"]))
            $tmp_categoria = depurar($_POST["categoria"]);
        else
            $tmp_categoria = "";
        $tmp_stock = depurar($_POST["stock"]);
        $tmp_descripcion = depurar($_POST["descripcion"]);
        $tmp_largo = depurar($_POST["largo"]);
        $tmp_ancho = depurar($_POST["ancho"]);
        $tmp_alto = depurar($_POST["alto"]);
        $medidas = array('largo' => intval($tmp_largo),'ancho' => intval($tmp_ancho),'alto' => intval($tmp_alto));
        $oferta = $_POST["oferta"];
        $id_proveedor = $_SESSION["usuario"];

        $nombre_imagen = $_FILES["img_producto"]["name"];
        $ubicacion_temporal = $_FILES["img_producto"]["tmp_name"];
        $ubicacion_final = "../../img/productos/$nombre_imagen";

        if ($tmp_nombre == '') {
            $err_nombre = "El nombre es obligatorio";
        } else {
            if (strlen($tmp_nombre) > 50 || strlen($tmp_nombre) < 3) {
                $err_nombre = "El nombre es de 50 caracteres maximo y 3 minimo";
            } else {
                $patron = "/^[0-9a-zA-Z áéíóúÁÉÍÓÚ]+$/";
                if (!preg_match($patron, $tmp_nombre)) {
                    $err_nombre = "El nombre solo puede tener letras, numeros y espacios";
                } else {
                    $nombre = $tmp_nombre;
                }
            }
        }

        if ($tmp_precio == '') {
            $err_precio = "El precio es obligatorio";
        } else {
            if (!filter_var($tmp_precio, FILTER_VALIDATE_FLOAT)) {
                $err_precio = "El precio tiene que ser un numero";
            } else {
                $patron = "/^[0-9]{1,4}(\.[0-9]{1,2})?$/";
                if (!preg_match($patron, $tmp_precio)) {
                    $err_precio = "El precio solo puede tener 6 numeros de los cuales 2 decimales";
                } else {
                    $precio = $tmp_precio;
                }
            }
        }

        if ($tmp_categoria == '') {
            $err_categoria = "La categoria es obligatoria";
        } else {
            if (strlen($tmp_categoria) > 30) {
                $err_categoria = "La categoria no puede tener mas de 30 caracteres";
            } else {
                $sql = "SELECT * FROM categorias ORDER BY nombre_categoria";
                $resultado = $_conexion->query($sql);
                $categorias = [];

                while ($fila = $resultado->fetch_assoc()) {
                    array_push($categorias, $fila["nombre_categoria"]);
                }

                if (!in_array($tmp_categoria, $categorias)) {
                    $err_categoria = "La categoria $tmp_categoria no existe";
                } else {
                    $categoria = $tmp_categoria;
                }
            }
        }

        if ($tmp_stock == '' || $tmp_stock == 0) {
            $stock = 0;
        } else {
            if (!filter_var($tmp_stock, FILTER_VALIDATE_INT)) {
                $err_stock = "El stock tiene que ser un numero entero";
            } else {
                if ($tmp_stock < 0 || $tmp_stock > 2147483647) {
                    $err_stock = "El stock tiene que ser como maximo 2147483647";
                } else {
                    $stock = $tmp_stock;
                }
            }
        }

        if ($tmp_descripcion == '') {
            $err_descripcion = "La descripcion es obligatoria";
        } else {
            if (strlen($tmp_descripcion) > 255) {
                $err_descripcion = "La descripcion no puede tener mas de 255 caracteres";
            } else {
                $descripcion = $tmp_descripcion;
            }
        }

        if ($tmp_largo == '') {
            $err_largo = "El largo es obligatorio";
        } else {
            if (!filter_var($tmp_largo, FILTER_VALIDATE_INT)) {
                $err_largo = "El largo tiene que ser un numero entero en centimetros";
            } else {
                if ($tmp_largo < 0 || $tmp_largo > 2147483647) {
                    $err_largo = "El largo tiene que ser como maximo 2147483647";
                } else {
                    $largo = $tmp_largo;
                }
            }
        }

        if ($tmp_ancho == '') {
            $err_ancho = "El ancho es obligatorio";
        } else {
            if (!filter_var($tmp_ancho, FILTER_VALIDATE_INT)) {
                $err_ancho = "El ancho tiene que ser un numero entero en centimetros";
            } else {
                if ($tmp_ancho < 0 || $tmp_ancho > 2147483647) {
                    $err_ancho = "El ancho tiene que ser como maximo 2147483647";
                } else {
                    $ancho = $tmp_ancho;
                }
            }
        }

        if ($tmp_alto == '') {
            $err_alto = "El alto es obligatorio";
        } else {
            if (!filter_var($tmp_alto, FILTER_VALIDATE_INT)) {
                $err_alto = "El alto tiene que ser un numero entero en centimetros";
            } else {
                if ($tmp_alto < 0 || $tmp_alto > 2147483647) {
                    $err_alto = "El alto tiene que ser como maximo 2147483647";
                } else {
                    $alto = $tmp_alto;
                }
            }
        }

        if ($nombre_imagen == "") {
            $err_imagen = "La imagen es obligatoria";
        } else {
            if (strlen($nombre_imagen) > 60) {
                $err_imagen = "La ruta de la imagen no puede tener mas de 60 caracteres";
            } else {
                move_uploaded_file($ubicacion_temporal, $ubicacion_final);
                $img_producto = $nombre_imagen;
            }
        }

        if (isset($nombre) && isset($precio) && isset($categoria) && isset($img_producto) && isset($descripcion) && isset($largo) && isset($ancho) && isset($alto) && isset($id_proveedor)) {
            // Inserta un nuevo producto
            $sql = "INSERT INTO productos (nombre, precio, categoria, stock, img_producto, descripcion, medidas, id_proveedor, id_oferta)
            VALUES ('$nombre', $precio, '$categoria', $stock, '$img_producto', '$descripcion', '" . json_encode($medidas) . "', $id_proveedor, $oferta)";
            $_conexion->query($sql);

            header("location: ./index.php");
            exit;
        }
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


    ?>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow rounded-4">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-3">Nuevo producto</h2>
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre</label>
                                    <input class="form-control" type="text" name="nombre">
                                    <?php if (isset($err_nombre))
                                        echo "<span class='error'>$err_nombre</span>"; ?>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Categorias</label>
                                    <select class="form-select" name="categoria">
                                    <option selected disabled hidden>--- Elige una categoria ---</option>
                                        <?php
                                        foreach ($categorias as $categoria) { ?>
                                            <option value="<?php echo $categoria ?>">
                                                <?php echo $categoria; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <?php if (isset($err_categoria))
                                        echo "<span class='error'>$err_categoria</span>"; ?>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Precio</label>
                                    <input class="form-control" type="text" name="precio">
                                    <?php if (isset($err_precio))
                                        echo "<span class='error'>$err_precio</span>"; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Stock</label>
                                    <input class="form-control" type="text" name="stock">
                                    <?php if (isset($err_stock))
                                        echo "<span class='error'>$err_stock</span>"; ?>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Largo (cm)</label>
                                    <input class="form-control" type="number" min="0" name="largo">
                                    <?php if (isset($err_largo))
                                        echo "<span class='error'>$err_largo</span>"; ?>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Ancho (cm)</label>
                                    <input class="form-control" type="number" min="0" name="ancho">
                                    <?php if (isset($err_ancho))
                                        echo "<span class='error'>$err_ancho</span>"; ?>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Alto (cm)</label>
                                    <input class="form-control" type="number" min="0" name="alto">
                                    <?php if (isset($err_alto))
                                        echo "<span class='error'>$err_alto</span>"; ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descripcion</label>
                                <textarea class="form-control" name="descripcion"></textarea>
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
                                            <option value="<?php echo $oferta['id_oferta'] ?>">
                                                <?php echo $oferta['nombre']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <?php if (isset($err_oferta))
                                        echo "<span class='error'>$err_oferta</span>"; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Imagen</label>
                                    <input class="form-control" type="file" name="img_producto">
                                    <?php if (isset($err_imagen))
                                        echo "<span class='error'>$err_imagen</span>"; ?>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <input type="hidden" name="id_producto">
                                <a href="./index.php" class="btn btn-outline-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-success">Confirmar cambio</button>
                            </div>
                        </form>
                    </div>
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