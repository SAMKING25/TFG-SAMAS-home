<?php
if (isset($_SESSION['proveedor'])) {
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
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    
    footer {
        margin-top: auto;
        text-align: center;
    }
</style>

<!-- Footer -->
    <footer class="text-white pt-5 pb-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-lg-4 col-xl-4 mb-4">
                    <h6 class="footer-title text-uppercase font-weight-bold mb-4">
                        Sobre nosotros
                    </h6>
                    <p class="">
                        Mucho más que muebles, Somos SAMAS home y operamos en toda la provincia de Málaga haciendo de tu
                        reforma de casa algo más simple y fácil de lograr.
                    </p>
                </div>
                <div class="col-md-4 col-lg-4 col-xl-4 mx-auto mb-4">
                    <h6 class="footer-title text-uppercase font-weight-bold mb-4">
                        Secciones
                    </h6>
                    <p><a href="/productos" class="footer-link">Productos</a></p>
                    <?php if ($tipo_sesion !== 'proveedor') { ?>
                        <p><a href="/plano/confirmacion_plano" class="footer-link">Plano</a></p>
                    <?php } ?>
                    <?php if ($tipo_sesion !== 'proveedor') { ?>
                        <p><a href="/suscripcion" class="footer-link">Suscripción</a></p>
                    <?php } else { ?>
                        <p><a href="/panel-control" class="footer-link">Panel de control</a></p>
                    <?php } ?>					
                    <p><a href="/contacto" class="footer-link">Contacto</a></p>
                    <p><a href="./util/archivos/politica-cookies" target="_blank" id="politica-cookies"
                            class="footer-link">Política de cookies</a></p>
                </div>
                <div class="col-md-4 col-lg-4 col-xl-4 mb-md-0 mb-4">
                    <h6 class="footer-title text-uppercase font-weight-bold mb-4">
                        Contacto
                    </h6>
                    <p><i class="fas fa-home me-2"></i>Málaga, Andalucía, España</p>
                    <p><i class="fas fa-envelope me-2"></i>samashome1@gmail.com</p>
                    <p><i class="fas fa-phone me-2"></i>+34 645 867 244</p>
                </div>
            </div>
            <div class="footer-copyright text-center font-weight-bold py-3">
                © 2025
                <a href="/" class="text-white">SAMAS home</a>
            </div>
    </footer>