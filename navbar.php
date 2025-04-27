<?php $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];

if (isset($_SESSION['usuario'])) {
    $id_usuario = $_SESSION['usuario'];
}

$sql = $_conexion->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$sql->bind_param("i", $id_usuario);
$sql->execute();
$resultado_usuario = $sql->get_result();

define('IMG_USUARIO', '/img/usuario/');
define('FUNCIONES', '/util/funciones/');
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
                            <a class="nav-link" href="#">Productos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/plano">Plano</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/suscripcion">Suscripción</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Contacto</a>
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
                    <div class="search-bar me-2">
                        <!-- Added me-2 for a little spacing -->
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" placeholder="Buscar..."
                                aria-label="Search" aria-describedby="search-addon" width="400px" />
                            <button class="btn btn-dark btn-sm" type="button" id="search-addon">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <a href="#" class="nav-link">
                        <i class="bi bi-cart2 icono-personalizado"></i>
                    </a>
                    <div class="dropdown">
                        <a class="dropdown-toggle text-light text-decoration-none" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php if (!isset($_SESSION['usuario'])) { ?>
                                <i class="bi bi-person-circle icono-personalizado"></i>
                            <?php } ?>
                            <?php while ($fila = $resultado_usuario->fetch_assoc()) { ?>
                                <img src="<?php echo IMG_USUARIO . $fila['foto_usuario'] ?>" alt="" width="32" height="32" class="rounded-circle me-2">
                                <strong><?php echo $fila['nombre_usuario'] ?></strong>
                            <?php } ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if (isset($_SESSION['usuario']) && !empty($_SESSION['usuario'])) { ?>
                                <li><a class="dropdown-item" href="/login/usuario/cambiar_credenciales_usuario.php">Mi Perfil</a></li>
                                <li><a class="dropdown-item" href="/login/usuario/iniciar_sesion_usuario.php">Cambiar cuenta</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="/util/funciones/cerrar_sesion.php">Cerrar Sesión</a></li>
                            <?php } else { ?>
                                <li><a class="dropdown-item" href="/login/usuario/iniciar_sesion_usuario.php">Iniciar Sesión</a></li>
                                <li><a class="dropdown-item" href="/login/usuario/registro_usuario.php">Registrarse</a></li>
                            <?php }; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- End Navbar -->