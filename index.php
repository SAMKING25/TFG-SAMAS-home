<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>SAMAS HOME</title>
	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
	<link id="favicon" rel="shortcut icon" href="./img/logos/loguito_gris.png" /> <!-- Archivo CSS personalizado -->
	<link rel="stylesheet" href="/css/landing.css" />
	<!--Search-->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet">

	<!--Conexion con BD-->
	<?php
	error_reporting(E_ALL);
	ini_set("display_errors", 1);

	require('util/conexion.php');

	session_start();

	// --- PROCESAR AÑADIR AL CARRITO DESDE INDEX ---
	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_to_cart"])) {
		// Si el usuario no está logueado, redirige al login
		if (!isset($_SESSION["usuario"])) {
			header("Location: ./login/usuario/iniciar_sesion_usuario");
			exit;
		}
		$id_producto = intval($_POST["id_producto"]);
		$id_usuario = $_SESSION["usuario"];
		$cantidad = isset($_POST["cantidad"]) ? intval($_POST["cantidad"]) : 1;

		// Obtener stock del producto
		$stmt_stock = $_conexion->prepare("SELECT stock FROM productos WHERE id_producto = ?");
		$stmt_stock->bind_param("i", $id_producto);
		$stmt_stock->execute();
		$result_stock = $stmt_stock->get_result();
		$stock = 0;
		if ($row = $result_stock->fetch_assoc()) {
			$stock = $row["stock"];
		}
		$stmt_stock->close();

		if ($cantidad < 1) {
			$mensaje = "error";
			$errorMsg = "Cantidad no válida.";
		} elseif ($cantidad > $stock) {
			$mensaje = "error";
			$errorMsg = "No hay suficiente stock disponible.";
		} else {
			$stmt = $_conexion->prepare(
				"INSERT INTO carrito (id_usuario, id_producto, cantidad) VALUES (?, ?, ?)
						ON DUPLICATE KEY UPDATE cantidad = VALUES(cantidad)"
			);
			$stmt->bind_param("iii", $id_usuario, $id_producto, $cantidad);

			if ($stmt->execute()) {
				$mensaje = "success";
			} else {
				$mensaje = "error";
				$errorMsg = $stmt->error;
			}
			$stmt->close();
		}
	}
	?>

	<style>
		.montserrat-title {
			font-family: 'Montserrat', Arial, sans-serif !important;
			font-weight: 700;
			letter-spacing: -1px;
		}

		.oferta-card {
			border-radius: 1.2rem !important;
			overflow: hidden;
			transition: transform 0.2s, box-shadow 0.2s;
			background: #fff;
		}

		.oferta-card:hover {
			transform: scale(0.96);
			box-shadow: 0 4px 16px rgba(60, 60, 60, 0.10);
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
			background: rgba(0, 0, 0, 0.25) !important;
			border-radius: 50%;
			border: 2px solid #e0e0e0;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.45);
			opacity: 0.85;
			transition: opacity 0.2s, box-shadow 0.2s;
			z-index: 2;
		}

		.custom-carousel-btn:hover {
			opacity: 1;
			box-shadow: 0 4px 16px rgba(0, 0, 0, 0.18);
		}

		.carousel-control-prev {
			left: -35px;
		}

		.carousel-control-next {
			right: -35px;
		}

		@media (max-width: 600px) {

			.carousel-control-prev,
			.carousel-control-next {
				left: 0 !important;
				right: 0 !important;
			}

			.oferta-card {
				border-radius: 0.8rem !important;
			}
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
				<h1 class="montserrat-title">Tu hogar comienza aquí</h1>
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
			<h2 class="mb-4 text-start fw-bold titulo-subrayado" style="font-size:2rem;">Últimas Ofertas</h2>
			<?php
			$sql = "SELECT p.*, o.porcentaje
					FROM productos p
					INNER JOIN ofertas o ON p.id_oferta = o.id_oferta
					ORDER BY RAND() LIMIT 8";
			$resultado = $_conexion->query($sql);

			$ofertas = [];
			while ($producto = $resultado->fetch_assoc()) {
				$ofertas[] = $producto;
			}
			?>
			<div id="carruselOfertas" class="carousel slide" data-bs-ride="carousel">
				<div class="carousel-inner">
					<?php for ($i = 0; $i < count($ofertas); $i += 2): ?>
						<div class="carousel-item <?php if ($i === 0)
							echo 'active'; ?>">
							<div class="row g-4 justify-content-center">
								<?php for ($j = $i; $j < $i + 2 && $j < count($ofertas); $j++):
									$producto = $ofertas[$j];
									$precio_original = $producto['precio'];
									$porcentaje_descuento = $producto['porcentaje'];
									$precio_final = $precio_original - ($precio_original * $porcentaje_descuento / 100);
									?>
									<div class="col-12 col-lg-6">
										<div class="card h-100 shadow oferta-card rounded-4 border-0">
											<div class="position-relative">
												<a
													href="./productos/ver_producto?id_producto=<?php echo $producto["id_producto"]; ?>">
													<img src="img/productos/<?php echo $producto['img_producto']; ?>"
														class="card-img-top rounded-top-4"
														alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
														style="height: 200px; object-fit: cover;">
												</a>
												<span
													class="badge bg-danger position-absolute top-0 start-0 m-2 fs-6 rounded-pill px-3 py-2 shadow">
													-<?php echo $producto['porcentaje']; ?>%
												</span>
											</div>
											<div class="card-body d-flex flex-column">
												<h5 class="card-title text-truncate">
													<?php echo htmlspecialchars($producto['nombre']); ?>
												</h5>
												<p class="card-text small text-muted text-truncate">
													<?php echo htmlspecialchars($producto['descripcion']); ?>
												</p>
												<div class="mt-auto">
													<span class="text-decoration-line-through text-secondary me-2">
														<?php echo number_format($precio_original, 2, ',', '.'); ?> €
													</span>
													<span class="fw-bold fs-5 text-success">
														<?php echo number_format($precio_final, 2, ',', '.'); ?> €
													</span>
												</div>
												<a href="./productos/ver_producto?id_producto=<?php echo $producto["id_producto"]; ?>"
													class="btn btn-dark btn-sm mt-3 w-100 rounded-pill">
													Ir a la oferta
													<i class="bi bi-arrow-right ms-2"></i>
												</a>
											</div>
										</div>
									</div>
								<?php endfor; ?>
							</div>
						</div>
					<?php endfor; ?>
				</div>
				<!-- Controles personalizados -->
				<button class="carousel-control-prev custom-carousel-btn" type="button"
					data-bs-target="#carruselOfertas" data-bs-slide="prev">
					<i class="bi bi-chevron-left fs-2"></i>
				</button>
				<button class="carousel-control-next custom-carousel-btn" type="button"
					data-bs-target="#carruselOfertas" data-bs-slide="next">
					<i class="bi bi-chevron-right fs-2"></i>
				</button>
			</div>

			<!-- Categorías con scroll horizontal -->
			<div class="container my-5">
				<h2 class="fw-bold mb-4 text-start titulo-subrayado" style="font-size:2rem;">Categorías</h2>
				<div class="categorias-scroll d-flex flex-row gap-4 overflow-auto pb-3">
					<?php
					$sql = "SELECT * FROM categorias";
					$categorias = $_conexion->query($sql);
					while ($categoria = $categorias->fetch_assoc()) {
						$nombre = htmlspecialchars($categoria['nombre_categoria']);
						$img = htmlspecialchars($categoria['img_categoria']);
						?>
						<a href="productos?categoria=<?php echo urlencode($nombre); ?>"
							class="text-decoration-none flex-shrink-0">
							<div class="card categoria-card-lg border-0 shadow-sm position-relative overflow-hidden">
								<img src="img/categorias/<?php echo $img; ?>"
									class="card-img-top w-100 h-100 object-fit-cover" alt="<?php echo $nombre; ?>">
								<div
									class="categoria-label position-absolute start-50 bottom-0 translate-middle-x mb-3 px-4 py-3 rounded-pill fw-semibold text-dark">
									<?php echo $nombre; ?>
								</div>
							</div>
						</a>
					<?php } ?>
				</div>
			</div>

			<?php
			$limite = isset($_GET['limite']) ? intval($_GET['limite']) : 8;

			if (isset($_GET['ver_mas'])) {
				$limite += 4;
			}

			if (isset($_GET['ver_menos'])) {
				$limite = max(8, $limite - 4); // Para que no baje de 8
			}

			$sql = "SELECT * FROM productos WHERE id_oferta IS NULL ORDER BY id_producto DESC LIMIT $limite";
			$productos = $_conexion->query($sql);
			?>
			<!-- Productos -->
			<div class="container py-5 mt-5">
				<h2 id="productos" class="text-start fw-bold mb-4 titulo-subrayado" style="font-size:2rem;">Productos
					Nuevos</h2>
				<div class="nuevos-productos-grid">

					<!-- SweetAlert2 para mostrar el mensaje toast -->
					<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
					<script>
						<?php if (isset($mensaje) && $mensaje == "success"): ?>
							Swal.fire({
								toast: true,
								position: 'top-end',
								icon: 'success',
								title: 'Producto añadido al carrito',
								showConfirmButton: false,
								timer: 2000,
								timerProgressBar: true,
								background: '#f4e5cc',
								color: '#333',
								didOpen: (toast) => {
									toast.addEventListener('mouseenter', Swal.stopTimer)
									toast.addEventListener('mouseleave', Swal.resumeTimer)
								}
							});
						<?php elseif (isset($mensaje) && $mensaje == "error"): ?>
							Swal.fire({
								toast: true,
								position: 'top-end',
								icon: 'error',
								title: 'Error al añadir el producto al carrito',
								text: '<?php echo addslashes($errorMsg); ?>',
								showConfirmButton: false,
								timer: 3000,
								timerProgressBar: true
							});
						<?php endif; ?>
					</script>
					<?php
					while ($producto = $productos->fetch_assoc()) { ?>
						<div class="nuevo-producto-card">
							<a href="./productos/ver_producto?id_producto=<?php echo $producto['id_producto']; ?>">
								<img src="./img/productos/<?php echo $producto['img_producto'] ?>" class="card-img-top"
									alt="Producto <?php echo $productos->field_count ?>" />
							</a>
							<div class="nuevo-producto-body">
								<div class="nuevo-producto-title"><?php echo htmlspecialchars($producto['nombre']); ?></div>
								<div class="nuevo-producto-desc"><?php echo htmlspecialchars($producto['descripcion']); ?>
								</div>
								<div class="nuevo-producto-precio">
									<?php echo number_format($producto['precio'], 2, ',', '.'); ?> €
								</div>
								<form method="post" action="" class="w-100">
									<input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">
									<input type="hidden" name="cantidad" value="1">
									<button type="submit" name="add_to_cart" class="nuevo-producto-btn w-100">
										<i class="bi bi-cart-plus"></i> Añadir al carrito
									</button>
								</form>
							</div>
						</div>
					<?php } ?>
				</div>
				<!-- Botón "Ver más productos" (añade 4 productos mas a la vista) -->
				<form method="get" action="#productos">
					<input type="hidden" name="limite" value="<?php echo $limite; ?>">
					<button type="submit" name="ver_mas" class="btn btn-dark mt-4">Ver más productos</button>

					<!-- Botón "Ver menos productos" (quita 4 productos de la vista) -->
					<?php if ($limite > 8): ?>
						<button type="submit" name="ver_menos" class="btn btn-outline-dark mt-4">Ver menos
							productos</button>
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
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			document.querySelectorAll('.titulo-subrayado').forEach(function (el) {
				el.classList.add('animar-subrayado');
			});
		});
	</script>
</body>

</html>