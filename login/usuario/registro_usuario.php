<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require('../../util/conexion.php');
require('../../util/funciones/utilidades.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
        $logo_url = "https://samas-home.com/img/logos/logo-marron-nobg.png"; // Cambia por la URL real de tu logo
        $mensaje = '
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Código de verificación SAMAS home</title>
        </head>
        <body style="font-family: Montserrat, Arial, sans-serif; background: #f7e5cb; margin:0; padding:0;">
            <div style="max-width: 480px; margin: 40px auto; background: #fff8f1; border-radius: 18px; box-shadow: 0 4px 24px #a3908240; padding: 32px 24px; text-align: center;">
                <img src="' . $logo_url . '" alt="SAMAS home" style="width: 120px; margin-bottom: 18px; filter: drop-shadow(0 2px 8px #a39082aa);">
                <h2 style="color: #a57d31; margin-bottom: 8px;">¡Bienvenido a SAMAS home!</h2>
                <p style="color: #6d4c1b; font-size: 1.08rem; margin-bottom: 28px;">
                    Para completar tu registro, introduce el siguiente código de verificación:
                </p>
                <div style="background: #fccb90; color:rgb(0, 0, 0); font-size: 2.2rem; font-weight: bold; letter-spacing: 8px; border-radius: 12px; padding: 18px 0; margin-bottom: 24px;">
                    ' . $codigo_verificacion . '
                </div>
                <p style="color: #a39082; font-size: 0.98rem;">
                    Si no has solicitado este registro, puedes ignorar este correo.
                </p>
                <hr style="margin: 32px 0 16px 0; border: none; border-top: 1.5px solid #f7e5cb;">
                <p style="color: #bdbdbd; font-size: 0.92rem;">SAMAS home · Málaga</p>
            </div>
        </body>
        </html>
        ';
        $cabeceras = "MIME-Version: 1.0\r\n";
        $cabeceras .= "Content-type: text/html; charset=UTF-8\r\n";
        $cabeceras .= "From: no-reply@samas-home.com\r\n";
        mail($email_usuario, $asunto, $mensaje, $cabeceras);

        // Redirigir a la página de verificación
        header("Location: verificar_codigo");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link id="favicon" rel="shortcut icon" href="/img/logos/loguito_gris.png" />
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #fccb90 0%, #a39082 100%);
            min-height: 100vh;
        }

        .gradient-form {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: none;
        }

        .card {
            border: none;
            border-radius: 2rem;
            box-shadow: 0 8px 32px 0 rgba(102, 67, 20, 0.15), 0 1.5px 6px 0 rgba(165, 125, 49, 0.10);
            overflow: hidden;
            background: rgba(255, 255, 255, 0.95);
        }

        .card-body {
            padding: 3rem 2.5rem;
        }

        .text-center img {
            filter: drop-shadow(0 2px 8px #a39082aa);
        }

        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');

        .form-label {
            font-family: 'Montserrat', Arial, Helvetica, sans-serif;
            font-weight: 600;
            color: #a39082;
            letter-spacing: 0.5px;
        }

        .form-control {
            font-family: 'Montserrat', Arial, Helvetica, sans-serif;
            font-size: 1.08rem;
            border-radius: 1.5rem;
            border: 1.5px solid #f7e5cb;
            background: #fff8f1;
            padding: 0.75rem 1.25rem;
            transition: border-color 0.2s;
            color: #6d4c1b;
            /* color acorde a la paleta */
        }

        .form-control:focus {
            border-color: #a39082;
            box-shadow: 0 0 0 2px #fccb90aa;
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-password-btn {
            position: absolute;
            top: 50%;
            right: 18px;
            transform: translateY(-50%);
            z-index: 2;
            border: none;
            background: transparent;
            padding: 0;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #a39082 !important;
            font-size: 1.3rem;
            cursor: pointer;
        }

        .toggle-password-btn:focus {
            outline: none;
        }

        .btn-primary.gradient-custom-2 {
            background: linear-gradient(90deg, #a39082 0%, #927447 50%, #a57d31 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 2rem;
            box-shadow: 0 2px 8px #a3908240;
            transition: background 0.2s, box-shadow 0.2s;
        }

        .btn-primary.gradient-custom-2:hover,
        .btn-primary.gradient-custom-2:focus {
            background: linear-gradient(90deg, #a57d31 0%, #927447 100%);
            box-shadow: 0 4px 16px #a3908240;
            color: #fff;
        }

        .btn-block {
            width: 100%;
        }

        .error {
            color: #b94a48;
            font-size: 0.97rem;
            margin-top: 0.25rem;
            font-weight: 500;
        }

        .side-panel {
            background: linear-gradient(135deg, #a39082 0%, #927447 100%);
            color: #fff;
            /* border-top-right-radius: 2rem;
            border-bottom-right-radius: 2rem; */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100%;
            padding: 3rem 2.5rem;
        }

        .side-panel h4 {
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 1.5rem;
        }

        .side-panel p {
            font-size: 1.08rem;
            opacity: 0.93;
        }

        .text-center h4 {
            color: #a57d31;
            font-weight: 700;
            letter-spacing: 1px;
        }

        a {
            color: #a57d31;
            text-decoration: underline;
            transition: color 0.2s;
        }

        a:hover {
            color: #927447;
        }

        .d-flex.align-items-center.justify-content-center.pb-4 {
            margin-top: 0.5rem;
        }

        /* Links de regístrate y eres una empresa */
        .login-links {
            margin-top: 2rem;
        }

        .login-links p {
            font-size: 1.08rem;
            color: #a39082;
            margin-bottom: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .login-links a {
            color: #a57d31;
            font-weight: 600;
            text-decoration: none;
            border-bottom: 2px solid #a57d31;
            transition: color 0.2s, border-color 0.2s;
            margin-left: 0.3rem;
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
        }

        .login-links a:hover {
            color: #927447;
            border-bottom: 2px solid #927447;
        }

        .login-links .bi {
            font-size: 1.1em;
            margin-right: 0.15em;
            opacity: 0.8;
        }

        @media (max-width: 991.98px) {
            .side-panel {
                border-radius: 0 0 2rem 2rem !important;
                min-height: 180px;
                padding: 2rem 1.5rem;
            }

            .card {
                border-radius: 2rem !important;
                overflow: hidden;
            }
        }

        @media (max-width: 767.98px) {
            .side-panel {
                border-radius: 0 0 1.2rem 1.2rem !important;
                min-height: 120px;
                padding: 1.5rem 1rem;
            }

            .card {
                border-radius: 1.2rem !important;
                overflow: hidden;
            }
        }

        @media (max-width: 575.98px) {
            .card-body {
                padding: 1.2rem 0.5rem;
            }

            .side-panel {
                padding: 1rem 0.5rem;
                border-radius: 0 0 1.2rem 1.2rem !important;
            }

            .card {
                border-radius: 1.2rem !important;
                overflow: hidden;
            }
        }
    </style>
</head>

<body>
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
                                                <?php if (isset($err_nombre_usuario))
                                                    echo $err_nombre_usuario; ?>
                                            </span>
                                        </div>

                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <label class="form-label" for="email_usuario">Email</label>
                                            <input type="text" id="email_usuario" name="email_usuario"
                                                class="form-control" placeholder="Inserte su correo electrónico"
                                                value="<?php echo isset($_POST['email_usuario']) ? htmlspecialchars($_POST['email_usuario']) : ''; ?>" />
                                            <span class="error" id="email-error">
                                                <?php if (isset($err_email_usuario))
                                                    echo $err_email_usuario; ?>
                                            </span>
                                        </div>

                                        <div data-mdb-input-init class="form-outline mb-4 password-wrapper">
                                            <label class="form-label" for="contrasena_usuario">Contraseña</label>
                                            <input type="password" id="contrasena_usuario" name="contrasena_usuario"
                                                class="form-control"
                                                value="<?php echo isset($_POST['contrasena_usuario']) ? htmlspecialchars($_POST['contrasena_usuario']) : ''; ?>" />
                                            <!-- Botón mostrar/ocultar contraseña -->
                                            <button type="button" id="togglePassword"
                                                class="btn btn-outline-secondary btn-sm"
                                                style="position: absolute; top: 38px; right: 10px; z-index: 2; border: none;">
                                                <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                                            </button>
                                            <span class="error" id="password-error">
                                                <?php if (isset($err_contrasena_usuario))
                                                    echo $err_contrasena_usuario; ?>
                                            </span>
                                        </div>

                                        <div class="pt-1 mb-5 pb-1">
                                            <button data-mdb-button-init data-mdb-ripple-init
                                                class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3"
                                                type="submit">Registrarse</button>
                                        </div>

                                        <div class="login-links">
                                            <p>
                                                <i class="bi bi-person-check"></i>
                                                ¿Ya tienes cuenta?
                                                <a href="./iniciar_sesion_usuario">
                                                    Iniciar sesión
                                                </a>
                                            </p>
                                            <p>
                                                <i class="bi bi-arrow-left-circle"></i>
                                                <a href="../../">
                                                    Volver al inicio
                                                </a>
                                            </p>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-lg-6 d-flex align-items-center side-panel">
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
    <script>
        // Mostrar/ocultar contraseña
        // Inicializa el icono según el estado inicial del input
        const passwordInput = document.getElementById('contrasena_usuario');
        const icon = document.getElementById('togglePasswordIcon');
        if (passwordInput.type === 'password') {
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }

        document.getElementById('togglePassword').addEventListener('click', function () {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        });
    </script>
</body>

</html>