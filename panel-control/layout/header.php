<?php $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
// Consulta SQL
$id_proveedor = $_SESSION['proveedor'];

$sql = $_conexion->prepare("SELECT * FROM proveedores WHERE id_proveedor = ?");
$sql->bind_param("i", $id_proveedor);
$sql->execute();
$resultado = $sql->get_result();

define('IMG_PROVEEDOR', '/img/proveedor/');
define('USUARIO', '/login/');
define('FUNCIONES', '/util/funciones/');
define('INICIO', '/');

?>
<style>
  /* Navbar madera profesional y translúcido */
  .navbar-madera {
    background: rgba(44, 27, 13, 0.93) !important;
    backdrop-filter: blur(4px);
    box-shadow: 0 2px 12px rgba(44, 27, 13, 0.10);
    transition: box-shadow 0.2s;
    min-height: 48px;
  }

  .navbar-madera * {
    color: #fff !important;
    fill: #fff !important;
    border-color: #fff !important;
  }

  .navbar-madera .btn-outline-light {
    border-color: #fff !important;
    color: #fff !important;
    background: transparent !important;
  }

  .navbar-madera .btn-outline-light:hover,
  .navbar-madera .btn-outline-light:focus {
    background: rgba(255, 255, 255, 0.12) !important;
    color: #fff !important;
    border-color: #fff !important;
  }

  /* Avatar estilo navbar */
  .navbar-madera .rounded-circle {
    border: none !important;
    background: none !important;
  }

  /* Menú desplegable claro */
  .navbar-madera-dropdown {
    background: #fff !important;
    border: none;
    color: #222 !important;
    border-radius: 14px !important;
    box-shadow: 0 4px 24px rgba(44, 27, 13, 0.10), 0 1.5px 6px rgba(44, 27, 13, 0.06) !important;
    padding: 0.5rem 0 !important;
    min-width: 200px;
  }

  .navbar-madera-dropdown .dropdown-item {
    color: #3a2a17 !important;
    font-size: 1.07rem;
    padding: 0.65rem 1.25rem;
    border-radius: 6px;
    transition: background 0.18s, color 0.18s;
    margin: 0 0.25rem;
  }

  .navbar-madera-dropdown .dropdown-item:hover,
  .navbar-madera-dropdown .dropdown-item:focus {
    background: #f8f5f2 !important;
    color: #513513 !important;
  }

  .navbar-madera-dropdown .dropdown-divider {
    margin: 0.35rem 0;
    border-top: 1.5px solid #eee;
  }
</style>

<link rel="stylesheet" href="/panel-control/css/panel.css">

<header class="sticky-top navbar-madera px-3 py-2 d-flex align-items-center justify-content-between"
  style="z-index:1050;">
  <div class="d-flex align-items-center gap-2">
    <a class="btn btn-outline-light d-flex align-items-center justify-content-center me-2" data-bs-toggle="offcanvas"
      href="#offcanvasExample" aria-controls="offcanvasExample">
      <i class="bi bi-list fs-4"></i>
    </a>
    <a href="/" class="text-decoration-none d-flex align-items-center gap-2">
      <img src="/img/logos/loguito_gris.png" alt="Logo" height="38" class="me-1" />
      <span class="fw-bold fs-5 text-white">SAMAS home</span>
    </a>
  </div>
  <div class="dropdown text-end">
    <a href="#" class="align-items-center text-white text-decoration-none dropdown-toggle d-flex" id="dropdownUser1"
      data-bs-toggle="dropdown" aria-expanded="false">
      <?php while ($fila = $resultado->fetch_assoc()) { ?>
        <img src="<?php echo IMG_PROVEEDOR . $fila['img_proveedor'] ?>" alt="" width="36" height="36"
          class="rounded-circle me-2" style="object-fit: cover; aspect-ratio: 1 / 1;">
        <strong class="d-none d-md-inline text-white">
          <?php echo $fila['nombre_proveedor'] ?>
        </strong>
      <?php } ?>
    </a>
    <ul class="dropdown-menu dropdown-menu-end text-small shadow navbar-madera-dropdown"
      aria-labelledby="dropdownUser1">
      <li>
        <a class="dropdown-item" href="<?php echo INICIO ?>">Volver a inicio</a>
      </li>
      <li>
        <a class="dropdown-item" href="<?php echo USUARIO ?>proveedor/cambiar_credenciales_proveedor">Ajustes de perfil</a>
      </li>
      <li>
        <a class="dropdown-item" href="<?php echo USUARIO ?>proveedor/iniciar_sesion_proveedor">Cambiar de cuenta</a>
      </li>
      <li>
        <hr class="dropdown-divider">
      </li>
      <li>
        <a class="dropdown-item" href="<?php echo FUNCIONES ?>cerrar_sesion">Cerrar sesión</a>
      </li>
    </ul>
  </div>
</header>