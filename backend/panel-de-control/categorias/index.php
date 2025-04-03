<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index de Categorías</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <?php
        error_reporting(E_ALL);
        ini_set("display_errors", 1);

        require('../util/conexion.php');

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
        <h1>Tabla de Categorías</h1>
        <?php
            if($_SERVER["REQUEST_METHOD"] == "POST") {
                $categoria = $_POST["categoria"];

                //VALIDAR ELIMINACIÓN
                $sql = "SELECT * FROM productos ORDER BY categoria";
                $resultado = $_conexion -> query($sql);

                $categorias_usuario = [];

                while($fila = $resultado -> fetch_assoc()) {
                    array_push($categorias_usuario, $fila["categoria"]);
                }

                if (in_array($categoria, $categorias_usuario)) {
                    $err_borrar = "Borrado no disponible, la categoria $categoria se está utilizando en la tabla Productos";
                } else {
                    /* $sql = "DELETE FROM categorias WHERE categoria = '$categoria'";
                    $_conexion -> query($sql); */

                    $sql = $_conexion -> prepare("DELETE FROM categorias WHERE categoria = ?");

                    $sql -> bind_param("s", $categoria);

                    $sql -> execute();

                }
            }
            
            $sql = "SELECT * FROM categorias";
            $resultado = $_conexion -> query($sql);

            $_conexion -> close();

        ?>
        <a href="../index.php" class="btn btn-dark">Inicio</a>
        <a href="../productos" class="btn btn-info">Ir a productos</a>
        <a href="nueva_categoria.php" class="btn btn-secondary">Crear nueva categoría</a><br><br>
        <?php if(isset($err_borrar)) echo "<span class='error'>$err_borrar</span>"; ?>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Categoría</th>
                    <th>Descripción</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    while($fila = $resultado -> fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $fila["categoria"] . "</td>";
                        echo "<td>" . $fila["descripcion"] . "</td>";
                        ?>
                        <td>
                            <a class="btn btn-primary" href="editar_categoria.php?categoria=<?php echo $fila["categoria"] ?>">Editar</a>
                        </td>
                        <td>
                            <form action="" method="post">
                                <input type="hidden" name="categoria" value="<?php echo $fila["categoria"] ?>">    
                                <input class="btn btn-danger" type="submit" value="Borrar">
                            </form>
                        </td>
                        <?php
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>