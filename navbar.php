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

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="row w-100 align-items-center">
                <!-- Menu (Left) -->
                <div class="col-md-4 d-flex justify-content-start">
                    <ul class="navbar-nav">
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
                </div>

                <!-- Logo (Center) -->
                <div class="col-md-4 d-flex justify-content-center">
                    <a class="navbar-brand" href="#">
                        <img src="/img/logos/logo-marron-nobg.png" alt="Logo" height="80px" />
                    </a>
                </div>

                <!-- Icons & Search (Right) -->
                <div class="col-md-4 d-flex justify-content-end align-items-center">
                    <div class="search-bar me-3">
                        <!-- Added me-2 for a little spacing -->
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" placeholder="Buscar..."
                                aria-label="Search" aria-describedby="search-addon" width="400px" />
                            <button class="btn btn-dark btn-sm" type="button" id="search-addon">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <a href="/carrito" class="nav-link me-3">
                        <i class="bi bi-cart2 icono-personalizado"></i>
                    </a>
                    <div class="dropdown">
                    <a class="dropdown-toggle text-light text-decoration-none" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                        <?php if (!$datos) { ?>
                            <i class="bi bi-person-circle icono-personalizado"></i>
                        <?php } else { ?>
                            <img src="<?php echo $tipo_sesion === 'usuario' 
                                            ? IMG_USUARIO . $datos['img_usuario'] 
                                            : IMG_USUARIO . $datos['img_proveedor']; ?>" 
                                alt="" width="32" height="32" class="rounded-circle me-2">
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
                                <li><a class="dropdown-item" href="/login/usuario/iniciar_sesion_usuario.php">Cambiar
                                        cuenta</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="/util/funciones/cerrar_sesion.php">Cerrar Sesión</a></li>
                            <?php } elseif ($tipo_sesion === 'proveedor') { ?>
                                <li><a class="dropdown-item" href="/login/proveedor/cambiar_credenciales_proveedor.php">Mi Perfil</a></li>
                                <li><a class="dropdown-item" href="/panel-control/index.php">Panel de control</a></li>
                                <li><a class="dropdown-item" href="/login/usuario/iniciar_sesion_usuario.php">Cambiar
                                        cuenta</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="/util/funciones/cerrar_sesion.php">Cerrar Sesión</a></li>
                            <?php } else { ?>
                                <li><a class="dropdown-item" href="/login/usuario/iniciar_sesion_usuario.php">Iniciar
                                        Sesión</a></li>
                                <li><a class="dropdown-item" href="/login/usuario/registro_usuario.php">Registrarse</a></li>
                            <?php }; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>