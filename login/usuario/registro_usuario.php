<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
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
            $tmp_email_usuario = depurar($_POST["email_usuario"]);
            $tmp_nombre_usuario = depurar($_POST["nombre_usuario"]);
            $tmp_contrasena_usuario = $_POST["contrasena_usuario"];
            $img_usuario = "estandar.png";
            $id_suscripcion = 1; //Suscripción básica por defecto

            if ($tmp_email_usuario == "") {
                $err_email_usuario = "El email es obligatorio";
            } else {
                $sql = "SELECT * FROM usuarios WHERE email_usuario ='$tmp_email_usuario'";
                $resultado = $_conexion->query($sql);
                
                if ($resultado->num_rows == 1) {
                    $err_email_usuario = "Este correo electrónico ya se encuentra registrado";
                } else {
                    if (filter_var($tmp_email_usuario, FILTER_VALIDATE_EMAIL) === false) {
                        $err_email_usuario = "El email no es válido";
                    } else {
                        $email_usuario = $tmp_email_usuario;
                    }
                }
            }

            if ($tmp_nombre_usuario == "") {
                $err_nombre_usuario = "El nombre es obligatorio";
            } else {
                $sql = "SELECT * FROM usuarios WHERE nombre_usuario ='$tmp_nombre_usuario'";
                $resultado = $_conexion->query($sql);
    
                if ($resultado->num_rows == 1) {
                    $err_nombre_usuario = "El nombre de usuario ya está en uso";
                } else {
                    $patron = "/^[a-zA-Z0-9 áéióúÁÉÍÓÚñÑüÜ]+$/";
                    if (!preg_match($patron, $tmp_nombre_usuario)) {
                        $err_nombre_usuario = "El nombre solo puede tener letras y números";
                    } else {
                        $nombre_usuario = $tmp_nombre_usuario;
                    }
                }
            }

            if ($tmp_contrasena_usuario == "") {
                $err_contrasena_usuario = "La contraseña es obligatoria";
            } else {
                if (strlen($tmp_contrasena_usuario) < 8) {
                    $err_contrasena_usuario = "La contraseña tiene que tener como minimo 8 caracteres";
                } else {
                    $patron = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/";
                    if (!preg_match($patron, $tmp_contrasena_usuario)) {
                        $err_contrasena_usuario = "La contraseña tiene que tener letras en mayus y minus, algun numero y puede tener caracteres especiales";
                    } else {
                        $contrasena_usuario_cifrada = password_hash($tmp_contrasena_usuario, PASSWORD_DEFAULT);
                    }
                }
            }

            if (isset($email_usuario) && isset($nombre_usuario) && isset($contrasena_usuario_cifrada)) {
                // Generar código de verificación
                $codigo_verificacion = rand(100000, 999999);
                
                // Guardar datos temporales en sesión
                session_start();
                $_SESSION['registro_email'] = $email_usuario;
                $_SESSION['registro_nombre'] = $nombre_usuario;
                $_SESSION['registro_contrasena'] = $contrasena_usuario_cifrada;
                $_SESSION['registro_id_suscripcion'] = $id_suscripcion;
                $_SESSION['registro_img_usuario'] = $img_usuario;
                $_SESSION['registro_codigo'] = $codigo_verificacion;

                // Enviar email con el código
                $asunto = "Código de verificación SAMAS home";
                $mensaje = "Tu código de verificación es: $codigo_verificacion";
                $cabeceras = "From: no-reply@samas-home.com\r\n";
                mail($email_usuario, $asunto, $mensaje, $cabeceras);

                // Redirigir a la página de verificación
                header("Location: verificar_codigo");
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
                                        <img src="/img/logos/logo-marron-nobg.png" style="width: 185px;" alt="logo">
                                        <h4 class="mt-1 mb-5 pb-1">SAMAS home</h4>
                                    </div>
                                    <form method="post" enctype="multipart/form-data">
                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <label class="form-label" for="nombre_usuario">Nombre</label>
                                            <input type="text" id="nombre_usuario" name="nombre_usuario"
                                                class="form-control" placeholder="Inserte su nombre"
                                                value="<?php echo isset($_POST['nombre_usuario']) ? htmlspecialchars($_POST['nombre_usuario']) : ''; ?>" />
                                            <span class="error" id="nombre-error">
                                                <?php if (isset($err_nombre_usuario)) echo $err_nombre_usuario; ?>
                                            </span>
                                        </div>

                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <label class="form-label" for="email_usuario">Email</label>
                                            <input type="text" id="email_usuario" name="email_usuario"
                                                class="form-control" placeholder="Inserte su correo electrónico"
                                                value="<?php echo isset($_POST['email_usuario']) ? htmlspecialchars($_POST['email_usuario']) : ''; ?>" />
                                            <span class="error" id="email-error">
                                                <?php if (isset($err_email_usuario)) echo $err_email_usuario; ?>
                                            </span>
                                        </div>

                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <label class="form-label" for="contrasena_usuario">Contraseña</label>
                                            <input type="password" id="contrasena_usuario" name="contrasena_usuario"
                                                class="form-control"
                                                value="<?php echo isset($_POST['contrasena_usuario']) ? htmlspecialchars($_POST['contrasena_usuario']) : ''; ?>" />
                                            <span class="error" id="password-error">
                                                <?php if (isset($err_contrasena_usuario)) echo $err_contrasena_usuario; ?>
                                            </span>
                                        </div>

                                        <div class="pt-1 mb-5 pb-1">
                                            <button data-mdb-button-init data-mdb-ripple-init
                                                class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3"
                                                type="submit">Registrarse</button>
                                            <a href="../../" data-mdb-button-init data-mdb-ripple-init
                                                class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3">Volver</a>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-center pb-4">
                                            <p class="mb-0 me-2">Ya tienes cuenta?
                                                <a style="text-decoration: none; color: black;"
                                                    href="./iniciar_sesion_usuario"><u>Iniciar sesión</u></a>
                                            </p>
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
    <?php include('../../cookies.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');
            const nombreInput = document.getElementById('nombre_usuario');
            const emailInput = document.getElementById('email_usuario');
            const contrasenaInput = document.getElementById('contrasena_usuario');

            form.addEventListener('submit', function (e) {
                let tieneErrores = false;

                limpiarErrores();

                // Nombre
                const nombreValor = nombreInput.value.trim();
                const nombrePatron = /^[a-zA-Z0-9 áéióúÁÉÍÓÚñÑüÜ]+$/;
                if (nombreValor === '') {
                    mostrarError(nombreInput, 'El nombre es obligatorio.');
                    tieneErrores = true;
                } else if (!nombrePatron.test(nombreValor)) {
                    mostrarError(nombreInput, 'El nombre solo puede contener letras, números y espacios.');
                    tieneErrores = true;
                }

                // Email
                const emailValor = emailInput.value.trim();
                const emailPatron = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (emailValor === '') {
                    mostrarError(emailInput, 'El email es obligatorio.');
                    tieneErrores = true;
                } else if (!emailPatron.test(emailValor)) {
                    mostrarError(emailInput, 'El formato del email no es válido.');
                    tieneErrores = true;
                }

                // Contraseña
                const contrasenaValor = contrasenaInput.value;
                const contrasenaPatron = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/;
                if (contrasenaValor === '') {
                    mostrarError(contrasenaInput, 'La contraseña es obligatoria.');
                    tieneErrores = true;
                } else if (contrasenaValor.length < 8) {
                    mostrarError(contrasenaInput, 'La contraseña debe tener al menos 8 caracteres.');
                    tieneErrores = true;
                } else if (!contrasenaPatron.test(contrasenaValor)) {
                    mostrarError(contrasenaInput, 'Debe tener mayúsculas, minúsculas, números y puede incluir caracteres especiales.');
                    tieneErrores = true;
                }

                if (tieneErrores) {
                    e.preventDefault();
                }
            });

            function mostrarError(input, mensaje) {
                const errorSpan = document.createElement('span');
                errorSpan.classList.add('error');
                errorSpan.textContent = mensaje;
                input.parentElement.appendChild(errorSpan);
            }

            function limpiarErrores() {
                const errores = document.querySelectorAll('.error');
                errores.forEach(function (error) {
                    error.remove();
                });
            }
        });
    </script>
</body>

</html>