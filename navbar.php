<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<?php
// Guarda la URL actual para redirigir después del login
$_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];

// Definiciones de rutas de imágenes y utilidades
define('IMG_USUARIO', '/img/usuario/');
define('IMG_PROVEEDOR', '/img/proveedor/');
define('FUNCIONES', '/util/funciones/');

$tipo_sesion = null;
$datos = null;

// Detecta si hay sesión de usuario o proveedor y obtiene sus datos
if (isset($_SESSION['usuario'])) {
    $id = $_SESSION['usuario'];
    $tipo_sesion = 'usuario';
    $sql = $_conexion->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
    $sql->bind_param("i", $id);
    $sql->execute();
    $resultado = $sql->get_result();
    $datos = $resultado->fetch_assoc();
} elseif (isset($_SESSION['proveedor'])) {
    $id = $_SESSION['proveedor'];
    $tipo_sesion = 'proveedor';
    $sql = $_conexion->prepare("SELECT * FROM proveedores WHERE id_proveedor = ?");
    $sql->bind_param("i", $id);
    $sql->execute();
    $resultado = $sql->get_result();
    $datos = $resultado->fetch_assoc();
}
?>

<style>
    /* Estilos para el menú desplegable del navbar */
    .dropdown-menu {
        background-color: #fff !important;
        color: #000 !important;
    }

    .dropdown-menu .dropdown-item {
        color: #000 !important;
    }

    .dropdown-menu .dropdown-item:hover {
        background-color: #f1f1f1 !important;
        color: #000 !important;
    }
</style>

<!-- Navbar principal -->
<nav
    class="navbar navbar-expand-lg fixed-top<?php echo (isset($navbar_home) && $navbar_home) ? ' navbar-home' : ''; ?>">
    <div class="container">
        <!-- Logo a la izquierda -->
        <a class="navbar-brand d-flex align-items-center" href="/">
            <img src="/img/logos/loguito_gris.png" alt="Logo" height="40px" class="me-2" />
            <span class="fw-bold" style="font-size: 1.3rem; letter-spacing: 2px;">SAMAS HOME</span>
        </a>

        <!-- Botón hamburguesa para responsive -->
        <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars fa-lg"></i>
        </button>

        <!-- Menú centrado -->
        <div class="collapse navbar-collapse rounded-bottom" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link subraya util-nav-icons" href="/">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link subraya util-nav-icons" href="/productos">Productos</a>
                </li>
                <?php if ($tipo_sesion !== 'proveedor') { ?>
                    <li class="nav-item">
                        <a class="nav-link subraya util-nav-icons" href="/plano">Plano</a>
                    </li>
                <?php } ?>
                <?php if ($tipo_sesion !== 'proveedor') { ?>
                    <li class="nav-item">
                        <a class="nav-link subraya util-nav-icons" href="/suscripcion">Suscripción</a>
                    </li>
                <?php } else { ?>
                    <li class="nav-item">
                        <a class="nav-link subraya util-nav-icons" href="/panel-control">Panel de control</a>
                    </li>
                <?php } ?>
                <li class="nav-item">
                    <a class="nav-link subraya util-nav-icons" href="/contacto">Contacto</a>
                </li>
            </ul>
            <!-- Iconos a la derecha -->
            <div class="d-flex align-items-center ms-auto navbar-icons">
                <div class="me-3" style="font-size: 1rem;">
                    <a href="/productos?focus=1" title="Ir a productos" class="text-white nav-link icon-grow">
                        <i class="bi bi-search util-nav-icons"></i>
                    </a>
                </div>
                <?php if ($tipo_sesion !== 'proveedor') { ?>
                    <a href="/carrito" class="nav-link me-3 icon-grow">
                        <i class="bi bi-cart2 util-nav-icons"></i>
                    </a>
                <?php } ?>
                <div class="dropdown">
                    <a class="dropdown-toggle text-light text-decoration-none" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <?php if (!$datos) { ?>
                            <i class="bi bi-person-circle util-nav-icons"></i>
                        <?php } else { ?>
                            <img src="<?php echo $tipo_sesion === 'usuario'
                                            ? IMG_USUARIO . $datos['img_usuario']
                                            : IMG_PROVEEDOR . $datos['img_proveedor']; ?>" alt="" width="32" height="32"
                                class="rounded-circle me-2" style="object-fit: cover; aspect-ratio: 1 / 1;">
                            <strong class="util-nav-icons">
                                <?php echo $tipo_sesion === 'usuario'
                                    ? $datos['nombre_usuario']
                                    : $datos['nombre_proveedor']; ?>
                            </strong>
                        <?php } ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php if ($tipo_sesion === 'usuario') { ?>
                            <li><a class="dropdown-item" href="/login/usuario/cambiar_credenciales_usuario">Ajustes de
                                    perfil</a></li>
                            <li><a class="dropdown-item" href="/pedidos/">Mis pedidos</a></li>
                            <li><a class="dropdown-item" href="/login/usuario/iniciar_sesion_usuario">Cambiar de cuenta</a>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="/util/funciones/cerrar_sesion">Cerrar sesión</a></li>
                        <?php } elseif ($tipo_sesion === 'proveedor') { ?>
                            <li><a class="dropdown-item" href="/login/proveedor/cambiar_credenciales_proveedor">Ajustes de
                                    perfil</a></li>
                            <li><a class="dropdown-item" href="/panel-control/">Panel de control</a></li>
                            <li><a class="dropdown-item" href="/login/usuario/iniciar_sesion_usuario">Cambiar de cuenta</a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="/util/funciones/cerrar_sesion">Cerrar sesión</a></li>
                        <?php } else { ?>
                            <li><a class="dropdown-item" href="/login/usuario/iniciar_sesion_usuario">Iniciar sesión</a>
                            </li>
                            <li><a class="dropdown-item" href="/login/usuario/registro_usuario">Registrarse</a></li>
                        <?php }; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
<!-- Pop-up de cookies incluido-->
<?php include('cookies.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
// Si el usuario NO es proveedor y NO tiene suscripción VIP, muestra avisos y controles especiales para el plano
if ($tipo_sesion !== 'proveedor' && (!isset($datos['id_suscripcion']) || $datos['id_suscripcion'] != 3)) { ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Detecta si la URL es exactamente /plano o termina con /plano/
            if (window.location.pathname === '/plano' || window.location.pathname === '/plano/') {
                Swal.fire({
                    title: "¿Estás seguro?",
                    text: "¿Quieres ir al plano? Se perderá un uso del plano disponible.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Sí, ir al plano",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('/util/funciones/sumar_uso_plano', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Permite el acceso, no hace nada porque ya está en /plano
                                } else if (data.limit) {
                                    Swal.fire("Límite alcanzado", "Has alcanzado el máximo de usos del plano para tu suscripción este mes.", "info")
                                        .then(() => {
                                            window.location.href = '/'; // Redirige a inicio u otra página
                                        });
                                } else {
                                    Swal.fire("Error", "No se pudo registrar el uso del plano.", "error")
                                        .then(() => {
                                            window.location.href = '/';
                                        });
                                }
                            })
                            .catch(() => {
                                Swal.fire("Error", "No se pudo registrar el uso del plano.", "error")
                                    .then(() => {
                                        window.location.href = '/';
                                    });
                            });
                    } else {
                        window.location.href = '/'; // Redirige si cancela
                    }
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Solo si estamos en /plano o /plano/
            if (window.location.pathname === '/plano' || window.location.pathname === '/plano/') {
                let confirmandoSalida = false; // Para evitar bucles
                // Aviso personalizado al pulsar cualquier enlace del navbar
                document.querySelectorAll('a.nav-link, .dropdown-item, a.navbar-brand').forEach(function(link) {
                    // Ignora enlaces que abren en nueva pestaña
                    if (link.target === '_blank') return;

                    link.addEventListener('click', function(e) {
                        // Si ya estamos confirmando, deja pasar
                        if (confirmandoSalida) return;

                        // Si el enlace es a la misma página, recarga, o a otra, muestra el modal
                        e.preventDefault();
                        Swal.fire({
                            title: "¿Quieres salir del plano?",
                            text: "Si sales del plano, podrías perder los cambios no guardados.",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Sí, salir",
                            cancelButtonText: "Cancelar"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                confirmandoSalida = true;
                                window.removeEventListener('beforeunload', () => {}); // Evita doble aviso
                                // Si es recarga (href actual), recarga, si no, navega
                                if (link.href === window.location.href) {
                                    window.location.reload();
                                } else {
                                    window.location.href = link.href;
                                }
                            }
                            // Si cancela, no hace nada
                        });
                    });
                });
            }
        });
    </script>
<?php } ?>