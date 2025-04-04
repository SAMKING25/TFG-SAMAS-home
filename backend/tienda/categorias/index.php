<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla Categoria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <?php
        error_reporting( E_ALL );
        ini_set("display_errors", 1 );  

        require('../util/conexion.php');

        /* session_start();
        if (isset($_SESSION["usuario"])) { ?>
            <h2>Bienvenid@ <?php echo $_SESSION["usuario"] ?> </h2>
            <a class="btn btn-warning" href="../usuario/cerrar_sesion.php">Cerrar sesion</a>
            <a class="btn btn-primary" href="../usuario/cambiar_credenciales.php?usuario=<?php echo $_SESSION["usuario"] ?>">Cambiar credenciales</a>
        <?php } else {
            header("location: ../usuario/iniciar_sesion.php");
            exit;
        } */
        ?>
        <style>
            .error {
                color: red;
            }
        </style>
</head>
<body>
    <?php
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $categoria = $_POST["categoria"];
            /* $sql="SELECT * FROM productos WHERE categoria ='$categoria'";
            $resultado=$_conexion -> query($sql); */

            // 1
            $sql = $_conexion -> prepare("SELECT * FROM productos WHERE categoria =?");
            // 2
            $sql -> bind_param("s",$categoria);
            // 3
            $sql -> execute();
            $resultado = $sql -> get_result();

            if($resultado -> num_rows >= 1){
                $err_borrar = "No puedes borrar la categoria sin borrar los objetos asociados";
            } else {
                $sql = "DELETE FROM categorias WHERE categoria = '$categoria'";
                $_conexion -> query($sql);
            }
            
        }

        $sql = "SELECT * FROM categorias";
        $resultado = $_conexion -> query($sql);
    ?>

    <div class="container">
        <h1>Tabla Categorías</h1>
        <?php if(isset($err_borrar)) echo "<h3 class='error'>$err_borrar</h3>"; ?>
        <div class="mb-3">
                <a href="nueva_categoria.php" class="btn btn-primary">Insertar Categoria</a>
                <a href="../productos/index.php" class="btn btn-success">Ir a tabla de productos</a>
        </div>
        <table class="table table-info table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Categoria</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    while($fila = $resultado -> fetch_assoc()){
                        echo "<tr>";
                        echo "<td>" . $fila["categoria"] ."</td>";
                        
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
                        </tr>
                    <?php } ?>
            </tbody>
        </table>
        <a href="../index.php" class="btn btn-outline-secondary">Volver a inicio</a>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>