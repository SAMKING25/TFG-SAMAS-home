<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesion proveedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link id="favicon" rel="shortcut icon" href="/img/logos/loguito_gris.png"/>	
    <?php
        error_reporting( E_ALL );
        ini_set("display_errors", 1 );

        require('../../util/conexion.php');
        require('../../util/funciones/utilidades.php');
    ?>
    <style>
        html {
            background: #fccb90;
        }

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
    if($_SERVER["REQUEST_METHOD"] == "POST"){
            $email_proveedor = depurar($_POST["email_proveedor"]);
            $contrasena_proveedor = $_POST["contrasena_proveedor"];

            $sql="SELECT * FROM proveedores WHERE email_proveedor ='$email_proveedor'";
            $resultado=$_conexion -> query($sql);

            if($resultado -> num_rows == 0){
                $err_email_proveedor = "El correo y la contraseña no coinciden";
            }else{
                $datos_usuario = $resultado -> fetch_assoc();
                $acceso_concedido = password_verify($contrasena_proveedor,$datos_usuario["contrasena_proveedor"]);

                if($acceso_concedido){
                    session_start();
    
                    session_unset();
                    session_destroy();
                                        
                    session_start();
                    
                    $_SESSION["proveedor"] = $datos_usuario["id_proveedor"];
                    header("location: ../../");
                    exit;
                }else{
                    $err_email_proveedor = "El correo y la contraseña no coinciden";
                }
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
                                        <img src="../../img/logos/logo-marron-nobg.png" style="width: 185px;"
                                            alt="logo">
                                        <h4 class="mt-1 mb-5 pb-1">SAMAS home Enterprise</h4>
                                    </div>

                                    <form method="post" enctype="multipart/form-data">
                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <label class="form-label" for="email_proveedor">Email</label>
                                            <input type="text" id="email_proveedor" name="email_proveedor"
                                                class="form-control" placeholder="Inserte su correo electrónico"
                                                value="<?php echo isset($_POST['email_proveedor']) ? htmlspecialchars($_POST['email_proveedor']) : ''; ?>" />
                                            <span class="error" id="email-error">
                                                <?php if (isset($err_email_proveedor)) echo $err_email_proveedor; ?>
                                            </span>
                                        </div>

                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <label class="form-label" for="contrasena_proveedor">Contraseña</label>
                                            <input type="password" id="contrasena_proveedor" name="contrasena_proveedor"
                                                class="form-control"
                                                value="<?php echo isset($_POST['contrasena_proveedor']) ? htmlspecialchars($_POST['contrasena_proveedor']) : ''; ?>" />
                                            <span class="error" id="password-error">
                                                <?php if (isset($err_contrasena_proveedor)) echo $err_contrasena_proveedor; ?>
                                            </span>
                                        </div>

                                        <div class="pt-1 mb-5 pb-1">
                                            <button data-mdb-button-init data-mdb-ripple-init
                                                class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3"
                                                type="submit">Iniciar sesión</button>
                                            <a href="../../" data-mdb-button-init data-mdb-ripple-init
                                                class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3">Volver</a>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-center pb-4">
                                            <p class="mb-0 me-2">¿Eres un usuario normal?
                                                <a style="text-decoration: none; color: black;"
                                                    href="../usuario/iniciar_sesion_usuario"><u>Iniciar
                                                        sesión</u></a>
                                            </p>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-center pb-4">
                                            <p class="mb-0 me-2">Si eres una empresa y quieres aparecer en SAMAS home
                                                ponte en contacto con nosotros y te daremos una cuenta de acceso.</p>
                                        </div>
                                    </form>

                                </div>
                            </div>
                            <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
                                <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                                    <h4 class="mb-4">Mucho más que muebles</h4>
                                    <p class="small mb-0">Somos SAMAS home y operamos en toda la provincia de Málaga
                                        haciendo de tu reforma de casa algo más simple y fácil de lograr.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Pop-up de cookies incluido-->
	<?php include('../../cookies.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const emailInput = document.getElementById('email_proveedor');
            const passwordInput = document.getElementById('contrasena_proveedor');
            const emailError = document.getElementById('email-error');
            const passwordError = document.getElementById('password-error');

            form.addEventListener('submit', function(event) {
                let valid = true;

                // Limpia los errores previos (incluyendo los de PHP)
                emailError.textContent = '';
                passwordError.textContent = '';

                // Email
                const emailValue = emailInput.value.trim();
                if (emailValue === '') {
                    emailError.textContent = 'El email es obligatorio.';
                    valid = false;
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
                    emailError.textContent = 'El email no es válido.';
                    valid = false;
                }

                // Contraseña
                const passwordValue = passwordInput.value.trim();
                if (passwordValue === '') {
                    passwordError.textContent = 'La contraseña es obligatoria.';
                    valid = false;
                }

                if (!valid) {
                    event.preventDefault();
                }
            });
        });
    </script>
</body>
</html>