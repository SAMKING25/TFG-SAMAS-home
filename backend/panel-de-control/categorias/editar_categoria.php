<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar categoría</title>
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
        <h1>Editar categoría</h1>
        <?php
            $categoria = $_GET["categoria"];
            /* $sql = "SELECT * FROM categorias WHERE categoria = '$categoria'";
            $resultado = $_conexion -> query($sql); */

            $sql = $_conexion -> prepare("SELECT * FROM categorias WHERE categoria = ?");

            $sql -> bind_param("s", $categoria);

            $sql -> execute();

            $resultado = $sql -> get_result();

            while($fila = $resultado -> fetch_assoc()) {
                $categoria = $fila["categoria"];
                $descripcion = $fila["descripcion"];
            }

            $sql = "SELECT * FROM categorias ORDER BY categoria";
            $resultado = $_conexion -> query($sql);

            $categorias = [];

            while($fila = $resultado -> fetch_assoc()) {
                array_push($categorias, $fila["categoria"]);
            }

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $tmp_categoria = depurar($_POST["categoria"]);
                $tmp_descripcion = depurar($_POST["descripcion"]);

                // VALIDAR CATEGORÍA
                if ($tmp_categoria == "") {
                    $err_categoria = "La categoría es obligatoria";
                } else {
                    if (strlen($tmp_categoria) > 30) {
                        $err_categoria = "La categoría debe tener máximo 30 caracteres";
                    } else {
                        $categoria = ucwords(strtolower($tmp_categoria));
                    }
                }

                //VALIDAR DESCRIPCIÓN
                if ($tmp_descripcion == "") {
                    $err_descripcion = "La descripción es obligatoria";
                } else {
                    if (strlen($tmp_descripcion) > 255) {
                        $err_descripcion = "La descripción debe tener máximo 255 caracteres";
                    } else {
                        $descripcion = $tmp_descripcion;
                    }
                }

                if (isset($categoria) && isset($descripcion)) {
                    /* $sql = "UPDATE categorias SET
                    descripcion = '$descripcion'
                    WHERE categoria = '$categoria'
                    ";
                    $_conexion -> query($sql); */

                    $sql = $_conexion -> prepare($sql = "UPDATE categorias SET
                        descripcion = ?,
                        WHERE categoria = ?
                    ");

                    $sql -> bind_param("ss",
                        $descripcion,
                        $categoria
                    );

                    $sql -> execute();

                    $_conexion -> close();
                }
            }

        ?>
        <form class="col-4" action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Categoría</label>
                <input class="form-control" type="text" name="categoria" disabled value="<?php echo $categoria; ?>">
                <?php if(isset($err_categoria)) echo "<span class='error'>$err_categoria</span>"; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea class="form-control" name="descripcion" id="descripcion"><?php echo $descripcion; ?></textarea>
                <?php if(isset($err_descripcion)) echo "<span class='error'>$err_descripcion</span>"; ?>
            </div>
            <div class="mb-3">
                <input type="hidden" name="categoria" value="<?php echo $categoria?>">
                <input class="btn btn-primary" type="submit" value="Confirmar">
                <a href="index.php" class="btn btn-secondary">Volver</a>
                <a href="../index.php" class="btn btn-dark">Inicio</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>