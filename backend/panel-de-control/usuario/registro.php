<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
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
            $tmp_usuario = $_POST["usuario"];
            $tmp_contrasena = $_POST["contrasena"];

            $sql = "SELECT * FROM usuarios ORDER BY usuario";
            $resultado = $_conexion -> query($sql);
            $usuarios = [];

            while($fila = $resultado -> fetch_assoc()) {
                array_push($usuarios, $fila["usuario"]);
            }

            //VALIDAR USUARIO
            if ($tmp_usuario == "") {
                $err_usuario = "El usuario es obligatorio";
            } else {
                $patron = "/^[a-zA-Z0-9]{3,15}$/";
                if (!preg_match($patron, $tmp_usuario)) {
                    $err_usuario = "El usuario debe contener de 3 a 15 caracteres";
                } else {
                    if (in_array($tmp_usuario, $usuarios)) {
                        $err_usuario = "El usuario introducido ya existe";
                    } else {
                        $usuario = $tmp_usuario;
                    }
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

                /* $sql = "INSERT INTO usuarios VALUES ('$usuario', '$contrasena_cifrada')";
                $_conexion -> query($sql); */

                $sql = $_conexion -> prepare("INSERT INTO usuarios VALUES (?,?)");

                $sql -> bind_param("ss",
                    $usuario,
                    $contrasena_cifrada
                );

                $sql -> execute();

                $_conexion -> close();

                header("location: iniciar_sesion.php");
                exit;
            }
        }
    ?>

    <div class="container">
        <h1>Registro</h1>    
        
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
                <input class="btn btn-primary" type="submit" value="Registrarse">
                <a href="../index.php" class="btn btn-dark">Inicio</a><br><br>
                <p>¿Ya tienes cuenta?</p>
                <a href="iniciar_sesion.php" class="btn btn-secondary">Iniciar sesión</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>