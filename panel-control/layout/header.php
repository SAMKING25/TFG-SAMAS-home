<?php $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    // Consulta SQL
    $id_proveedor = $_SESSION['usuario'];

    $sql = $_conexion->prepare("SELECT * FROM proveedores WHERE id_proveedor = ?");
    $sql->bind_param("i", $id_proveedor);
    $sql->execute();
    $resultado = $sql->get_result();
    
    define('IMG_USUARIO','/img/usuario/');
    define('USUARIO','/login/');
    define('FUNCIONES','/util/funciones/')

?>

<header class="p-3 text-white d-flex" style="background-color:#381D12;">
  <div class="col-12 col-md-9 h4" style="padding-left: 280px;">
    Panel de control
  </div>
  <div class="dropdown text-end col-12 col-md-3 justify-content-center">
    <a href="#" class=" align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1"
      data-bs-toggle="dropdown" aria-expanded="false">
      <?php while ($fila = $resultado->fetch_assoc()) { ?>
      <img src="<?php echo IMG_USUARIO.$fila['foto_proveedor']?>" alt="" width="32" height="32"
        class="rounded-circle me-2">
      <strong>
        <?php echo $fila['nombre_proveedor']?>
      </strong>
      <?php } ?>
    </a>
    <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser1">
      <li><a class="dropdown-item" href="<?php echo USUARIO?>proveedor/cambiar_credenciales_proveedor.php">Mi Perfil</a>
      </li>
      <li><a class="dropdown-item" href="<?php echo USUARIO?>proveedor/iniciar_sesion_proveedor.php">Cambiar de
          cuenta</a></li>
      <li>
        <hr class="dropdown-divider">
      </li>
      <li><a class="dropdown-item" href="<?php echo FUNCIONES?>cerrar_sesion.php">Salir</a></li>
    </ul>
  </div>
</header>