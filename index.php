<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>SAMAS HOME</title>
	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
	<link id="favicon" rel="shortcut icon" href="./img/logos/loguito_gris.png"/>	<!-- Archivo CSS personalizado -->
	<link rel="stylesheet" href="/css/landing.css" />
	<!--Search-->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />

	<!--Conexion con BD-->
	<?php
	error_reporting(E_ALL);
	ini_set("display_errors", 1);

	require('util/conexion.php');

	session_start();
	?>

				<style>
			.oferta-card {
				border-radius: 1.2rem !important;
				overflow: hidden;
				transition: transform 0.2s, box-shadow 0.2s;
				background: #fff;
			}
			.oferta-card:hover {
				transform: scale(0.96);
				box-shadow: 0 4px 16px rgba(60,60,60,0.10);
				border-radius: 1.2rem !important;
			}
			.carousel-inner {
				padding-bottom: 30px;
			}
			.custom-carousel-btn {
				width: 48px;
				height: 48px;
				top: 50%;
				transform: translateY(-50%);
				background: rgba(0, 0, 0, 0.25)!important;
				border-radius: 50%;
				border: 2px solid #e0e0e0;
				box-shadow: 0 2px 8px rgba(0, 0, 0, 0.45);
				opacity: 0.85;
				transition: opacity 0.2s, box-shadow 0.2s;
				z-index: 2;
			}
			.custom-carousel-btn:hover {
				opacity: 1;
				box-shadow: 0 4px 16px rgba(0,0,0,0.18);
			}
			.carousel-control-prev {
				left: -35px;
			}
			.carousel-control-next {
				right: -35px;
			}
			@media (max-width: 600px) {
				.carousel-control-prev, .carousel-control-next {
					left: 0 !important;
					right: 0 !important;
				}
				.oferta-card { border-radius: 0.8rem !important; }
			}
			</style>
</head>

<body class="navbar-home-body">
	<!-- Navbar incluido -->
	<?php $navbar_home = true; ?>
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
		<!-- Carrusel Mejorado de Ofertas -->
		<div class="container mt-5">
			<h2 class="mb-4 text-start fw-bold" style="font-size:2rem;">Últimas Ofertas</h2>
			<?php
			$sql = "SELECT p.*, o.porcentaje
					FROM productos p
					INNER JOIN ofertas o ON p.id_oferta = o.id_oferta
					ORDER BY RAND() LIMIT 8"; // Puedes aumentar el LIMIT si tienes más ofertas
			$resultado = $_conexion->query($sql);

			// Agrupar productos en arrays de 2 para cada slide
			$ofertas = [];
			while ($producto = $resultado->fetch_assoc()) {
				$ofertas[] = $producto;
			}
			$ofertas_por_slide = array_chunk($ofertas, 2);
			?>
			<div id="carruselOfertas" class="carousel slide" data-bs-ride="carousel">
				<div class="carousel-inner">
					<?php foreach ($ofertas_por_slide as $i => $grupo): ?>
						<div class="carousel-item <?php if ($i === 0) echo 'active'; ?>">
							<div class="row g-4 justify-content-center">
								<?php foreach ($grupo as $producto): 
									$precio_original = $producto['precio'];
									$porcentaje_descuento = $producto['porcentaje'];
									$precio_final = $precio_original - ($precio_original * $porcentaje_descuento / 100);
								?>
								<div class="col-12 col-lg-6">
									<div class="card h-100 shadow oferta-card rounded-4 border-0">
										<div class="position-relative">
											<a href="./productos/ver_producto.php?id_producto=<?php echo $producto["id_producto"]; ?>">
												<img src="img/productos/<?php echo $producto['img_producto']; ?>" class="card-img-top rounded-top-4" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" style="height: 200px; object-fit: cover;">
											</a>
											<span class="badge bg-danger position-absolute top-0 start-0 m-2 fs-6 rounded-pill px-3 py-2 shadow">
												-<?php echo $producto['porcentaje']; ?>%
											</span>
										</div>
										<div class="card-body d-flex flex-column">
											<h5 class="card-title text-truncate"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
											<p class="card-text small text-muted text-truncate"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
											<div class="mt-auto">
												<span class="text-decoration-line-through text-secondary me-2">
													<?php echo number_format($precio_original, 2, ',', '.'); ?> €
												</span>
												<span class="fw-bold fs-5 text-success">
													<?php echo number_format($precio_final, 2, ',', '.'); ?> €
												</span>
											</div>
											<a href="./productos/ver_producto.php?id_producto=<?php echo $producto["id_producto"]; ?>" class="btn btn-dark btn-sm mt-3 w-100 rounded-pill">
												Ir a ofertas
											</a>
										</div>
									</div>
								</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<!-- Controles personalizados -->
				<button class="carousel-control-prev custom-carousel-btn" type="button" data-bs-target="#carruselOfertas" data-bs-slide="prev">
					<i class="bi bi-chevron-left fs-2"></i>
				</button>
				<button class="carousel-control-next custom-carousel-btn" type="button" data-bs-target="#carruselOfertas" data-bs-slide="next">
					<i class="bi bi-chevron-right fs-2"></i>
				</button>
			</div>

		<!-- Categorias -->
		<div class="text-start mt-5">
			<h2 class="fw-bold">Categorías</h2>
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
			<h2 id="productos" class="text-start fw-bold mb-4">Productos Nuevos</h2>
			<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">

				<?php
				while ($producto = $productos->fetch_assoc()) { ?>
					<div class="col">
						<div class="card h-100 shadow-sm">
							<a href="./productos/ver_producto.php?id_producto=<?php echo $producto['id_producto']; ?>">
								<img src="./img/productos/<?php echo $producto['img_producto'] ?>" class="card-img-top"
									alt="Producto <?php echo $productos->field_count ?>" />
							</a>
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
	</div>

	<?php include('footer.php'); ?>
	<?php include('udify-bot.php'); ?>
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
	<script>
		document.addEventListener("DOMContentLoaded", function () {
			const navbar = document.querySelector('.navbar-home');
			if (!navbar) return;
			window.addEventListener('scroll', function () {
				if (window.scrollY > 30) {
				navbar.classList.add('scrolled');
				} else {
				navbar.classList.remove('scrolled');
				}
			});
		});
	</script>

	<script>
	function updateFavicon(theme) {
		const favicon = document.getElementById('favicon');
		if (theme === 'dark') {
		favicon.href = './img/logos/loguito_gris.png';
		} else {
		favicon.href = './img/logos/loguito_negro.png';
		}
	}

	// Detecta el tema del sistema
	const darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');

	// Cambia el favicon según el tema actual
	updateFavicon(darkModeMediaQuery.matches ? 'dark' : 'light');

	// Escucha los cambios en el tema
	darkModeMediaQuery.addEventListener('change', e => {
		updateFavicon(e.matches ? 'dark' : 'light');
	});
	</script>
</body>
</html>