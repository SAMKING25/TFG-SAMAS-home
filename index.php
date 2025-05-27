<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>SAMAS HOME</title>
	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
	<link rel="shortcut icon" href="./img/logos/logo-marron-nobg.ico" />
	<!-- Archivo CSS personalizado -->
	<link rel="stylesheet" href="/css/landing.css" />
	<!--search-->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
	<!--conexion con BD-->
	<?php
	error_reporting(E_ALL);
	ini_set("display_errors", 1);

	require('util/conexion.php');

	session_start();
	?>
</head>

<body>
	<?php include('navbar.php'); ?>
	<!-- Sección para la imagen de portada -->
	<div class="col">
		<section class="index-section">
			<div class="section-banner">
				<h1 class="cardo-title">Tu hogar comienza aquí</h1>
				<p class="banner-subtext">
					Encuentra los mejores muebles para tu hogar
				</p>
				<a href="./productos/" class="banner-btn btn btn-dark">
					Ver Productos
				</a>
			</div>
		</section>
	</div>
	<div class="container">
		<!-- Ofertas -->
		<!-- Carrusel -->
		<div class="container mt-5">
			<h2 class="text-center mb-4">Últimas Ofertas</h2>
			<?php
			$sql = "SELECT p.*, o.porcentaje
            FROM productos p
            INNER JOIN ofertas o ON p.id_oferta = o.id_oferta";
			$resultado = $_conexion->query($sql);
			?>
			<div id="carruselOfertas" class="carousel slide" data-bs-ride="carousel">
				<div class="carousel-inner carrusel-inner">
					<?php
					$primero = true;
					while ($producto = $resultado->fetch_assoc()) {
						$precio_original = $producto['precio'];
						$porcentaje_descuento = $producto['porcentaje'];
						$precio_final = $precio_original - ($precio_original * $porcentaje_descuento / 100);
					?>
						<div class="carousel-item <?php if ($primero) {
														echo 'active';
														$primero = false;
													} ?>">
							<a href="./productos/ver_producto.php?id_producto= <?php echo $producto["id_producto"]; ?>"><img src="img/productos/<?php echo $producto['img_producto']; ?>" class="d-block w-100"
									alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
									style="object-fit: cover; height: 100%; width: 100%;"></a>
							<div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
								<h5 class="fs-2">
									<?php echo htmlspecialchars($producto['nombre']); ?>
								</h5>
								<p class="fs-5">¡
									<?php echo $producto['porcentaje']; ?>% de descuento!
								</p>
								<p class="fs-6">
									<span style="text-decoration:line-through; color:grey;">
										<?php echo number_format($precio_original, 2, ',', '.'); ?> €
									</span>
								</p>
								<p class="fs-1 fw-bold">
									<?php echo number_format($precio_final, 2, ',', '.'); ?> €
								</p>
							</div>
						</div>
					<?php } ?>
				</div>

				<!-- Controles -->
				<button class="carousel-control-prev" type="button" data-bs-target="#carruselOfertas"
					data-bs-slide="prev">
					<span class="carousel-control-prev-icon"></span>
					<span class="visually-hidden">Anterior</span>
				</button>
				<button class="carousel-control-next" type="button" data-bs-target="#carruselOfertas"
					data-bs-slide="next">
					<span class="carousel-control-next-icon"></span>
					<span class="visually-hidden">Siguiente</span>
				</button>
			</div>
		</div>

		<!-- Estilos exclusivos para el carrusel -->
		<style>
			/* Aseguramos que solo el carrusel tenga altura y formato adecuado */
			#carruselOfertas .carousel-inner {
				height: 500px;
				/* Fijamos la altura del carrusel */
			}

			#carruselOfertas .carousel-item {
				height: 100%;
			}

			#carruselOfertas img {
				object-fit: cover;
				height: 100%;
				width: 100%;
			}

			/* Estilo específico para la descripción del carrusel */
			#carruselOfertas .carousel-caption {
				bottom: 20px;
				/* Ajustamos la posición de la descripción */
				padding: 10px;
				background-color: rgba(0, 0, 0, 0.5);
				/* Fondo oscuro y semitransparente */
				border-radius: 8px;
			}
		</style>



		<!-- Categorias -->
		<div class="text-center mt-5">
			<h2>Categorías</h2>
		</div>
		<!-- Pedimos a la BD todas las categorias -->
		<?php
		$sql = "SELECT * FROM categorias";
		$categorias = $_conexion->query($sql);
		?>
		<!-- While de todas las categorias -->
		<div class="container row mt-4">
			<?php
			// if (isset($_SESSION['usuario'])) {
			while ($categoria = $categorias->fetch_assoc()) { ?>
				<div class="panel active"
					style="background-image: url('img/categorias/<?php echo $categoria['img_categoria'] ?>');">
					<h3>
						<?php echo $categoria['nombre_categoria'] ?>
					</h3>
				</div>
			<?php } ?>
			<?php //} 
			?>
		</div>

		<?php
		$limite = isset($_POST['limite']) ? intval($_POST['limite']) : 4;

		if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ver_mas'])) {
			$limite += 4;
		}

		if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ver_menos'])) {
			$limite = max(4, $limite - 4); // Para que no baje de 4
		}

		$sql = "SELECT * FROM productos ORDER BY id_producto DESC LIMIT $limite";
		$productos = $_conexion->query($sql);
		?>
		<!-- Productos -->
		<div class="container py-5 mt-5">
			<h2 id="productos" class="text-center mb-4">Productos Nuevos</h2>
			<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">

				<?php
				while ($producto = $productos->fetch_assoc()) { ?>
					<div class="col">
						<div class="card h-100 shadow-sm">
							<img src="./img/productos/<?php echo $producto['img_producto'] ?>" class="card-img-top"
								alt="Producto <?php echo $productos->field_count ?>" />
							<div class="card-body">
								<h5 class="card-title">
									<?php echo $producto['nombre'] ?>
								</h5>
								<p class="card-text">
									<?php echo $producto['descripcion'] ?>
								</p>
								<div class="d-flex justify-content-between align-items-center">
									<span class="h5 mb-0">
										<?php echo $producto['precio'] ?>€
									</span>
									<button class="btn btn-outline-secondary">
										<i class="bi bi-cart-plus"></i> Añadir al carrito
									</button>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
			<!-- Botón "Ver más productos" (añade 4 productos mas a la vista) -->
			<form method="post" action="#productos">
				<input type="hidden" name="limite" value="<?php echo $limite; ?>">
				<button type="submit" name="ver_mas" class="btn btn-dark mt-4">Ver más productos</button>

				<!-- Botón "Ver menos productos" (quita 4 productos de la vista) -->
				<?php if ($limite > 4): ?>
					<button type="submit" name="ver_menos" class="btn btn-outline-dark mt-4">Ver menos productos</button>
				<?php endif; ?>
			</form>
		</div>
	</div>

	<!-- Footer -->
	<footer class="text-white pt-5 pb-4">
		<div class="container">
			<div class="row">
				<div class="col-md-3 col-lg-4 col-xl-3 mb-4">
					<h6 class="footer-title text-uppercase font-weight-bold mb-4">
						Sobre nosotros
					</h6>
					<p class="">
						Mucho más que muebles, Somos SAMAS home y operamos en toda la provincia de Málaga haciendo de tu
						reforma de casa algo más simple y fácil de lograr.
					</p>
				</div>
				<div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
					<h6 class="footer-title text-uppercase font-weight-bold mb-4">
						Categorias
					</h6>
					<p><a href="#" class="footer-link">Mesas</a></p>
					<p><a href="#" class="footer-link">Sillas</a></p>
					<p><a href="#" class="footer-link">Armarios</a></p>
					<p><a href="#" class="footer-link">Decoración</a></p>
				</div>
				<div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
					<h6 class="footer-title text-uppercase font-weight-bold mb-4">
						Secciones
					</h6>
					<p><a href="/productos" class="footer-link">Productos</a></p>
					<p><a href="/plano" class="footer-link">Plano</a></p>
					<p><a href="/suscripcion" class="footer-link">Suscripción</a></p>
					<p><a href="/contacto" class="footer-link">Contacto</a></p>
					<p><a href="./util/archivos/politica-cookies" target="_blank" id="politica-cookies"
							class="footer-link">Política de cookies</a></p>
				</div>
				<div class="col-md-4 col-lg-3 col-xl-3 mb-md-0 mb-4">
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
				<a href="#" class="text-white">SAMAS home</a>
			</div>
	</footer>
	<script>
		window.difyChatbotConfig = {
			token: 'B4keNRHr22WXJT38',
			systemVariables: {
				// user_id: 'YOU CAN DEFINE USER ID HERE',
				// conversation_id: 'YOU CAN DEFINE CONVERSATION ID HERE, IT MUST BE A VALID UUID',
			},
		}
	</script>
	<script src="https://udify.app/embed.min.js" id="B4keNRHr22WXJT38" defer>
	</script>
	<style>
		#dify-chatbot-bubble-button {
			background-color: #1C64F2 !important;
			position: fixed !important;
		}

		#dify-chatbot-bubble-window {
			width: 24rem !important;
			height: 40rem !important;
			position: fixed !important;
		}
	</style>
	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		const panels = document.querySelectorAll(".panel");
		let hoverTimeout;

		panels.forEach((panel) => {
			panel.addEventListener("mouseenter", () => {
				clearTimeout(hoverTimeout);
				hoverTimeout = setTimeout(() => {
					removeActiveClasses();
					panel.classList.add("active");
				}, 200);
			});
		});

		function removeActiveClasses() {
			panels.forEach((panel) => {
				panel.classList.remove("active");
			});
		}
	</script>
	
	</div>
	<script>
		document.addEventListener("DOMContentLoaded", function() {
		const navbar = document.querySelector('.navbar');
		window.addEventListener('scroll', function() {
			if (window.scrollY > 30) {
			navbar.classList.add('scrolled');
			} else {
			navbar.classList.remove('scrolled');
			}
		});
		});
	</script>

</body>

</html>