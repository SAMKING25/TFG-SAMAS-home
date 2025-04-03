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
    <a class="btn btn-warning" href="../usuario/cerrar_sesion.php">Cerrar sesión</a><br><br>
    <div class="container">
        <h1>Cambiar credenciales</h1>
        <?php
            $usuario = $_GET["usuario"];
            /* $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
            $resultado = $_conexion -> query($sql); */

            $sql = $_conexion -> prepare("SELECT * FROM usuarios WHERE usuario = ?");

            $sql -> bind_param("s", $usuario);

            $sql -> execute();

            $resultado = $sql -> get_result();

            while($fila = $resultado -> fetch_assoc()) {
                $usuario = $fila["usuario"];
                $contrasena = $fila["contrasena"];
            }

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $tmp_usuario = $_POST["usuario"];
                $tmp_contrasena = $_POST["contrasena"];

                //VALIDAR USUARIO
                if ($tmp_usuario == "") {
                    $err_usuario = "El usuario es obligatorio";
                } else {
                    $patron = "/^[a-zA-Z0-9]{3,15}$/";
                    if (!preg_match($patron, $tmp_usuario)) {
                        $err_usuario = "El usuario debe contener de 3 a 15 caracteres";
                    } else {
                        $usuario = $tmp_usuario;
                    }
                }

                //VALIDAR CONTRASEÑA
                if ($tmp_contrasena == "") {
                    $err_contrasena = "La contraseña es obligatoria";
                } else {
                    $patron = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,15}$/";
                    if (!preg_match($patron, $tmp_contrasena)) {
                        $err_contrasena = "La contraseña debe contener: De 8 a 15 dígitos y Al menos 1 mayúscula, 1 minúscula y 1 número";
                    } else {
                        $contrasena = $tmp_contrasena;
                    }
                }

                if (isset($usuario) && isset($contrasena)) {
                    $contrasena_cifrada = password_hash($contrasena, PASSWORD_DEFAULT);
                    /* $sql = "UPDATE usuarios SET
                    contrasena = '$contrasena_cifrada'
                    WHERE usuario = '$usuario'
                    ";
                    $_conexion -> query($sql); */

                    $sql = $_conexion -> prepare("UPDATE usuarios 
                        SET contrasena = ?
                        WHERE usuario = ?");

                    $sql -> bind_param("ss",
                        $contrasena_cifrada,
                        $usuario
                    );

                    $_conexion -> close();
                }
            }

        ?>
        <form class="col-4" action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input class="form-control" type="text" name="usuario" disabled value="<?php echo trim($usuario); ?>">
                <?php if(isset($err_usuario)) echo "<span class='error'>$err_usuario</span>"; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input class="form-control" type="password" name="contrasena">
                <?php if(isset($err_contrasena)) echo "<span class='error'>$err_contrasena</span>"; ?>
            </div>
            <div class="mb-3">
                <input type="hidden" name="usuario" value="<?php echo trim($usuario)?>">
                <input class="btn btn-primary" type="submit" value="Confirmar">
                <a href="../index.php" class="btn btn-dark">Inicio</a><br><br>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>