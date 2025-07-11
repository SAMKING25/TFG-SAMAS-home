<?php
// Mostrar todos los errores de PHP para depuración
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Incluir archivo de conexión a la base de datos
require('../util/conexion.php');
// Iniciar sesión
session_start();

// Verificar si el usuario está logueado, si no redirigir a inicio de sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: /login/usuario/iniciar_sesion_usuario");
    exit;
}

// Obtener el ID del usuario desde la sesión
$id_usuario = $_SESSION['usuario'];

// Obtener todos los pedidos del usuario ordenados por fecha descendente
$sql = "SELECT id_pedido, fecha, total, datos_usuario FROM pedidos WHERE id_usuario = ? ORDER BY fecha DESC";
$stmt = $_conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$pedidos = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mis pedidos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Bootstrap CSS principal -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Iconos de Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <!-- Favicon del sitio -->
    <link id="favicon" rel="shortcut icon" href="/img/logos/loguito_gris.png" />
    <!-- Archivo CSS personalizado -->
    <link rel="stylesheet" href="/css/landing.css" />
    <style>
        /* Estilos generales del body */
        body {
            background: linear-gradient(120deg, #f8f6f2 0%, #f4e5cc 100%);
            font-family: 'Segoe UI', 'Roboto', Arial, sans-serif;
            color: #222;
        }

        /* Contenedor principal de la tabla de pedidos */
        .pedidos-table-container {
            background: #fffbe9;
            border-radius: 2rem;
            box-shadow: 0 4px 24px 0 #bfa16a22;
            padding: 3.5rem 2.5rem;
            margin-top: 6rem;
            margin-bottom: 4rem;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Estilos para las cards de pedidos en móvil */
        .card.mb-3.shadow-sm {
            margin-left: 0.5rem;
            margin-right: 0.5rem;
        }

        /* Tabla de pedidos para escritorio */
        .table-pedidos {
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 2px 12px #bfa16a22;
        }

        .table-pedidos th,
        .table-pedidos td {
            text-align: center;
            vertical-align: middle !important;
        }

        .table-pedidos th {
            color: #fff;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            border: none;
            font-weight: 700;
            height: 56px;
        }

        .table-pedidos td {
            background: #fff;
            font-size: 1.08rem;
            border: none;
            height: 56px;
        }

        /* Icono de pedido */
        .pedido-icon {
            color: #b88c4a;
            font-size: 1.5rem;
            margin: 0 !important;
            vertical-align: middle;
        }

        /* Colores de encabezados de tabla */
        .th-pedido {
            background: #c8ad7f !important;
        }

        .th-fecha {
            background: #eec06b !important;
        }

        .th-total {
            background: #ffd88a !important;
        }

        .th-datos {
            background: #d6b77b !important;
        }

        /* Botón de ver pedido */
        .btn-eye-pedido {
            background: rgb(233, 215, 192);
            color: #fff !important;
            border: none;
            border-radius: 50%;
            width: 2.5rem;
            height: 2.5rem;
            transition: background 0.2s, transform 0.15s;
            box-shadow: 0 2px 8px #bfa16a33;
            font-size: 1.2rem;
            padding: 0;
        }

        .btn-eye-pedido:hover,
        .btn-eye-pedido:focus {
            background: rgb(143, 99, 34);
            color: #fff !important;
            transform: scale(1.08);
        }

        /* Responsive para móvil */
        @media (max-width: 600px) {
            .pedidos-table-container {
                padding: 1rem 0.3rem;
            }

            .table-pedidos th,
            .table-pedidos td {
                font-size: 0.98rem;
            }
        }

        @media (max-width: 700px) {
            .pedidos-table-container {
                padding: 1.5rem 0.7rem !important;
            }

            .card.mb-3.shadow-sm {
                margin-left: 0.2rem;
                margin-right: 0.2rem;
            }

            .card.flex-row {
                flex-direction: column !important;
                align-items: stretch !important;
                text-align: center;
                gap: 0.5rem;
            }

            .card.flex-row>div {
                min-width: 0 !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            .d-md-flex {
                display: none !important;
            }
        }

        @media (min-width: 768px) {
            .d-md-none {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <!-- Incluir barra de navegación -->
    <?php include('../navbar.php'); ?>
    <div class="container pedidos-table-container">
        <h2 class="mb-4 fw-bold" style="color:#b88c4a; letter-spacing:1px;">Mis pedidos</h2>
        <?php if ($pedidos->num_rows > 0): ?>
            <!-- Vista cards para móvil -->
            <div class="d-md-none">
                <?php
                // Reiniciar el puntero del resultado para volver a recorrerlo
                $stmt->execute();
                $pedidos = $stmt->get_result();
                ?>
                <?php while ($pedido = $pedidos->fetch_assoc()): ?>
                    <?php
                    // Decodificar los datos del usuario si existen
                    $datos = [];
                    if (!empty($pedido['datos_usuario'])) {
                        $datos = json_decode($pedido['datos_usuario'], true);
                    }
                    ?>
                    <div class="card mb-3 shadow-sm" style="border-radius: 1.2rem; background: #fffbe9;">
                        <div class="card-body py-3 px-3">
                            <div class="d-flex align-items-center mb-2">
                                <span class="d-inline-flex align-items-center justify-content-center me-2" style="background:#eec06b; border-radius:50%; width:2.2rem; height:2.2rem;">
                                    <i class="bi bi-cart pedido-icon" style="font-size:1.1rem; color:#fff; margin:0;"></i>
                                </span>
                                <span class="fw-semibold text-secondary small"><i class="bi bi-calendar3 pedido-icon"></i> <?php echo date('d/m/Y H:i', strtotime($pedido['fecha'])); ?></span>
                            </div>
                            <div class="mb-2">
                                <div class="fw-semibold text-secondary small mb-1"><i class="bi bi-person pedido-icon"></i> Datos</div>
                                <?php if (!empty($datos)): ?>
                                    <div class="text-muted small">
                                        <strong><?php echo htmlspecialchars($datos['nombre'] ?? ''); ?></strong><br>
                                        <?php echo htmlspecialchars($datos['apellidos'] ?? ''); ?><br>
                                        <?php echo htmlspecialchars($datos['email'] ?? ''); ?><br>
                                        <?php echo htmlspecialchars($datos['telefono'] ?? ''); ?><br>
                                        <?php echo htmlspecialchars($datos['direccion'] ?? ''); ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">No disponible</span>
                                <?php endif; ?>
                            </div>
                            <div class="mb-2">
                                <span class="fw-semibold text-secondary small"><i class="bi bi-cash-coin pedido-icon"></i> Total:</span>
                                <span class="fw-bold" style="color:#b88c4a;"><?php echo number_format($pedido['total'], 2); ?> €</span>
                            </div>
                            <div class="text-center mt-2">
                                <a href="ver_pedido?id_pedido=<?php echo $pedido['id_pedido']; ?>" title="Ver pedido"
                                    class="btn btn-eye-pedido d-inline-flex align-items-center justify-content-center mx-auto">
                                    <i class="bi bi-eye pedido-icon" style="font-size:1.2rem; margin:0;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <!-- Vista horizontal para escritorio -->
            <div class="d-none d-md-flex flex-column gap-4">
                <?php
                // Reiniciar el puntero del resultado para escritorio
                $stmt->execute();
                $pedidos = $stmt->get_result();
                ?>
                <?php while ($pedido = $pedidos->fetch_assoc()): ?>
                    <?php
                    // Decodificar los datos del usuario si existen
                    $datos = [];
                    if (!empty($pedido['datos_usuario'])) {
                        $datos = json_decode($pedido['datos_usuario'], true);
                    }
                    ?>
                    <div class="card shadow-sm flex-row align-items-center p-3" style="border-radius: 1.2rem; background: #fffbe9;">
                        <div class="d-flex align-items-center justify-content-center" style="min-width:70px;">
                            <span class="d-inline-flex align-items-center justify-content-center mx-2" style="background:#eec06b; border-radius:50%; width:2.5rem; height:2.5rem;">
                                <i class="bi bi-cart pedido-icon" style="font-size:1.3rem; color:#fff; margin:0;"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 px-2" style="min-width:120px;">
                            <div class="fw-semibold text-secondary small mb-1">
                                <i class="bi bi-person pedido-icon"></i> Datos
                            </div>
                            <?php if (!empty($datos)): ?>
                                <div class="text-muted small">
                                    <strong><?php echo htmlspecialchars($datos['nombre'] ?? ''); ?></strong><br>
                                    <?php echo htmlspecialchars($datos['apellidos'] ?? ''); ?><br>
                                    <?php echo htmlspecialchars($datos['email'] ?? ''); ?><br>
                                    <?php echo htmlspecialchars($datos['telefono'] ?? ''); ?><br>
                                    <?php echo htmlspecialchars($datos['direccion'] ?? ''); ?>
                                </div>
                            <?php else: ?>
                                <span class="text-muted">No disponible</span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-grow-1 px-2" style="min-width:120px;">
                            <div class="fw-semibold text-secondary small mb-1">
                                <i class="bi bi-calendar3 pedido-icon"></i> Fecha
                            </div>
                            <?php echo date('d/m/Y H:i', strtotime($pedido['fecha'])); ?>
                        </div>
                        <div class="flex-grow-1 px-2" style="min-width:120px;">
                            <div class="fw-semibold text-secondary small mb-1">
                                <i class="bi bi-cash-coin pedido-icon"></i> Total
                            </div>
                            <span class="fw-bold" style="color:#b88c4a;">
                                <?php echo number_format($pedido['total'], 2); ?> €
                            </span>
                        </div>
                        <div class="d-flex align-items-center justify-content-center" style="min-width:90px;">
                            <div class="text-center">
                                <div class="fw-semibold text-secondary small mb-1">Detalles</div>
                                <a href="ver_pedido?id_pedido=<?php echo $pedido['id_pedido']; ?>" title="Ver pedido"
                                    class="btn btn-eye-pedido d-flex align-items-center justify-content-center mx-auto">
                                    <i class="bi bi-eye pedido-icon" style="font-size:1.3rem; margin:0;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <!-- Mensaje si no hay pedidos -->
            <div class="alert alert-info mt-4">No tienes pedidos realizados.</div>
        <?php endif; ?>
    </div>
    <!-- Incluir pie de página, cookies y bot -->
    <?php include('../footer.php'); ?>
    <?php include('../cookies.php'); ?>
    <?php include('../udify-bot.php'); ?>

    <!-- Scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>