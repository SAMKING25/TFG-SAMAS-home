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
	<link rel="stylesheet" href="./css/landing.css" />
	<!--search-->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
	<!--conexion con BD-->
	<?php
	session_start();

	error_reporting(E_ALL);
	ini_set("display_errors", 1);

	require('util/conexion.php');
	?>
</head>

<body>
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
								<a class="nav-link" href="#">Inicio</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#">Productos</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#">Plano</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#">Suscripción</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#">Contacto</a>
							</li>
						</ul>
					</div>

					<!-- Logo (Center) -->
					<div class="col-md-4 d-flex justify-content-center">
						<a class="navbar-brand" href="#">
							<img src="./img/logos/logo-marron-nobg.png" alt="Logo" height="80px" />
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
						<a href="./panel-control/index.php" class="nav-link">
							<i class="bi bi-person-circle icono-personalizado"></i>
						</a>
					</div>
				</div>
			</div>
		</div>
	</nav>

	<!-- End Navbar -->
	<!-- Sección para la imagen de portada -->
	<div class="col">
		<section class="index-section">
			<div class="section-banner">
				<h1 class="banner-text">Tu hogar comienza aquí</h1>
				<p class="banner-subtext">
					Encuentra los mejores muebles para tu hogar
				</p>
				<a href="#productos" class="banner-btn btn btn-dark">
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
			<!-- Carrusel -->
			<div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
				<!-- Contenido del Carrusel -->
				<div class="carousel-inner">
					<!-- Slide 1 -->
					<div class="carousel-item active">
						<div class="row text-center">
							<div class="col-md-4">
								<img src="./img/productos/cama.jpg" class="img-fluid mb-2 small-image" alt="Producto 1" />
								<p>
									<span>Canapé y colchón</span>
									<br>
									<span style="text-decoration: line-through; color: grey;">325€</span>
									<span style="font-size: larger; font-weight: bold; color: #004085;">275€</span>
								</p>
							</div>
							<div class="col-md-4">
								<img src="./img/productos/sofa.jpg" class="img-fluid mb-2 small-image" alt="Producto 2" />
								<p>
									<span>Sillón de dos plazas</span>
									<br>
									<span style="text-decoration: line-through; color: grey;">375€</span>
									<span style="font-size: larger; font-weight: bold; color: #004085;">300€</span>
								</p>
							</div>
							<div class="col-md-4">
								<img src="./img/productos/mueble.jpeg" class="img-fluid mb-2 small-image" alt="Producto 3" />
								<p>
									<span>Armario con tres puertas</span>
									<br>
									<span style="text-decoration: line-through; color: grey;">315€</span>
									<span style="font-size: larger; font-weight: bold; color: #004085;">280€</span>
								</p>
							</div>
						</div>
					</div>

					<!-- Slide 2 -->
					<div class="carousel-item">
						<div class="row text-center">
							<div class="col-md-4">
								<img src="./img/productos/mesas.jpg" class="img-fluid mb-2 small-image" alt="Producto 5" />
								<p>
									<span>Mesa y sillas para comedor</span>
									<br>
									<span style="text-decoration: line-through; color: grey;">200 €</span>
									<span style="font-size: larger; font-weight: bold; color: #004085;">150,00 €</span>
								</p>
							</div>
							<div class="col-md-4">
								<img src="./img/productos/mueble.jpeg" class="img-fluid mb-2 small-image" alt="Producto 6" />
								<p>
									<span>Mueble para televisión</span>
									<br>
									<span style="text-decoration: line-through; color: grey;">125 €</span>
									<span style="font-size: larger; font-weight: bold; color: #004085;">99,50 €</span>
								</p>
							</div>
							<div class="col-md-4">
								<img src="./img/productos/armarios.webp" class="img-fluid mb-2 small-image" alt="Producto 7" />
								<p>
									<span>Escritorio y silla de oficina </span>
									<br>
									<span style="text-decoration: line-through; color: grey;">250€</span>
									<span style="font-size: larger; font-weight: bold; color: #004085;">150€</span>
								</p>
							</div>
						</div>
					</div>
				</div>

				<!-- Controles -->
				<button class="carousel-control-prev" type="button" data-bs-target="#productCarousel"
					data-bs-slide="prev">
					<span class="carousel-control-prev-icon" aria-hidden="true"></span>
					<span class="visually-hidden">Anterior</span>
				</button>
				<button class="carousel-control-next" type="button" data-bs-target="#productCarousel"
					data-bs-slide="next">
					<span class="carousel-control-next-icon" aria-hidden="true"></span>
					<span class="visually-hidden">Siguiente</span>
				</button>
			</div>
		</div>

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
			while ($categoria = $categorias->fetch_assoc()) { ?>
				<div class="panel active" style="background-image: url('img/categorias/<?php echo $categoria['img_categoria'] ?>')">
					<h3><?php echo $categoria['categoria'] ?></h3>
				</div>
			<?php } ?>
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
							<img src="./img/productos/<?php echo $producto['imagen'] ?>" class="card-img-top" alt="Producto <?php echo $productos->field_count ?>" />
							<div class="card-body">
								<h5 class="card-title"><?php echo $producto['nombre'] ?></h5>
								<p class="card-text">
									<?php echo $producto['descripcion'] ?>
								</p>
								<div class="d-flex justify-content-between align-items-center">
									<span class="h5 mb-0"><?php echo $producto['precio'] ?>€</span>
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
					<p><a href="#" class="footer-link">Muebles</a></p>
					<p><a href="#" class="footer-link">Plano</a></p>
					<p><a href="#" class="footer-link">Suscripción</a></p>
					<p><a href="#" class="footer-link">Contacto</a></p>
				</div>
				<div class="col-md-4 col-lg-3 col-xl-3 mb-md-0 mb-4">
					<h6 class="footer-title text-uppercase font-weight-bold mb-4">
						Contacto
					</h6>
					<p><i class="fas fa-home me-2"></i>Málaga, Andalucía, España</p>
					<p><i class="fas fa-envelope me-2"></i>samashome@gmail.com</p>
					<p><i class="fas fa-phone me-2"></i>+34 645 867 244</p>
				</div>
			</div>
			<div class="footer-copyright text-center font-weight-bold py-3">
				© 2025
				<a href="#" class="text-white">SAMAS home</a>
			</div>
	</footer>

	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		const panels = document.querySelectorAll(".panel");

		panels.forEach((panel) => {
			panel.addEventListener("mouseover", () => {
				removeActiveClasses();
				panel.classList.add("active");
			});
		});

		panels.forEach((panel) => {
			panel.addEventListener("mouseleave", () => {
				addActiveClasses();
				panel.classList.add("active");
			});
		});

		function removeActiveClasses() {
			panels.forEach((panel) => {
				panel.classList.remove("active");
			});
		}

		function addActiveClasses() {
			panels.forEach((panel) => {
				panel.classList.add("active");
			});
		}
	</script>
</body>

</html>