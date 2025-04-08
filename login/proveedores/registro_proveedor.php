<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <?php
    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    require('../../util/conexion.php');
    require('../../util/funciones/utilidades.php');
    ?>
    <style>
        .error {
            color: red;
        }

        .gradient-custom-2 {
            background: #fccb90;
            background: -webkit-linear-gradient(to right, rgb(163, 144, 130), rgb(146, 116, 71), rgb(165, 125, 49), rgb(102, 67, 20));
            background: linear-gradient(to right, rgb(163, 144, 130), rgb(146, 116, 71), rgb(165, 125, 49), rgb(102, 67, 20));

            border: 1px solid #F7E5CB;
        }

        .btn:hover {
            border: 1px solid black;
        }

        @media (min-width: 768px) {
            .gradient-form {
                height: 100vh !important;
            }
        }

        @media (min-width: 769px) {
            .gradient-custom-2 {
                border-top-right-radius: .3rem;
                border-bottom-right-radius: .3rem;
            }
        }
    </style>
</head>

<body>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $tmp_email_proveedor = depurar($_POST["email_proveedor"]);
        $tmp_nombre_proveedor = depurar($_POST["nombre_proveedor"]);
        $tmp_contrasena_proveedor = $_POST["contrasena_proveedor"];
        $foto_proveedor = "../../img/usuario/estandar.png";

        if ($tmp_email_proveedor == "") {
            $err_email_proveedor = "El email es obligatorio";
        } else {
            $sql = "SELECT * FROM proveedores WHERE email_proveedor ='$tmp_email_proveedor'";
            $resultado = $_conexion->query($sql);
            
            if ($resultado->num_rows == 1) {
                $err_email_proveedor = "El email $tmp_email_proveedor ya existe";
            } else {
                if (filter_var($tmp_email_proveedor, FILTER_VALIDATE_EMAIL) === false) {
                    $err_email_proveedor = "El email no es válido";
                } else {
                    $email_proveedor = $tmp_email_proveedor;
                }
            }
        }
        
        if ($tmp_nombre_proveedor == "") {
            $err_nombre_proveedor = "El nombre es obligatorio";
        } else {
            $sql = "SELECT * FROM proveedores WHERE nombre_proveedor ='$tmp_nombre_proveedor'";
            $resultado = $_conexion->query($sql);

            if ($resultado->num_rows == 1) {
                $err_nombre_proveedor = "El nombre $tmp_nombre_proveedor ya existe";
            } else {
                $patron = "/^[a-zA-Z0-9 áéióúÁÉÍÓÚñÑüÜ]+$/";
                if (!preg_match($patron, $tmp_nombre_proveedor)) {
                    $err_nombre_proveedor = "El nombre solo puede tener letras y números";
                } else {
                    $nombre_proveedor = $tmp_nombre_proveedor;
                }
            }
        }
        
        if ($tmp_contrasena_proveedor == "") {
            $err_contrasena_proveedor = "La contraseña es obligatoria";
        } else {
            if (strlen($tmp_contrasena_proveedor) < 8) {
                $err_contrasena_proveedor = "La contraseña tiene que tener como minimo 8 caracteres";
            } else {
                $patron = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/";
                if (!preg_match($patron, $tmp_contrasena_proveedor)) {
                    $err_contrasena_proveedor = "La contraseña tiene que tener letras en mayus y minus, algun numero y puede tener caracteres especiales";
                } else {
                    $contrasena_proveedor_cifrada = password_hash($tmp_contrasena_proveedor, PASSWORD_DEFAULT);
                }
            }
        }

        if (isset($email_proveedor) && isset($nombre_proveedor) && isset($contrasena_proveedor_cifrada)) {
            $sql = "INSERT INTO proveedores (email_proveedor, nombre_proveedor, contrasena_proveedor, foto_proveedor) 
                    VALUES ('$email_proveedor','$nombre_proveedor','$contrasena_proveedor_cifrada','$foto_proveedor')";
            $_conexion->query($sql);
            header("location: iniciar_sesion_proveedor.php");
            exit;
        }
    }
    ?>
    <section class="h-100 gradient-form" style="background-color: #F7E5CB;">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-xl-10">
                    <div class="card rounded-3 text-black">
                        <div class="row g-0">
                            <div class="col-lg-6">
                                <div class="card-body p-md-5 mx-md-4">

                                    <div class="text-center">
                                        <img src="../../img/logo-marron-nobg.png"
                                            style="width: 185px;" alt="logo">
                                        <h4 class="mt-1 mb-5 pb-1">SAMAS home</h4>
                                    </div>

                                    <form method="post" enctype="multipart/form-data">
                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <label class="form-label" for="nombre_proveedor">Nombre</label>
                                            <input type="text" id="nombre_proveedor" name="nombre_proveedor" class="form-control"
                                                placeholder="Inserte su nombre" />
                                            <?php if (isset($err_nombre_proveedor)) echo "<span class='error'>$err_nombre_proveedor</span>"; ?>
                                        </div>

                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <label class="form-label" for="email_proveedor">Email</label>
                                            <input type="email" id="email_proveedor" name="email_proveedor" class="form-control"
                                                placeholder="Inserte su correo electrónico" />
                                            <?php if (isset($err_email_proveedor)) echo "<span class='error'>$err_email_proveedor</span>"; ?>
                                        </div>

                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <label class="form-label" for="contrasena_proveedor">Contraseña</label>
                                            <input type="password" id="contrasena_proveedor" name="contrasena_proveedor" class="form-control" />
                                            <?php if (isset($err_contrasena_proveedor)) echo "<span class='error'>$err_contrasena_proveedor</span>"; ?>
                                        </div>

                                        <div class="pt-1 mb-5 pb-1">
                                            <button data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" type="submit">Registrarse</button>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-center pb-4">
                                            <p class="mb-0 me-2">Ya tienes cuenta?
                                                <a style="text-decoration: none; color: black;" href="../usuario/iniciar_sesion.php"><u>Iniciar sesión</u></a>
                                            </p>
                                        </div>
                                    </form>

                                </div>
                            </div>
                            <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
                                <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                                    <h4 class="mb-4">Mucho más que muebles</h4>
                                    <p class="small mb-0">Somos SAMAS home y operamos en toda la provincia de Málaga haciendo de tu reforma de casa algo más simple y fácil de lograr.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>