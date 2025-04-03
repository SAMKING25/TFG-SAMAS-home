<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <?php
        error_reporting( E_ALL );
        ini_set("display_errors", 1 );  

        require ("../util/conexion.php");
    ?>
    <style>
        .error {
            color: red
        }
    </style>
</head>
<body>
    <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $usuario = $_POST["usuario"];
            $contrasena = $_POST["contrasena"];

            /* $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
            $resultado = $_conexion -> query($sql); */

            $sql = $_conexion -> prepare("SELECT * FROM usuarios WHERE usuario = ?");

            $sql -> bind_param("s", $usuario);

            $sql -> execute();

            $resultado = $sql -> get_result();

            $_conexion -> close();
            
            if ($resultado -> num_rows == 0) {
                $err_usuario = "El usuario $usuario no existe";
            } else {
                $datos_usuario = $resultado -> fetch_assoc();
                $acceso_concedido = password_verify($contrasena, $datos_usuario["contrasena"]);

                if ($acceso_concedido) {
                    session_start();
                    $_SESSION["usuario"] = $usuario;
                    header("location: ../index.php");
                    exit;
                } else {
                    $err_contrasena = "Contraseña incorrecta";
                }
            }
        }
    ?>

    <div class="container">
        <h1>Inicio de sesión</h1>    
        
        <form class="col-3" action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input class="form-control" type="text" name="usuario">
                <?php if(isset($err_usuario)) echo "<span class='error'>$err_usuario</span>"; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input class="form-control" type="password" name="contrasena">
                <?php if(isset($err_contrasena)) echo "<span class='error'>$err_contrasena</span>"; ?>
            </div>
            <div class="mb-3">
                <input class="btn btn-primary" type="submit" value="Iniciar sesión">
                <a href="../index.php" class="btn btn-dark">Inicio</a><br><br>
                <p>¿No tienes cuenta todavía?</p>
                <a href="registro.php" class="btn btn-secondary">Registrarse</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>