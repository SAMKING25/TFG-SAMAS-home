<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <?php
        error_reporting( E_ALL );
        ini_set("display_errors", 1 );  

        require "../util/conexion.php";
        require "../util/funciones.php";

        session_start();
        if (isset($_SESSION["usuario"])) {
            echo "<h4>Bienvenid@" . $_SESSION["usuario"] . "</h4>";
        } else {
            header("location: ../usuario/iniciar_sesion.php");
            exit;
        }
    ?>
    <style>
        .error {
            color: red
        }
    </style>
</head>
<body>
<?php
        if (isset($_SESSION["usuario"])) { ?>
            <a class="btn btn-warning" href="../usuario/cerrar_sesion.php">Cerrar sesión</a>
            <a class="btn btn-warning" href="../usuario/cambiar_credenciales.php?usuario=<?php echo $_SESSION["usuario"];?>">Modificar contraseña</a>
        <?php } ?>
    <div class="container">
        <h1>Nuevo producto</h1>    
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $tmp_nombre = depurar($_POST["nombre"]);
                $tmp_precio = depurar($_POST["precio"]);
                if (!isset($_POST["categoria"])) {
                    $tmp_categoria = "";
                } else {
                    $tmp_categoria = depurar($_POST["categoria"]);
                }
                $tmp_stock = depurar($_POST["stock"]);
                $tmp_nombre_imagen = depurar($_FILES["imagen"]["name"]);
                $tmp_descripcion = depurar($_POST["descripcion"]);

                //VALIDAR NOMBRE
                if ($tmp_nombre == "") {
                    $err_nombre = "El nombre es obligatorio";
                } else {
                    if (strlen($tmp_nombre) < 2 || strlen($tmp_nombre) > 50) {
                        $err_nombre = "El nombre debe tener de 2 a 50 caracteres";
                    } else {
                        $patron = "/^[a-zA-Z0-9 ]*$/";
                        if (!preg_match($patron, $tmp_nombre)) {
                            $err_nombre = "El nombre solo puede tener letras, espacios en blanco o números";
                        } else {
                            $nombre = ucwords(strtolower($tmp_nombre));
                        }
                    }
                }

                //VALIDAR PRECIO
                if ($tmp_precio == "") {
                    $err_precio = "El precio es obligatorio";
                } else {
                    $patron = "/^[0-9]{1,4}(\.[0-9]{1,2})?$/";
                    if (!preg_match($patron, $tmp_precio)) {
                        $err_precio = "El precio solo puede contener hasta 4 números con 2 decimales";
                    } else {
                        $precio = $tmp_precio;
                    }
                }

                $sql = "SELECT * FROM categorias ORDER BY categoria";
                $resultado = $_conexion -> query($sql);
                $categorias = [];

                while($fila = $resultado -> fetch_assoc()) {
                    array_push($categorias, $fila["categoria"]);
                }

                //VALIDAR CATEGORÍA
                if ($tmp_categoria == "" || $tmp_categoria == "--- Elige la categoría ---") {
                    $err_categoria = "La categoría es obligatoria";
                } else {
                    if (strlen($tmp_categoria) > 30) {
                        $err_categoria = "La categoría debe tener máximo 30 caracteres";
                    } else {
                        // Cambiar a categorias interactivas
                        if (!in_array($tmp_categoria, $categorias)) {
                            $err_categoria = "La categoria $tmp_categoria no existe";  
                        } else {
                            $categoria = ucwords(strtolower($tmp_categoria));
                        }
                    }
                }

                //VALIDAR STOCK
                if ($tmp_stock == "") {
                    $stock = 0;
                } else {
                    $patron = "/^[0-9]*$/";
                    if (!preg_match($patron, $tmp_stock)) {
                        $err_stock = "Solo puedes introducir números";
                    } else {
                        if ($tmp_stock < 0 || $tmp_stock > 2147483647) {
                            $err_stock = "El stock debe ser un número positivo de máximo 2.147.483.647";
                        } else {
                            $stock = $tmp_stock;
                        }
                    }
                }

                //VALIDAR NOMBRE IMAGEN
                if ($tmp_nombre_imagen == "") {
                    $err_nombre_imagen = "La imagen es obligatoria";
                } else {
                    if (strlen($tmp_nombre_imagen) > 60) {
                        $err_nombre_imagen = "El nombre de la imagen debe tener máximo 60 caracteres";
                    } else {
                        $nombre_imagen = $tmp_nombre_imagen;
                    }
                }

                //VALIDAR DESCRIPCION
                if ($tmp_descripcion == "") {
                    $err_descripcion = "La descripción es obligatoria";
                } else {
                    if (strlen($tmp_descripcion) > 255) {
                        $err_descripcion = "La descripción debe tener máximo 255 caracteres";
                    } else {
                        $descripcion = $tmp_descripcion;
                    }
                }

                if (isset($nombre) && isset($precio) && isset($categoria) && isset($stock) && isset($nombre_imagen) && isset($descripcion)) {
                    //ALMACENAMOS LA IMAGEN
                    $ubicacion_temporal = $_FILES["imagen"]["tmp_name"];
                    $ubicacion_final = "../imagenes/$nombre_imagen";

                    move_uploaded_file($ubicacion_temporal, $ubicacion_final);

                    /* $sql = "INSERT INTO productos (nombre, precio, categoria, stock, imagen, descripcion)
                        VALUES ('$nombre', $precio, '$categoria', $stock, '$nombre_imagen', '$descripcion')";
                    $_conexion -> query($sql); */

                    $sql = $_conexion -> prepare("INSERT INTO productos (nombre, precio, categoria, stock, imagen, descripcion)
                        VALUES (?,?,?,?,?,?)");

                    $sql -> bind_param("sisiss",
                        $nombre,
                        $precio,
                        $categoria,
                        $stock,
                        $nombre_imagen,
                        $descripcion
                    );

                    $sql -> execute();
                }
            }
            $sql = "SELECT * FROM categorias ORDER BY categoria";
            $resultado = $_conexion -> query($sql);
            $categorias = [];

            $_conexion -> close();

            while($fila = $resultado -> fetch_assoc()) {
                array_push($categorias, $fila["categoria"]);
            }  
        ?>
        <form class="col-4" action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input class="form-control" type="text" name="nombre">
                <?php if(isset($err_nombre)) echo "<span class='error'>$err_nombre</span>"; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Precio</label>
                <input class="form-control" type="text" name="precio">
                <?php if(isset($err_precio)) echo "<span class='error'>$err_precio</span>"; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Categoría</label>
                <select name="categoria" class="form-select">
                    <option value="" selected hidden>--- Elige la categoría ---</option>
                    <?php
                    foreach($categorias as $categoria) { ?>
                        <option value="<?php echo $categoria ?>">
                            <?php echo $categoria; ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if(isset($err_categoria)) echo "<span class='error'>$err_categoria</span>"; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Stock</label>
                <input class="form-control" type="text" name="stock">
                <?php if(isset($err_stock)) echo "<span class='error'>$err_stock</span>"; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Imagen</label>
                <input class="form-control" type="file" name="imagen">
                <?php if(isset($err_nombre_imagen)) echo "<span class='error'>$err_nombre_imagen</span>"; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea class="form-control" name="descripcion" id="descripcion"></textarea>
                <?php if(isset($err_descripcion)) echo "<span class='error'>$err_descripcion</span>"; ?>
            </div>
            <div class="mb-3">
                <input class="btn btn-primary" type="submit" value="Insertar">
                <a href="index.php" class="btn btn-secondary">Volver</a>
                <a href="../index.php" class="btn btn-dark">Inicio</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>