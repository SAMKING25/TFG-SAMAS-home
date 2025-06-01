<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link id="favicon" rel="shortcut icon" href="/img/logos/loguito_gris.png" />
    <link href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css" rel="stylesheet">
    <?php
    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    require('../../util/conexion.php');
    require('../../util/funciones/utilidades.php');

    define('IMG_USUARIO', '/img/usuario/');

    session_start();
    if (!isset($_SESSION["proveedor"])) {
        header("location: ../login/proveedor/iniciar_sesion_proveedor");
        exit;
    }

    $id_proveedor = $_SESSION['proveedor'];

    $sql = $_conexion->prepare("SELECT * FROM proveedores WHERE id_proveedor = ?");
    $sql->bind_param("i", $id_proveedor);
    $sql->execute();
    $datos_actuales = $sql->get_result();

    while ($fila = $datos_actuales->fetch_assoc()) {
        $email_proveedor_actual = $fila['email_proveedor'];
        $nombre_proveedor_actual = $fila['nombre_proveedor'];
        $contrasena_proveedor_cifrada_actual = $fila['contrasena_proveedor'];
        $img_proveedor_actual = $fila['img_proveedor'];
    }
    ?>
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
        }

        .form-control:focus {
            border-color: #a39082;
            box-shadow: 0 0 0 2px #fccb90aa;
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

        .foto-perfil-editable:hover img {
            filter: brightness(0.7);
            transition: filter 0.2s;
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
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nuevo_email_proveedor = depurar($_POST["nuevo_email_proveedor"]);
        $nuevo_nombre_proveedor = depurar($_POST["nuevo_nombre_proveedor"]);
        $nueva_contrasena_proveedor = $_POST["nueva_contrasena_proveedor"];
        $nueva_img_proveedor = "estandar.png";

        $nuevo_nombre_imagen = $_FILES["nueva_img_proveedor"]["name"];
        $ubicacion_temporal = $_FILES["nueva_img_proveedor"]["tmp_name"];
        $ubicacion_final = "../../img/usuario/$nuevo_nombre_imagen";

        if ($nuevo_email_proveedor == "") {
            $err_email_proveedor = "El email es obligatorio";
        } else {
            $sql = "SELECT * FROM proveedores WHERE email_proveedor ='$nuevo_email_proveedor'";
            $resultado = $_conexion->query($sql);
            if (filter_var($nuevo_email_proveedor, FILTER_VALIDATE_EMAIL) === false) {
                $err_email_proveedor = "El email no es válido";
            } else {
                $email_proveedor_actual = $nuevo_email_proveedor;
                $sql = "UPDATE proveedores SET email_proveedor = '$email_proveedor_actual' WHERE id_proveedor = $id_proveedor";
                $_conexion->query($sql);
            }
        }

        if ($nuevo_nombre_proveedor == "") {
            $err_nombre_proveedor = "El nombre es obligatorio";
        } else {
            $sql = "SELECT * FROM proveedores WHERE nombre_proveedor ='$nuevo_nombre_proveedor'";
            $resultado = $_conexion->query($sql);
            $patron = "/^[a-zA-Z0-9 áéióúÁÉÍÓÚñÑüÜ]+$/";
            if (!preg_match($patron, $nuevo_nombre_proveedor)) {
                $err_nombre_proveedor = "El nombre solo puede tener letras y números";
            } else {
                $nombre_proveedor_actual = $nuevo_nombre_proveedor;
                $sql = "UPDATE proveedores SET nombre_proveedor = '$nombre_proveedor_actual' WHERE id_proveedor = $id_proveedor";
                $_conexion->query($sql);
            }
        }

        if ($nueva_contrasena_proveedor == "") {
            $err_contrasena_proveedor = "La contraseña es obligatoria";
        } else {
            if (strlen($nueva_contrasena_proveedor) < 8) {
                $err_contrasena_proveedor = "La contraseña tiene que tener como minimo 8 caracteres";
            } else {
                $patron = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/";
                if (!preg_match($patron, $nueva_contrasena_proveedor)) {
                    $err_contrasena_proveedor = "La contraseña tiene que tener letras en mayus y minus, algun numero y puede tener caracteres especiales";
                } else {
                    $contrasena_proveedor_cifrada_actual = password_hash($nueva_contrasena_proveedor, PASSWORD_DEFAULT);
                    $sql = "UPDATE proveedores SET contrasena_proveedor = '$contrasena_proveedor_cifrada_actual' WHERE id_proveedor = $id_proveedor";
                    $_conexion->query($sql);
                }
            }
        }

        if ($nuevo_nombre_imagen == "") {
            $err_img_proveedor = "La imagen es obligatoria";
        } else {
            if (strlen($nuevo_nombre_imagen) > 60) {
                $err_img_proveedor = "La ruta de la imagen no puede tener mas de 60 caracteres";
            } else {
                move_uploaded_file($ubicacion_temporal, to: $ubicacion_final);
                $img_proveedor_actual = $nuevo_nombre_imagen;
                $sql = "UPDATE proveedores SET img_proveedor = '$img_proveedor_actual' WHERE id_proveedor = $id_proveedor";
                $_conexion->query($sql);
            }
        }
    }

    // Redirige a donde quería ir el usuario
    if (isset($_SESSION['redirect_after_login'])) {
        $redirect_url = $_SESSION['redirect_after_login'];
        unset($_SESSION['redirect_after_login']);
    } else {
        $redirect_url = "/index";
    }
    ?>
    <section class="h-100 gradient-form" style="background-color: #F7E5CB;">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-xl-10">
                    <div class="card rounded-3 text-black">
                        <div class="row g-0">
                            <div class="col-lg-6 d-flex align-items-center side-panel">
                                <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                                    <h4 class="mb-4">Ajustes</h4>
                                    <p class="small mb-0">Datos personales</p>
                                    <hr class="my-4" style="border-color: #fff6;" />
                                    <p class="mb-2">
                                        Desde esta sección puedes modificar tu información personal, como tu nombre, correo electrónico, contraseña y foto de perfil.
                                    </p>
                                    <p class="mb-2">
                                        Mantén tus datos actualizados para una mejor experiencia y seguridad en la plataforma.
                                    </p>
                                    <p class="mb-0">
                                        Recuerda que tu información es confidencial y solo tú puedes cambiarla.
                                    </p>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="card-body p-md-5 mx-md-4">
                                    <div class="text-center">
                                    </div>
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="text-center">
                                            <div id="foto-perfil-wrapper"
                                                style="width: 185px; height: 185px; margin: 0 auto; display: flex; align-items: center; justify-content: center; cursor: pointer; position: relative;">
                                                <img id="foto-perfil"
                                                    src="<?php echo IMG_USUARIO . $img_proveedor_actual ?>"
                                                    style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; border: 2px solid #ccc;"
                                                    alt="Foto de perfil" class="img-fluid" />
                                            </div>
                                            <input type="file" name="nueva_img_proveedor" id="nueva_img_proveedor"
                                                class="form-control mb-4" accept="image/*" style="display: none;" />
                                            <?php if (isset($err_img_proveedor))
                                                echo "<span class='error'>$err_img_proveedor</span>"; ?>
                                        </div>

                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <label class="form-label" for="nuevo_nombre_proveedor">Nombre</label>
                                            <input type="text" disabled id="nuevo_nombre_proveedor"
                                                name="nuevo_nombre_proveedor"
                                                value="<?php echo $nombre_proveedor_actual ?>" class="form-control"
                                                placeholder="Inserte su nombre" />
                                            <?php if (isset($err_nombre_proveedor))
                                                echo "<span class='error'>$err_nombre_proveedor</span>"; ?>
                                        </div>

                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <label class="form-label" for="nuevo_email_proveedor">Email</label>
                                            <input type="email" disabled id="nuevo_email_proveedor"
                                                name="nuevo_email_proveedor"
                                                value="<?php echo $email_proveedor_actual ?>" class="form-control"
                                                placeholder="Inserte su correo electrónico" />
                                            <?php if (isset($err_email_proveedor))
                                                echo "<span class='error'>$err_email_proveedor</span>"; ?>
                                        </div>

                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <label class="form-label"
                                                for="nueva_contrasena_proveedor">Contraseña</label>
                                            <input type="password" disabled id="nueva_contrasena_proveedor"
                                                name="nueva_contrasena_proveedor" class="form-control" />
                                            <?php if (isset($err_contrasena_proveedor))
                                                echo "<span class='error'>$err_contrasena_proveedor</span>"; ?>
                                        </div>

                                        <div class="pt-1 mb-5 pb-1">
                                            <button data-mdb-button-init data-mdb-ripple-init
                                                class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3"
                                                type="button" id="cambiar_datos">Cambiar datos</button>
                                            <a href="<?php echo $redirect_url ?>" data-mdb-button-init
                                                data-mdb-ripple-init
                                                class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3">Volver</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal para recortar imagen -->
    <div class="modal fade" id="cropperModal" tabindex="-1" aria-labelledby="cropperModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cropperModalLabel">Recorta tu foto de perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <img id="cropper-image" style="max-width:100%; max-height:400px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="cropper-apply" class="btn btn-primary">Aplicar recorte</button>
                </div>
            </div>
        </div>
    </div>

    <?php include('../../cookies.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>
    <script>
        let modo_edicion = false;

        // Validación de errores
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const nombreInput = document.getElementById('nuevo_nombre_proveedor');
            const emailInput = document.getElementById('nuevo_email_proveedor');
            const contrasenaInput = document.getElementById('nueva_contrasena_proveedor');

            const botonCambiar = document.getElementById('cambiar_datos');

            form.addEventListener('submit', function(e) {
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
                errores.forEach(function(error) {
                    error.remove();
                });
            }

            // "Cambiar datos" ==> "Aplicar cambios"
            botonCambiar.addEventListener('click', function(event) {
                if (!modo_edicion) {
                    event.preventDefault();

                    botonCambiar.textContent = "Aplicar cambios";
                    modo_edicion = true;

                    form.querySelectorAll('input').forEach(input => {
                        input.disabled = false;
                    });

                    const inputFile = document.getElementById('nueva_img_proveedor');
                    inputFile.hidden = false;
                } else {
                    form.requestSubmit();
                }
            });
        });
    </script>
    <script>
        // Foto de perfil: click para cambiar imagen
        document.addEventListener('DOMContentLoaded', function() {
            const fotoPerfilWrapper = document.getElementById('foto-perfil-wrapper');
            const fotoPerfil = document.getElementById('foto-perfil');
            const inputFile = document.getElementById('nueva_img_proveedor');
            const botonCambiar = document.getElementById('cambiar_datos');

            // Cambia el cursor según el modo
            function actualizarCursor() {
                if (typeof modo_edicion !== 'undefined' && modo_edicion) {
                    fotoPerfilWrapper.style.cursor = 'pointer';
                    fotoPerfilWrapper.classList.add('foto-perfil-editable');
                } else {
                    fotoPerfilWrapper.style.cursor = 'default';
                    fotoPerfilWrapper.classList.remove('foto-perfil-editable');
                }
            }
            actualizarCursor();

            // Permitir click en la foto SOLO si modo_edicion es true
            fotoPerfilWrapper.addEventListener('click', function() {
                if (typeof modo_edicion !== 'undefined' && modo_edicion) {
                    inputFile.click();
                }
            });

            // Actualiza el cursor cuando cambie el modo
            if (botonCambiar) {
                botonCambiar.addEventListener('click', function() {
                    setTimeout(actualizarCursor, 10);
                });
            }

            // Previsualización de la imagen seleccionada
            inputFile.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        fotoPerfil.src = ev.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let cropper;
            const inputFile = document.getElementById('nueva_img_proveedor');
            const fotoPerfil = document.getElementById('foto-perfil');
            const cropperModal = new bootstrap.Modal(document.getElementById('cropperModal'));
            const cropperImage = document.getElementById('cropper-image');
            const cropperApply = document.getElementById('cropper-apply');

            inputFile.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        cropperImage.src = ev.target.result;
                        cropperModal.show();
                    }
                    reader.readAsDataURL(file);
                }
            });

            document.getElementById('cropperModal').addEventListener('shown.bs.modal', function() {
                cropper = new Cropper(cropperImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 1,
                    movable: true,
                    zoomable: true,
                    rotatable: false,
                    scalable: false,
                });
            });

            document.getElementById('cropperModal').addEventListener('hidden.bs.modal', function() {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
            });

            cropperApply.addEventListener('click', function() {
                if (cropper) {
                    const canvas = cropper.getCroppedCanvas({
                        width: 300,
                        height: 300,
                        imageSmoothingQuality: 'high'
                    });
                    fotoPerfil.src = canvas.toDataURL();
                    canvas.toBlob(function(blob) {
                        const fileInput = document.getElementById('nueva_img_proveedor');
                        const file = new File([blob], "recorte.png", {
                            type: "image/png"
                        });
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        fileInput.files = dataTransfer.files;
                    }, 'image/png');
                    cropperModal.hide();
                }
            });
        });
    </script>
</body>

</html>