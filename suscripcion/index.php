<?php
// Mostrar todos los errores de PHP para depuración
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Incluir archivo de conexión a la base de datos
require('../util/conexion.php');
session_start();

// Obtener el id de usuario desde la sesión (si existe)
$id_usuario = $_SESSION['usuario'] ?? null;
$id_suscripcion_usuario = 1; // Por defecto básica

// Si el usuario está logueado, obtener su suscripción actual
if ($id_usuario) {
    $sql_user = $_conexion->prepare("SELECT id_suscripcion FROM usuarios WHERE id_usuario = ?");
    $sql_user->bind_param("i", $id_usuario);
    $sql_user->execute();
    $res_user = $sql_user->get_result();
    if ($row = $res_user->fetch_assoc()) {
        $id_suscripcion_usuario = (int)$row['id_suscripcion'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAMAS HOME - suscripcion</title>
    <!-- Bootstrap CSS principal -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Iconos de Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <!-- Favicon del sitio -->
    <link id="favicon" rel="shortcut icon" href="/img/logos/loguito_gris.png" />
    <!-- Font Awesome para iconos adicionales -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <!-- Bootstrap CSS alternativo (puede ser redundante) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Archivo CSS personalizado -->
    <link rel="stylesheet" href="/css/landing.css" />
    <style>
        /* Importar fuente Inter de Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap');

        /* Estilos generales del body */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f4f4;
        }

        /* Espaciado inferior para el contenido principal */
        .main-content {
            padding-bottom: 5rem !important;
        }

        /* Fuente personalizada para títulos */
        .cardo-title {
            font-family: 'Inter', sans-serif;
        }

        /* Estilos de las tarjetas de precios */
        .pricing-card {
            display: flex;
            flex-direction: column;
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            min-width: 280px;
        }

        /* Efecto hover en las tarjetas */
        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        /* Sombra de color según el tipo de suscripción */
        .pricing-card.básica {
            box-shadow: 0 4px 20px rgba(163, 144, 130, 1);
        }

        .pricing-card.premium {
            box-shadow: 0 4px 20px rgba(146, 116, 71, 1);
        }

        .pricing-card.vip {
            box-shadow: 0 4px 20px rgba(165, 125, 49, 1);
        }

        /* Encabezado de la tarjeta de precios */
        .pricing-header {
            border-radius: 15px 15px 0 0;
            padding: 2rem;
        }

        /* Evitar desbordamiento de texto en encabezados */
        .pricing-header h3,
        .pricing-header .display-4 {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Colores de fondo según el tipo de suscripción */
        .pricing-header.básica {
            background-color: rgba(163, 144, 130, 1);
        }

        .pricing-header.premium {
            background-color: rgba(146, 116, 71, 1);
        }

        .pricing-header.vip {
            background-color: rgba(165, 125, 49, 1);
        }

        /* Estilos de la sección de características */
        .pricing-features {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2rem;
        }

        /* Espaciado para el botón en la parte inferior */
        .pricing-features .text-center {
            margin-top: auto;
        }

        /* Estilos personalizados para los botones */
        .btn-custom {
            border-radius: 50px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        /* Efecto hover en los botones */
        .btn-custom:hover {
            transform: scale(1.05);
        }

        /* Colores de fondo para los botones según el tipo */
        .Básica {
            background-color: rgba(163, 144, 130, 1);
        }

        .Premium {
            background-color: rgba(146, 116, 71, 1);
        }

        .VIP {
            background-color: rgba(165, 125, 49, 1);
        }

        /* Icono de característica */
        .feature-icon {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }

        /* Colores y efectos para los botones según el tipo */
        .btn-custom.Básica {
            background-color: rgba(163, 144, 130, 1);
            color: white;
        }

        .btn-custom.Básica:hover,
        .btn-custom.Básica:focus {
            background-color: rgba(143, 124, 110, 1);
            color: white;
        }

        .btn-custom.Premium {
            background-color: rgba(146, 116, 71, 1);
            color: white;
        }

        .btn-custom.Premium:hover,
        .btn-custom.Premium:focus {
            background-color: rgba(126, 96, 51, 1);
            color: white;
        }

        .btn-custom.VIP {
            background-color: rgba(165, 125, 49, 1);
            color: white;
        }

        .btn-custom.VIP:hover,
        .btn-custom.VIP:focus {
            background-color: rgba(145, 105, 29, 1);
            color: white;
        }
    </style>
</head>

<body>
    <!-- Incluir barra de navegación -->
    <?php include('../navbar.php'); ?>

    <div class="container py-5 main-content">
        <!-- Título principal -->
        <h2 class="cardo-title text-center mb-5">¡Escoge el plan perfecto para ti!</h2>

        <?php
        // Verificar si existen usuarios en la base de datos
        $hay_usuarios = false;
        $sql_check = "SELECT COUNT(*) as total FROM usuarios";
        $res_check = $_conexion->query($sql_check);
        if ($res_check && $row_check = $res_check->fetch_assoc()) {
            $hay_usuarios = $row_check['total'] > 0;
        }
        ?>

        <?php
        // Obtener todas las suscripciones disponibles
        $sql = "SELECT * FROM suscripciones ORDER BY id_suscripcion ASC";
        $suscripciones = $_conexion->query($sql);
        $i = 0;
        ?>
        <?php
        // Verificar si el usuario no está logueado
        $usuario_no_logueado = !isset($_SESSION['usuario']);
        ?>

        <div class="row justify-content-center g-4">
            <!-- Recorrer cada suscripción y mostrar su tarjeta -->
            <?php while ($suscripcion = $suscripciones->fetch_assoc()): ?>
                <?php
                $id_suscripcion = (int)$suscripcion['id_suscripcion'];
                ?>
                <div class="col-12 col-md-6 col-lg-4 d-flex">
                    <div class="card h-100 pricing-card <?= strtolower($suscripcion['nombre']) ?> w-100 d-flex flex-column">
                        <div class="pricing-header <?php echo $suscripcion['nombre'] ?> text-white text-center">
                            <h3 class="mb-0"><?php echo $suscripcion['nombre'] ?></h3>
                            <div class="display-4 fw-bold my-3"><?php echo $suscripcion['precio'] ?>€</div>
                            <p class="mb-0">cada mes</p>
                        </div>
                        <div class="card-body pricing-features d-flex flex-column justify-content-between">
                            <ul class="list-unstyled">
                                <?php
                                // Mostrar el texto de usos del plano según el valor
                                $usos = $suscripcion['max_usos_plano'];
                                if ($usos == -1) {
                                    $texto_uso = "Uso infinito del plano";
                                } elseif ($usos == 1) {
                                    $texto_uso = "1 uso del plano por mes";
                                } else {
                                    $texto_uso = "$usos usos del plano por mes";
                                }
                                ?>
                                <li class="mb-3">
                                    <!-- Icono de check -->
                                    <svg class="feature-icon text-success" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <?php echo $texto_uso ?>
                                </li>
                                <li class="mb-3">
                                    <!-- Icono de check -->
                                    <svg class="feature-icon text-success" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Nuevas ofertas
                                </li>
                                <li class="mb-3">
                                    <?php if ($i === 0): ?>
                                        <!-- Icono de X para la básica -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x text-danger feature-icon" viewBox="0 0 16 16">
                                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                        </svg>
                                        Descuentos especiales
                                    <?php else: ?>
                                        <!-- Icono de check para premium y vip -->
                                        <svg class="feature-icon text-success" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Descuentos especiales
                                    <?php endif; ?>
                                </li>
                                <li class="mb-3">
                                    <?php if ($i < 2): ?>
                                        <!-- Icono de X para básica y premium -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x text-danger feature-icon" viewBox="0 0 16 16">
                                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                        </svg>
                                        Soporte prioritario
                                    <?php else: ?>
                                        <!-- Icono de check para vip -->
                                        <svg class="feature-icon text-success" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Soporte prioritario
                                    <?php endif; ?>
                                </li>
                            </ul>
                            <div class="mt-auto text-center">
                                <!-- Formulario para activar/cambiar suscripción -->
                                <form action="../pasarela-pago/" method="post">
                                    <input type="hidden" name="importe" value="<?php echo number_format((float)$suscripcion['precio'], 2, '.', ''); ?>">
                                    <input type="hidden" name="id_suscripcion" value="<?php echo $id_suscripcion; ?>">
                                    <?php
                                    // Si no hay usuarios o el usuario no está logueado, mostrar enlace a registro
                                    if (!$hay_usuarios || $usuario_no_logueado) {
                                        echo '<a href="/login/usuario/registro_usuario" class="btn btn-custom ' . $suscripcion['nombre'] . ' w-100" style="max-width:220px;">Activar</a>';
                                        echo '<div style="height:1.5em;"></div>';
                                    } else {
                                        // Si la suscripción actual es la misma, mostrar botón deshabilitado
                                        if ($id_suscripcion_usuario === $id_suscripcion) {
                                            echo '<a href="#" class="btn btn-outline-success btn-custom disabled mb-2 w-100" style="max-width:220px;">Activado</a>';
                                            // Si no es la básica, mostrar opción para cancelar
                                            if ($id_suscripcion_usuario != 1) {
                                                echo '<div><a href="#" id="cancelar-suscripcion-btn" class="text-decoration-underline small align-middle" style="cursor:pointer; color: #333;">Cancelar suscripción</a></div>';
                                            }
                                        } else {
                                            // Si es la básica y el usuario tiene otra suscripción, mostrar alerta antes de cancelar
                                            if ($id_suscripcion == 1 && $id_suscripcion_usuario != 1) {
                                                echo '<a href="#" id="activar-basica-btn" class="btn btn-custom Básica w-100" style="max-width:220px;">Activar</a>';
                                            } else {
                                                // Botón para activar la suscripción seleccionada
                                                echo '<button type="submit" class="btn btn-custom ' . $suscripcion['nombre'] . ' w-100" style="max-width:220px;">Activar</button>';
                                            }
                                        }
                                    }
                                    ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $i++; ?>
            <?php endwhile; ?>
        </div>
    </div>
    <!-- Incluir pie de página, cookies y bot -->
    <?php include('../footer.php'); ?>
    <?php include('../cookies.php'); ?>
    <?php include('../udify-bot.php'); ?>

    <!-- Scripts de Bootstrap y SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Botón cancelar suscripción
            const cancelarBtn = document.getElementById('cancelar-suscripcion-btn');
            if (cancelarBtn) {
                cancelarBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Seguro que quieres cancelar?',
                        text: 'Al cancelar tu suscripción perderás el acceso a las funciones premium.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, cancelar',
                        cancelButtonText: 'No, mantener',
                        reverseButtons: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#198754'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/suscripcion/cancelar';
                        }
                    });
                });
            }

            // Botón activar básica (cancela suscripción superior)
            const activarBasicaBtn = document.getElementById('activar-basica-btn');
            if (activarBasicaBtn) {
                activarBasicaBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Seguro que quieres volver a la suscripción básica?',
                        text: 'Esto cancelará tu suscripción actual y pasarás a la básica perdiendo las funciones premium.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, cambiar a básica',
                        cancelButtonText: 'No, mantener actual',
                        reverseButtons: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#198754'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/suscripcion/cancelar';
                        }
                    });
                });
            }
        });
    </script>
    <script>
        <?php if (!empty($_SESSION['mensaje_cancelacion'])): ?>
            // Mostrar notificación de éxito tras cancelar suscripción
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '<?php echo addslashes($_SESSION['mensaje_cancelacion']); ?>',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                background: '#f4e5cc',
                color: '#333',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        <?php unset($_SESSION['mensaje_cancelacion']);
        endif; ?>
    </script>
</body>

</html>