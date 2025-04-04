<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar categoria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <?php
        /* error_reporting( E_ALL );
        ini_set("display_errors", 1 );    

        require('../util/conexion.php');
        require('../util/utilidades.php');

        session_start();
        if (!isset($_SESSION["usuario"])) { 
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
    <div class="container">
        <h1>Editar categoria</h1>
        <?php
        $categoria = $_GET["categoria"];
        /* $sql = "SELECT * FROM categorias WHERE categoria = '$categoria'";
        $resultado = $_conexion -> query($sql); */

        // 1
        $sql = $_conexion -> prepare("SELECT * FROM categorias WHERE categoria =?");
        // 2
        $sql -> bind_param("s",$categoria);
        // 3
        $sql -> execute();
        $resultado = $sql -> get_result();

        while($fila = $resultado -> fetch_assoc()) {
            $descripcion = $fila["descripcion"];
        }

        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $nueva_descripcion = $_POST["nueva_descripcion"];

            if($nueva_descripcion == ""){
                $err_descripcion = "La descripcion es obligatoria";
            } else {
                if(strlen($nueva_descripcion) > 255){
                    $err_descripcion = "La descripcion no puede tener mas de 255 caracteres";
                } else{
                    // Modifica la descripcion
                    /* $sql = "UPDATE categorias SET descripcion = '$nueva_descripcion' WHERE descripcion = '$descripcion'";
                    $_conexion -> query($sql); */

                    // 1
                    $sql = $_conexion -> prepare("UPDATE categorias SET descripcion = ? WHERE descripcion = ?");
                    // 2
                    $sql -> bind_param("ss",$nueva_descripcion,$descripcion);
                    // 3
                    $sql -> execute();

                    $descripcion = $nueva_descripcion;
                }
            }
            
        }
        ?>
        <form class="col-6" action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Título</label>
                <input class="form-control" type="text" name="categoria" value="<?php echo $categoria ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripcion</label>
                <textarea class="form-control" name="nueva_descripcion"><?php echo $descripcion ?></textarea>
                <?php if(isset($err_descripcion)) echo "<span class='error'>$err_descripcion</span>"; ?>
            </div>
            <div class="mb-3">
                <input type="hidden" name="categoria" value="<?php echo $categoria ?>">
                <input class="btn btn-primary" type="submit" value="Confirmar">
                <a class="btn btn-secondary" href="index.php">Volver</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>