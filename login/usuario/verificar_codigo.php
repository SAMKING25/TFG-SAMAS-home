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
                                            <input type="text" class="form-control" id="codigo" name="codigo" placeholder="Introduce el código recibido">
                                        </div>
                                        <?php if ($mensaje) echo "<div class='alert alert-danger'>$mensaje</div>"; ?>
                                        <div class="pt-1 mb-5 pb-1">
                                            <button class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" type="submit">Verificar</button>
                                            <a href="registro_usuario.php" class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3">Volver</a>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-center pb-4">
                                            <p class="mb-0 me-2">¿Ya tienes cuenta?
                                                <a style="text-decoration: none; color: black;" href="./iniciar_sesion_usuario"><u>Iniciar sesión</u></a>
                                            </p>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
                                <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                                    <h4 class="mb-4">Verifica tu correo</h4>
                                    <p class="small mb-0">
                                        Te hemos enviado un código de verificación a tu correo electrónico.<br>
                                        Por favor, revisa tu bandeja de entrada y la carpeta de spam e introduce el código para completar tu registro.
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