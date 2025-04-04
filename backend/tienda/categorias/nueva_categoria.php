<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva categoria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <?php
        error_reporting( E_ALL );
        ini_set("display_errors", 1 ); 
        require('../util/conexion.php'); 
        require('../util/utilidades.php'); 

        // session_start();
        // if (!isset($_SESSION["usuario"])) { 
        //     header("location: ../usuario/iniciar_sesion.php");
        //     exit;
        // }
    ?>
    <style>
        .error{
            color: red;
        }
    </style>
</head>
<body>
    <?php 
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $tmp_categoria = depurar($_POST["categoria"]);

            $sql = $_conexion -> prepare("SELECT * FROM categorias WHERE categoria =?");
            
            $sql -> bind_param("s",$tmp_categoria);
            
            $sql -> execute();
            $resultado = $sql -> get_result();

            if($resultado -> num_rows == 1){
                $err_categoria = "La categoria $tmp_categoria ya existe";
            }else{
                if($tmp_categoria == ''){
                    $err_categoria = "La categoría es obligatoria";
                } else {
                    if(strlen($tmp_categoria) > 30 || strlen($tmp_categoria) < 2){
                        $err_categoria = "La categoria no puede tener mas de 30 caracteres ni menos de 2";
                    } else{
                        $categoria = $tmp_categoria;
                    }
                }
            }

            if (isset($categoria)){
                $sql = $_conexion -> prepare("INSERT INTO categorias (categoria)
                    VALUES (?)");
                
                $sql -> bind_param("s",$categoria);
                
                $sql -> execute();
            }
            
        }
    ?>

    <div class="container">
        <h1>Nueva Categoría</h1> 
        <form class="col-4" action="" method="post">
            <div class="mb-3">
                <label class="form-label">Categoria</label>
                <input type="text" class="form-control" name="categoria">
                <?php if(isset($err_categoria)) echo "<span class='error'>$err_categoria</span>"; ?>
            </div>
            <div class="mb-3">
                <input type="submit" class="btn btn-primary" value="Insertar catergoria">
                <a href="index.php" class="btn btn-secondary">Volver</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>