<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link id="favicon" rel="shortcut icon" href="/img/logos/loguito_gris.png" />
    <?php
    error_reporting(E_ALL);
    ini_set("display_errors", 1);

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

        /* Mostrar/Ocultar contraseña */
        .password-wrapper {
            position: relative;
        }

        .toggle-password-btn {
            position: absolute;
            top: 38px;
            /* Ajusta según el padding/margen de tu input */
            right: 10px;
            z-index: 2;
            border: none;
            background: transparent;
            padding: 0 8px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
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

        @media (max-width: 576px) {
            .toggle-password-btn {
                top: 36px;
                /* Ajusta para móviles si es necesario */
                right: 8px;
            }
        }

        /* Quita hover de Mostrar/Ocultar contraseña */
        .toggle-password-btn,
        .toggle-password-btn:hover,
        .toggle-password-btn:focus {
            /* background: rgba(255, 255, 255, 0.8) !important; */
            /* Fondo blanco semitransparente */
            border: none !important;
            box-shadow: none !important;
            color: #333 !important;
            outline: none !important;
            cursor: pointer;
        }

        .toggle-password-btn i,
        .toggle-password-btn:hover i,
        .toggle-password-btn:focus i {
            color: #333 !important;
            /* Color oscuro siempre visible */
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            font-size: 1.2rem;
        }
    </style>
</head>

<body>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email_usuario = depurar($_POST["email_usuario"]);
        $contrasena_usuario = $_POST["contrasena_usuario"];

        $sql = "SELECT * FROM usuarios WHERE email_usuario ='$email_usuario'";
        $resultado = $_conexion->query($sql);

        if ($resultado->num_rows == 0) {
            $err_email_usuario = "El correo y la contraseña no coinciden";
        } else {
            $datos_usuario = $resultado->fetch_assoc();
            $acceso_concedido = password_verify($contrasena_usuario, $datos_usuario["contrasena_usuario"]);

            if ($acceso_concedido) {
                session_start();

                session_unset();
                session_destroy();

                session_start();

                $_SESSION["usuario"] = $datos_usuario["id_usuario"];

                // Redirige a donde quería ir el usuario
                if (isset($_SESSION['redirect_after_login'])) {
                    $redirect_url = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);
                    header("Location: $redirect_url");
                } else {
                    header("Location: /index.php"); // Si no había una página previa, va al inicio
                }
                exit();
            } else {
                $err_email_usuario = "El correo y la contraseña no coinciden";
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
                                        <h4 class="mt-1 mb-5 pb-1">SAMAS home</h4>
                                    </div>

                                    <form method="post" enctype="multipart/form-data">
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
                                                type="submit">Iniciar sesión</button>
                                            <a href="../../" data-mdb-button-init data-mdb-ripple-init
                                                class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3">Volver</a>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-center pb-4">
                                            <p class="mb-0 me-2">¿Eres una empresa?
                                                <a style="text-decoration: none; color: black;"
                                                    href="../proveedor/iniciar_sesion_proveedor"><u>Iniciar
                                                        sesión</u></a>
                                            </p>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-center pb-4">
                                            <p class="mb-0 me-2">¿No tienes cuenta?
                                                <a style="text-decoration: none; color: black;"
                                                    href="./registro_usuario"><u>Registrarse</u></a>
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
            const emailInput = document.getElementById('email_usuario');
            const passwordInput = document.getElementById('contrasena_usuario');
            const emailError = document.getElementById('email-error');
            const passwordError = document.getElementById('password-error');

            form.addEventListener('submit', function (event) {
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