<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<?php
$_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];

define('IMG_USUARIO', '/img/usuario/');
define('FUNCIONES', '/util/funciones/');

$tipo_sesion = null;
$datos = null;

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

<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
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
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/productos">Productos</a>
                </li>
                <?php if ($tipo_sesion !== 'proveedor') { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/plano">Plano</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/suscripcion">Suscripción</a>
                    </li>
                <?php } else { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/panel-control">Panel de control</a>
                    </li>
                <?php } ?>
                <li class="nav-item">
                    <a class="nav-link" href="/contacto">Contacto</a>
                </li>
            </ul>
            <!-- Iconos a la derecha -->
            <div class="d-flex align-items-center ms-auto">
                <div class="me-3" style="font-size: 1rem;">
                    <a href="/productos?focus=1" title="Ir a productos" class="text-white nav-link">
                        <i class="bi bi-search icono-personalizado"></i>
                    </a>
                </div>
                <?php if ($tipo_sesion !== 'proveedor') { ?>
                    <a href="/carrito" class="nav-link me-3">
                        <i class="bi bi-cart2 icono-personalizado"></i>
                    </a>
                <?php } ?>
                <div class="dropdown">
                    <a class="dropdown-toggle text-light text-decoration-none" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <?php if (!$datos) { ?>
                            <i class="bi bi-person-circle icono-personalizado"></i>
                        <?php } else { ?>
                            <img src="<?php echo $tipo_sesion === 'usuario'
                                            ? IMG_USUARIO . $datos['img_usuario']
                                            : IMG_USUARIO . $datos['img_proveedor']; ?>"
                                alt="" width="32" height="32" class="rounded-circle me-2"
                                style="object-fit: cover; aspect-ratio: 1 / 1;">
                            <strong>
                                <?php echo $tipo_sesion === 'usuario'
                                    ? $datos['nombre_usuario']
                                    : $datos['nombre_proveedor']; ?>
                            </strong>
                        <?php } ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php if ($tipo_sesion === 'usuario') { ?>
                            <li><a class="dropdown-item" href="/login/usuario/cambiar_credenciales_usuario.php">Mi Perfil</a></li>
                            <li><a class="dropdown-item" href="/login/usuario/iniciar_sesion_usuario.php">Cambiar cuenta</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="/util/funciones/cerrar_sesion.php">Cerrar Sesión</a></li>
                        <?php } elseif ($tipo_sesion === 'proveedor') { ?>
                            <li><a class="dropdown-item" href="/login/proveedor/cambiar_credenciales_proveedor">Mi Perfil</a></li>
                            <li><a class="dropdown-item" href="/panel-control/">Panel de control</a></li>
                            <li><a class="dropdown-item" href="/login/usuario/iniciar_sesion_usuario">Cambiar cuenta</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="/util/funciones/cerrar_sesion">Cerrar Sesión</a></li>
                        <?php } else { ?>
                            <li><a class="dropdown-item" href="/login/usuario/iniciar_sesion_usuario">Iniciar Sesión</a></li>
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