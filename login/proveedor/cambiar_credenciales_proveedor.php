<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <?php
        error_reporting( E_ALL );
        ini_set("display_errors", 1 );    

        require('../../util/conexion.php');
        require('../../util/funciones/utilidades.php');

        define('IMG_USUARIO','/img/usuario/');

        session_start();
        if (!isset($_SESSION["usuario"])) { 
            header("location: ../login/proveedor/iniciar_sesion_proveedor.php");
            exit;
        }

        $id_proveedor = $_SESSION['usuario'];

        $sql = $_conexion-> prepare("SELECT * FROM proveedores WHERE id_proveedor = ?");
        $sql->bind_param("i", $id_proveedor);
        $sql->execute();
        $datos_actuales = $sql->get_result();

        while ($fila = $datos_actuales->fetch_assoc()) {
            $email_proveedor_actual = $fila['email_proveedor'];
            $nombre_proveedor_actual = $fila['nombre_proveedor'];
            $contrasena_proveedor_cifrada_actual = $fila['contrasena_proveedor'];
            $foto_proveedor_actual = $fila['foto_proveedor'];
        } 
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
                $err_foto_proveedor = "La imagen es obligatoria";
            } else {
                if (strlen($nuevo_nombre_imagen) > 60) {
                    $err_foto_proveedor = "La ruta de la imagen no puede tener mas de 60 caracteres";
                } else {
                    move_uploaded_file($ubicacion_temporal, to: $ubicacion_final);
                    $foto_proveedor_actual = $nuevo_nombre_imagen;
                    $sql = "UPDATE proveedores SET foto_proveedor = '$foto_proveedor_actual' WHERE id_proveedor = $id_proveedor";
                    $_conexion->query($sql);
                }
            }
        }

        // Redirige a donde quería ir el usuario
        if (isset($_SESSION['redirect_after_login'])) {
            $redirect_url = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
        } else {
            $redirect_url = "/index.php";
        }
    ?>
    <section class="h-100 gradient-form" style="background-color: #F7E5CB;">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-xl-10">
                    <div class="card rounded-3 text-black">
                        <div class="row g-0">
                            <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
                                <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                                    <h2 class="mb-4">Ajustes</h2>
                                    <p class="small mb-0">Datos personales</p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card-body p-md-5 mx-md-4">
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="text-center">
                                            <img src="<?php echo IMG_USUARIO.$foto_proveedor_actual ?>" style="width: 185px;" alt="logo" class="rounded-circle img-fluid" />
                                            <input type="file" disabled hidden name="nueva_img_proveedor" id="nueva_img_proveedor" class="form-control mb-4" accept="image/*"/>
                                        </div>
                                    
                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <label class="form-label" for="nuevo_nombre_proveedor">Nombre</label>
                                            <input type="text" disabled id="nuevo_nombre_proveedor" name="nuevo_nombre_proveedor" value="<?php echo $nombre_proveedor_actual?>"
                                                class="form-control" placeholder="Inserte su nombre" />
                                            <?php if (isset($err_nombre_proveedor)) echo "<span class='error'>$err_nombre_proveedor</span>"; ?>
                                        </div>

                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <label class="form-label" for="nuevo_email_proveedor">Email</label>
                                            <input type="email" disabled id="nuevo_email_proveedor" name="nuevo_email_proveedor" value="<?php echo $email_proveedor_actual?>"
                                                class="form-control" placeholder="Inserte su correo electrónico" />
                                            <?php if(isset($err_email_proveedor)) echo "<span class='error'>$err_email_proveedor</span>"; ?>
                                        </div>

                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <label class="form-label" for="nueva_contrasena_proveedor">Contraseña</label>
                                            <input type="password" disabled id="nueva_contrasena_proveedor" name="nueva_contrasena_proveedor"
                                                class="form-control"/>
                                            <?php if(isset($err_contrasena_proveedor)) echo "<span class='error'>$err_contrasena_proveedor</span>"; ?>
                                        </div>
                                        
                                        <div class="pt-1 mb-5 pb-1">
                                            <button data-mdb-button-init data-mdb-ripple-init
                                                class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3"
                                                type="button" id="cambiar_datos">Cambiar datos</button>
                                            <a href="<?php echo $redirect_url ?>" data-mdb-button-init data-mdb-ripple-init
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script>
        let modo_edicion = false;

        // Validación de errores
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');
            const nombreInput = document.getElementById('nuevo_nombre_proveedor');
            const emailInput = document.getElementById('nuevo_email_proveedor');
            const contrasenaInput = document.getElementById('nueva_contrasena_proveedor');

            const botonCambiar = document.getElementById('cambiar_datos');

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

            // "Cambiar datos" ==> "Aplicar cambios"
            botonCambiar.addEventListener('click', function (event) {
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
</body>

</html>