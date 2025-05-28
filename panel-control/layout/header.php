<?php $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
// Consulta SQL
$id_proveedor = $_SESSION['proveedor'];

$sql = $_conexion->prepare("SELECT * FROM proveedores WHERE id_proveedor = ?");
$sql->bind_param("i", $id_proveedor);
$sql->execute();
$resultado = $sql->get_result();

define('IMG_USUARIO', '/img/usuario/');
define('USUARIO', '/login/');
define('FUNCIONES', '/util/funciones/')

?>

<header class="p-3 text-white row align-items-center" style="background-color:#381D12;">
  <div class="col-2 col-sm-2  col-lg-1 d-flex align-items-center justify-content-center">
    <a class="btn btn-outline-light col-10" data-bs-toggle="offcanvas" href="#offcanvasExample" aria-controls="offcanvasExample">
      <i class="bi bi-list"></i>
    </a>
  </div>
  <a href="/" class="text-decoration-none col-4 col-sm-5 col-lg-8 h4 align-items-center d-flex">
    <div class="d-flex align-items-center">
      <img src="/img/logos/loguito_gris.png" alt="Logo" height="40px" class="me-2" />
      <span>Panel de control</span>
    </div>
  </a>
  <div class="dropdown text-end col-6 col-sm-5 col-lg-3 justify-content-center">
    <a href="#" class=" align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1"
      data-bs-toggle="dropdown" aria-expanded="false">
      <?php while ($fila = $resultado->fetch_assoc()) { ?>
        <img src="<?php echo IMG_USUARIO . $fila['img_proveedor'] ?>" alt="" width="32" height="32"
          class="rounded-circle me-2" style="object-fit: cover; aspect-ratio: 1 / 1;">
        <strong>
          <?php echo $fila['nombre_proveedor'] ?>
        </strong>
      <?php } ?>
    </a>
    <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser1">
      <li><a class="dropdown-item" href="<?php echo USUARIO ?>proveedor/cambiar_credenciales_proveedor">Mi Perfil</a>
      </li>
      <li><a class="dropdown-item" href="<?php echo USUARIO ?>proveedor/iniciar_sesion_proveedor">Cambiar de
          cuenta</a></li>
      <li>
        <hr class="dropdown-divider">
      </li>
      <li><a class="dropdown-item" href="<?php echo FUNCIONES ?>cerrar_sesion">Salir</a></li>
    </ul>
  </div>
</header>