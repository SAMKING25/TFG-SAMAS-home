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

        require('./util/conexion.php');

        session_start();
        if (isset($_SESSION["usuario"])) {
            echo "<h4>Bienvenid@" . $_SESSION["usuario"] . "</h4>";
        } else {
            echo "<h4>Estás en modo invitado, inicia sesión para acceder a todas las funcionalidades</h4>";
            echo "<a class='btn btn-warning' href='./usuario/iniciar_sesion.php'>Iniciar sesión</a><br><br>";
        }
    ?>
</head>
<body>
    <?php
        if (isset($_SESSION["usuario"])) { ?>
            <a class="btn btn-warning" href="./usuario/cerrar_sesion.php">Cerrar sesión</a>
            <a class="btn btn-warning" href="./usuario/cambiar_credenciales.php?usuario=<?php echo $_SESSION["usuario"];?>">Modificar contraseña</a>
        <?php } ?>
    <div class="container">
        <h1>Tabla principal</h1>
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
        
        <?php
            if (isset($_SESSION["usuario"])) {
                echo '<a href="./categorias" class="btn btn-info">Ir a categorías</a><span> </span>';
                echo '<a href="./productos/" class="btn btn-info">Ir a productos</a><br><br>';
            }
        ?>
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Categoría</th>
                    <th>Stock</th>
                    <th>Imagen</th>
                    <th>Descripción</th>
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
                            <img width="100" height="200" src="<?php echo "./imagenes/".$fila["imagen"]?>" alt="Imagen del producto">
                        </td>
                <?php
                        echo "<td>" . $fila["descripcion"] . "</td>";
                        ?>
                            <form action="" method="post">
                                <input type="hidden" name="id_producto" value="<?php echo $fila["id_producto"] ?>">    
                            </form>
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