<?php
session_start();
if (!isset($_SESSION['registro_email'])) {
    header("Location: registro_usuario");
    exit;
}

$mensaje = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo_usuario = $_POST['codigo'];
    if ($codigo_usuario == $_SESSION['registro_codigo']) {
        // Código correcto: insertar usuario en la base de datos
        require('../../util/conexion.php');
        $email = $_SESSION['registro_email'];
        $nombre = $_SESSION['registro_nombre'];
        $contrasena = $_SESSION['registro_contrasena'];
        $id_suscripcion = $_SESSION['registro_id_suscripcion'];
        $img_usuario = $_SESSION['registro_img_usuario'];

        $sql = "INSERT INTO usuarios (email_usuario, nombre_usuario, contrasena_usuario, id_suscripcion, img_usuario) 
                VALUES ('$email','$nombre','$contrasena',$id_suscripcion,'$img_usuario')";
        $_conexion->query($sql);

        // Limpiar sesión y redirigir
        session_unset();
        session_destroy();
        header("Location: iniciar_sesion_usuario?verificado=1");
        exit;
    } else {
        $mensaje = "El código es incorrecto. Inténtalo de nuevo.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Verificar código</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                border-radius: 0 0 2rem 2rem;
                min-height: 180px;
                padding: 2rem 1.5rem;
            }

            .card-body {
                padding: 2rem 1.2rem;
            }
        }

        @media (max-width: 767.98px) {
            .side-panel {
                border-radius: 0 0 2rem 2rem;
                min-height: 120px;
                padding: 1.5rem 1rem;
            }

            .card {
                border-radius: 1.2rem;
            }
        }

        @media (max-width: 575.98px) {
            .card-body {
                padding: 1.2rem 0.5rem;
            }

            .side-panel {
                padding: 1rem 0.5rem;
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




                                        <!-- ALERTA BORRAR CÓDIGO -->
                                        <?php
                                        if (isset($_SESSION['registro_codigo'])) {
                                            echo "<div class='alert alert-info'>Código de verificación: <strong>" . htmlspecialchars($_SESSION['registro_codigo']) . "</strong></div>";
                                        }
                                        ?>
                                        <!-- ALERTA BORRAR CÓDIGO -->





                                    </div>
                                    <form method="post">
                                        <div class="mb-4">
                                            <label for="codigo" class="form-label">Código de verificación</label>
                                            <input type="text" class="form-control" id="codigo" name="codigo"
                                                placeholder="Introduce el código recibido">
                                        </div>
                                        <?php if ($mensaje)
                                            echo "<div class='alert alert-danger'>$mensaje</div>"; ?>
                                        <div class="pt-1 mb-5 pb-1">
                                            <button class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3"
                                                type="submit">Verificar</button>
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
                                                <a href="registro_usuario">
                                                    Volver
                                                </a>
                                            </p>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-lg-6 d-flex align-items-center side-panel">
                                <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                                    <h4 class="mb-4">Verifica tu correo</h4>
                                    <p class="small mb-0">
                                        Te hemos enviado un código de verificación a tu correo electrónico.<br>
                                        Por favor, revisa tu bandeja de entrada y la carpeta de spam e introduce el
                                        código para completar tu registro.
                                    </p>
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
            const codigoInput = document.getElementById('codigo');

            form.addEventListener('submit', function (e) {
                limpiarErrores();
                if (codigoInput.value.trim() === '') {
                    mostrarError(codigoInput, 'El código es obligatorio.');
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