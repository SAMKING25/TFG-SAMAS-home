<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <?php
        error_reporting( E_ALL );
        ini_set("display_errors", 1 );    

        require('../util/conexion.php');
        require('../util/utilidades.php');
    ?>
    <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <?php
    if($_SERVER["REQUEST_METHOD"] == "POST"){
            $usuario = depurar($_POST["usuario"]);
            $contrasena = $_POST["contrasena"];

            $sql="SELECT * FROM usuarios WHERE usuario ='$usuario'";
            $resultado=$_conexion -> query($sql);

            if($resultado -> num_rows == 0){
                $err_usuario = "El usuario $usuario no existe";
            }else{
                $datos_usuario = $resultado -> fetch_assoc();
                $acceso_concedido = password_verify($contrasena,$datos_usuario["contrasena"]);

                if($acceso_concedido){
                    session_start();
                    $_SESSION["usuario"] = $usuario;
                    header("location: ../index.php");
                }else{
                    $err_contrasena = "La contraseña no es correcta";
                }
            }
        } 
    ?>
    
    <section class="vh-100">
    <div class="container-fluid h-custom">
        <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-md-9 col-lg-6 col-xl-5">
            <img src="../imagenes/loguito1.png"
            class="img-fluid" alt="Sample image">
        </div>
        <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
            <form>
            <!-- Email input -->
            <div data-mdb-input-init class="form-outline mb-4">
                <input type="email" id="form3Example3" class="form-control form-control-lg"
                placeholder="Introduce un email válido" />
                <label class="form-label" for="form3Example3">Email</label>
            </div>

            <!-- Password input -->
            <div data-mdb-input-init class="form-outline mb-3">
                <input type="password" id="form3Example4" class="form-control form-control-lg"
                placeholder="Introduce la contraseña" />
                <label class="form-label" for="form3Example4">Contraseña</label>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <!-- Checkbox -->
                <div class="form-check mb-0">
                <input class="form-check-input me-2" type="checkbox" value="" id="form2Example3" />
                <label class="form-check-label" for="form2Example3">
                    Recuérdame
                </label>
                </div>
                <a href="#!" class="text-body">He olvidado mi contraseña</a>
            </div>

            <div class="text-center text-lg-start mt-4 pt-2">
                <button  type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg"
                style="padding-left: 2.5rem; padding-right: 2.5rem;">Login</button>
                <a href="../index.php" class="btn btn-outline-success">Volver a inicio</a>
                <p class="small fw-bold mt-2 pt-1 mb-0">¿No tienes cuenta? <a href="#!"
                    class="link-danger">Registrarse</a></p>
            </div>

            </form>
        </div>
        </div>
    </div>

    <!-- ANTIGUO: -->
    <!-- <div class="container">
        <h1>Iniciar sesion</h1>
        <form class="col-6" action="" method="post" enctype="multipart/form-data">
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
                <input class="btn btn-primary" type="submit" value="Iniciar sesion">
            </div>
            <div class="mb-3">
                <p>¿Todavía no tienes cuenta?</p>
                <a class="btn btn-secondary" href="registro.php">Registrarse</a>
                <a href="../index.php" class="btn btn-outline-success">Volver a inicio</a>
            </div>
        </form>
    </div> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>