<?php
// Obtener la URL actual
    $current_url = $_SERVER['REQUEST_URI'];
    define('BASE_URL','/TFG-SAMAS-home/panel-control/tienda/');
?>

<div class="d-flex">
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-light sidebar" style="width: 280px; position: fixed; top: 0; left: 0; height: 100vh; overflow-y: auto;">
        <a href="/TFG-SAMAS-home/panel-control/tienda/index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
            <img src="/TFG-SAMAS-home/panel-control/tienda/imagenes/samas-home-logo.png" class="w-50" alt="">
            <span class="fs-4">SAMAS home</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>index.php" class="nav-link  <?php echo (strpos($current_url, '/panel-control/tienda/index.php') !== false) ? 'active' : 'link-dark'; ?>">
                    <i class="bi bi-house-door me-2"></i>
                    Inicio
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>productos/index.php" class="nav-link  <?php echo (strpos($current_url, '/panel-control/tienda/productos/index.php') !== false) ? 'active' : 'link-dark'; ?>">
                    <i class="bi bi-archive me-2"></i>
                    Gestión de productos
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>productos/nuevo_producto.php" class="nav-link  <?php echo (strpos($current_url, '/panel-control/tienda/productos/nuevo_producto.php') !== false) ? 'active' : 'link-dark'; ?>">
                    <i class="bi bi-folder-plus me-2"></i>
                    Nuevo producto
                </a>
            </li>
            <li>
                <a href="#" class="nav-link link-dark">
                    <i class="bi bi-cart me-2"></i>
                    Products
                </a>
            </li>
            <li>
                <a href="#" class="nav-link link-dark">
                    <i class="bi bi-gear me-2"></i>
                    Ajustes
                </a>
            </li>
        </ul>
        <hr>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="https://github.com/justmanuva.png" alt="" width="32" height="32" class="rounded-circle me-2">
                <strong>justmanuva</strong>
            </a>
            <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser2">
                <li><a class="dropdown-item" href="#">New project...</a></li>
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li><a class="dropdown-item" href="#">Profile</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="#">Sign out</a></li>
            </ul>
        </div>
    </div>