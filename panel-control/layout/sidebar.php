<?php
// Obtener la URL actual
$current_url = $_SERVER['REQUEST_URI'];
define('BASE_URL', '/panel-control/');
?>

<style>
    hr {
        color: rgb(81, 53, 19) !important;
        opacity: 0.5;
    }

    .offcanvas {
        z-index: 1100 !important;
        box-shadow: 2px 0 12px rgba(81, 53, 19, 0.08);
        border-radius: 18px 0 0 18px;
    }

    .sidebar {
        background: linear-gradient(180deg, #f8f5f2 80%, #e9e2d8 100%);
        min-height: 100vh;
        border-radius: 18px 0 0 18px;
        box-shadow: 2px 0 12px rgba(81, 53, 19, 0.10);
    }

    .active {
        background-color: rgb(81, 53, 19) !important;
        color: white !important;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(81, 53, 19, 0.08);
        font-weight: 500;
    }

    .nav-link.active:hover {
        background-color: rgb(100, 70, 30) !important;
        color: #fff !important;
        transition: background 0.2s, color 0.2s;
    }

    .nav-link {
        border-radius: 8px;
        margin-bottom: 4px;
        transition: background 0.2s, color 0.2s;
        color: rgb(81, 53, 19) !important;
        font-size: 1.05rem;
        padding: 0.65rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .nav-link:hover {
        background-color: #f1e7db !important;
        color: rgb(81, 53, 19) !important;
        box-shadow: 0 1px 4px rgba(81, 53, 19, 0.06);
    }

    .nav-link i {
        color: rgb(81, 53, 19) !important;
        font-size: 1.2rem;
        transition: color 0.2s;
    }

    .nav-link.active,
    .nav-link.active * {
        color: #fff !important;
    }

    .sidebar img {
        border-radius: 0;
        box-shadow: none;
        background: transparent;
        width: 72px;
        /* Aumenta el tamaño de la imagen */
        height: 72px;
        object-fit: contain;
        margin-right: 1.2rem;
        /* Más separación respecto al texto */
    }

    .sidebar-logo {
        display: flex;
        align-items: center;
        gap: 1.2rem;
        margin-bottom: 2.2rem;
        margin-top: 0.5rem;
        text-decoration: none !important;
    }

    .sidebar-title {
        /* font-family: 'Playfair Display', 'Georgia', serif; */
        /* Fuente elegante */
        font-size: 1.7rem;
        /* Más grande */
        font-weight: 700;
        color: rgb(81, 53, 19);
        letter-spacing: 1.5px;
        line-height: 1.25;
        margin-left: 0.2rem;
        margin-top: 0.2rem;
        text-shadow: 0 1px 0 #f8f5f2;
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .fs-4 {
        font-weight: 600;
        letter-spacing: 0.5px;
    }
</style>
<div class="d-flex">
    <div class="d-flex flex-column flex-shrink-0 p-3 bg-light sidebar offcanvas offcanvas-start" id="offcanvasExample"
        style="width: 280px;overflow-y: auto;">
        <a href="/index.php" class="sidebar-logo">
            <img src="/img/logos/logo-negro-nobg.png" alt="SAMAS home">
            <span class="sidebar-title">SAMAS<br>home</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>index.php"
                    class="nav-link <?php echo ($current_url === BASE_URL) ? 'active' : 'link-dark'; ?>">
                    <i class="bi bi-house-door me-2"></i>
                    Inicio
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>productos/"
                    class="nav-link <?php echo (strpos($current_url, '/panel-control/productos/') !== false && strpos($current_url, '/panel-control/productos/nuevo_producto') === false) ? 'active' : 'link-dark'; ?>">
                    <i class="bi bi-archive me-2"></i>
                    Gestión de productos
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>productos/nuevo_producto"
                    class="nav-link <?php echo (strpos($current_url, '/panel-control/productos/nuevo_producto') !== false) ? 'active' : 'link-dark'; ?>">
                    <i class="bi bi-folder-plus me-2"></i>
                    Nuevo producto
                </a>
            </li>
            <li>
                <a href="/util/funciones/cerrar_sesion.php" class="nav-link link-dark">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Cerrar sesión
                </a>
            </li>
        </ul>
        <hr>
    </div>