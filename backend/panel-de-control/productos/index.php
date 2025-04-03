<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index de Productos</title>
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
</head>
<body>
    <?php
        if (isset($_SESSION["usuario"])) { ?>
            <a class="btn btn-warning" href="../usuario/cerrar_sesion.php">Cerrar sesión</a>
            <a class="btn btn-warning" href="../usuario/cambiar_credenciales.php?usuario=<?php echo $_SESSION["usuario"];?>">Modificar contraseña</a>
        <?php } ?>
    <div class="container">
        <h1>Tabla de Productos</h1>
        <?php
            if($_SERVER["REQUEST_METHOD"] == "POST") {
                $id_producto = $_POST["id_producto"];

                /* $sql = "DELETE FROM productos WHERE id_producto = $id_producto";
                $_conexion -> query($sql); */

                $sql = $_conexion -> prepare("DELETE FROM productos WHERE id_producto = ?");

                $sql -> bind_param("i", $id_producto);

                $sql -> execute();
                

            }
            
            $sql = "SELECT * FROM productos";
            $resultado = $_conexion -> query($sql);

            $_conexion -> close();
        ?>
        <a href="../index.php" class="btn btn-dark">Inicio</a>
        <a href="../categorias" class="btn btn-info">Ir a categorías</a>
        <a href="nuevo_producto.php" class="btn btn-secondary">Crear nuevo producto</a><br><br>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Categoría</th>
                    <th>Stock</th>
                    <th>Imagen</th>
                    <th>Descripción</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    while($fila = $resultado -> fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $fila["nombre"] . "</td>";
                        echo "<td>" . $fila["precio"] . "</td>";
                        echo "<td>" . $fila["categoria"] . "</td>";
                        echo "<td>" . $fila["stock"] . "</td>";
                ?>
                        <td>
                            <img width="100" height="200" src="../imagenes/<?php echo $fila["imagen"]?>" alt="Imagen del producto">
                        </td>
                <?php
                        echo "<td>" . $fila["descripcion"] . "</td>";
                        ?>
                        <td>
                            <a class="btn btn-primary" href="editar_producto.php?id_producto=<?php echo $fila["id_producto"] ?>">Editar</a>
                        </td>
                        <td>
                            <form action="" method="post">
                                <input type="hidden" name="id_producto" value="<?php echo $fila["id_producto"] ?>">    
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