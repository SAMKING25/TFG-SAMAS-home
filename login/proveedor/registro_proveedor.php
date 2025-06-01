<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require('../../util/conexion.php');
require('../../util/funciones/utilidades.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tmp_email_proveedor = depurar($_POST["email_proveedor"]);
    $tmp_nombre_proveedor = depurar($_POST["nombre_proveedor"]);
    $tmp_contrasena_proveedor = $_POST["contrasena_proveedor"];
    $img_proveedor = "estandar.png";

    if ($tmp_email_proveedor == "") {
        $err_email_proveedor = "El email es obligatorio";
    } else {
        $sql = "SELECT * FROM proveedores WHERE email_proveedor ='$tmp_email_proveedor'";
        $resultado = $_conexion->query($sql);

        if ($resultado->num_rows == 1) {
            $err_email_proveedor = "Este correo electrónico ya se encuentra registrado";
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
            $err_nombre_proveedor = "El nombre de usuario ya está en uso";
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
        $sql = "INSERT INTO proveedores (email_proveedor, nombre_proveedor, contrasena_proveedor, img_proveedor) 
                VALUES ('$email_proveedor','$nombre_proveedor','$contrasena_proveedor_cifrada','$img_proveedor')";
        $_conexion->query($sql);
        $registro_ok = true; // <-- Añade esto
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
                                        <img src="../../img/logos/logo-marron-nobg.png" style="width: 185px;"
                                            alt="logo">
                                        <h4 class="mt-1 mb-5 pb-1">SAMAS home Enterprise</h4>
                                    </div>

                                    <form method="post" enctype="multipart/form-data">
                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <label class="form-label" for="nombre_proveedor">Nombre</label>
                                            <input type="text" id="nombre_proveedor" name="nombre_proveedor"
                                                class="form-control" placeholder="Inserte su nombre"
                                                value="<?php echo isset($_POST['nombre_proveedor']) ? htmlspecialchars($_POST['nombre_proveedor']) : ''; ?>" />
                                            <span class="error" id="nombre-error">
                                                <?php if (isset($err_nombre_proveedor))
                                                    echo $err_nombre_proveedor; ?>
                                            </span>
                                        </div>

                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <label class="form-label" for="email_proveedor">Email</label>
                                            <input type="text" id="email_proveedor" name="email_proveedor"
                                                class="form-control" placeholder="Inserte su correo electrónico"
                                                value="<?php echo isset($_POST['email_proveedor']) ? htmlspecialchars($_POST['email_proveedor']) : ''; ?>" />
                                            <span class="error" id="email-error">
                                                <?php if (isset($err_email_proveedor))
                                                    echo $err_email_proveedor; ?>
                                            </span>
                                        </div>

                                        <div data-mdb-input-init class="form-outline mb-4 password-wrapper">
                                            <label class="form-label" for="contrasena_proveedor">Contraseña</label>
                                            <input type="password" id="contrasena_proveedor" name="contrasena_proveedor"
                                                class="form-control"
                                                value="<?php echo isset($_POST['contrasena_proveedor']) ? htmlspecialchars($_POST['contrasena_proveedor']) : ''; ?>" />
                                            <!-- Botón mostrar/ocultar contraseña -->
                                            <button type="button" id="togglePassword"
                                                class="btn btn-outline-secondary btn-sm"
                                                style="position: absolute; top: 38px; right: 10px; z-index: 2; border: none;">
                                                <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                                            </button>

                                            <span class="error" id="password-error">
                                                <?php if (isset($err_contrasena_proveedor))
                                                    echo $err_contrasena_proveedor; ?>
                                            </span>
                                        </div>

                                        <div class="pt-1 mb-5 pb-1">
                                            <button data-mdb-button-init data-mdb-ripple-init
                                                class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3"
                                                type="submit">Registrarse</button>
                                            <a href="../../" data-mdb-button-init data-mdb-ripple-init
                                                class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3">Volver</a>
                                        </div>

                                        <div class="login-links">
                                            <p>
                                                <i class="bi bi-person-check"></i>
                                                ¿Ya tienes cuenta?
                                                <a href="./iniciar_sesion_proveedor">
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
            const nombreInput = document.getElementById('nombre_proveedor');
            const emailInput = document.getElementById('email_proveedor');
            const contrasenaInput = document.getElementById('contrasena_proveedor');

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
        const passwordInput = document.getElementById('contrasena_proveedor');
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
    <?php if (isset($registro_ok) && $registro_ok): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Cuenta de empresa registrada con éxito',
                text: '¡Ya puedes iniciar sesión!',
                confirmButtonColor: '#a57d31'
            }).then(() => {
                window.location.href = "iniciar_sesion_proveedor.php";
            });
        </script>
    <?php endif; ?>
</body>

</html>