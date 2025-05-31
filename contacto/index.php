<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require('../util/conexion.php');
session_start();
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>SAMAS HOME - Contacto</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
	<link id="favicon" rel="shortcut icon" href="/img/logos/loguito_gris.png" />
	<link rel="stylesheet" href="/css/landing.css" />
	<style>
		.contact-main {
			max-width: 950px;
			margin: 0 auto;
			margin-top: 90px;
			background: #fff;
			border-radius: 1.5rem;
			box-shadow: 0 6px 32px rgba(60, 60, 60, 0.10);
			overflow: hidden;
			display: flex;
			flex-wrap: wrap;
		}

		.contact-info-panel {
			background: linear-gradient(135deg, var(--color-footer) 0%, #a67c52 100%);
			color: #fff;
			flex: 1 1 320px;
			min-width: 300px;
			padding: 2.5rem 2rem 2rem 2rem;
			display: flex;
			flex-direction: column;
			justify-content: center;
		}

		.contact-info-panel .logo-contacto {
			display: flex;
			justify-content: flex-start;
			align-items: center;
			margin-bottom: 1.5rem;
		}

		.contact-info-panel .logo-contacto img {
			height: 48px;
		}

		.contact-info-panel h3 {
			font-size: 1.6rem;
			font-weight: 700;
			margin-bottom: 1.2rem;
			color: var(--color-highlight);
		}

		.main-content {
			padding-bottom: 5rem !important;
		}

		.contact-info-panel p {
			font-size: 1.05rem;
			margin-bottom: 2.2rem;
		}

		.contact-info-list {
			margin-bottom: 2.2rem;
		}

		.contact-info-item {
			display: flex;
			align-items: center;
			margin-bottom: 1.2rem;
		}

		.contact-info-icon {
			background: rgba(255, 255, 255, 0.18);
			color: var(--color-accent);
			border-radius: 50%;
			width: 44px;
			height: 44px;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 1.4rem;
			margin-right: 1rem;
		}

		.contact-info-label {
			font-weight: 600;
			margin-bottom: 0.1rem;
		}

		.contact-social {
			margin-top: auto;
		}

		.contact-social a {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			width: 38px;
			height: 38px;
			background: rgba(255, 255, 255, 0.22);
			border-radius: 50%;
			color: #fff;
			margin-right: 0.5rem;
			font-size: 1.2rem;
			transition: background 0.2s, color 0.2s, transform 0.2s;
		}

		.contact-social a:hover {
			background: var(--color-accent);
			color: #222;
			transform: translateY(-2px) scale(1.1);
		}

		.contact-form-panel {
			flex: 2 1 400px;
			min-width: 320px;
			padding: 2.5rem 2.5rem 2rem 2.5rem;
			background: #fff;
			display: flex;
			flex-direction: column;
			justify-content: center;
		}

		.contact-form-panel h2 {
			font-size: 2rem;
			font-weight: 700;
			color: var(--color-medium);
			margin-bottom: 1.5rem;
		}

		.contact-form .form-label {
			font-weight: 500;
			color: var(--color-medium);
		}

		.contact-form .form-control {
			border-radius: 0.8rem;
			border: 2px solid #eee;
			padding: 0.7rem 1rem;
			margin-bottom: 1.1rem;
			font-size: 1rem;
			transition: border-color 0.2s;
		}

		.contact-form .form-control:focus {
			border-color: var(--color-accent);
			box-shadow: none;
		}

		.contact-form .btn-submit {
			background: var(--color-dark);
			color: #fff;
			border-radius: 2rem;
			padding: 0.7rem 2.2rem;
			font-weight: 600;
			font-size: 1.1rem;
			border: none;
			transition: background 0.2s, color 0.2s, transform 0.2s;
		}

		.contact-form .btn-submit:hover {
			background: var(--color-accent);
			color: #222;
			transform: translateY(-2px) scale(1.04);
		}

		@media (max-width: 900px) {
			.contact-main {
				flex-direction: column;
				margin-top: 70px;
			}

			.contact-info-panel,
			.contact-form-panel {
				min-width: 100%;
				padding: 2rem 1.2rem;
			}
		}

		@media (max-width: 600px) {
			.contact-main {
				margin-top: 30px;
				margin-bottom: 20px;
			}

			.contact-form-panel h2 {
				font-size: 1.3rem;
			}
		}

		/* Estilo del error de formulario */
		input-error-msg {
			color: #d32f2f;
			font-size: 0.97em;
			margin-top: 0.2em;
		}

		.form-control.is-invalid {
			border-color: #d32f2f;
		}
	</style>
</head>

<body>
	<?php include('../navbar.php'); ?>

	<div class="container main-content">
		<div class="contact-main mt-4">
			<!-- Panel de información -->
			<div class="contact-info-panel">
				<div class="logo-contacto">
					<img src="/img/logos/loguito_gris.png" alt="Logo SAMAS HOME">
					<span class="ms-2 fw-bold" style="font-size:1.3rem;letter-spacing:2px;">SAMAS HOME</span>
				</div>
				<h3>¿Hablamos?</h3>
				<p>
					¿Tienes dudas, sugerencias o quieres colaborar con nosotros?<br>
					¡Rellena el formulario o usa los datos de contacto!
				</p>
				<div class="contact-info-list">
					<div class="contact-info-item">
						<div class="contact-info-icon"><i class="bi bi-telephone"></i></div>
						<div>
							<div class="contact-info-label">Teléfono</div>
							<div>+34 645 867 244</div>
						</div>
					</div>
					<div class="contact-info-item">
						<div class="contact-info-icon"><i class="bi bi-envelope"></i></div>
						<div>
							<div class="contact-info-label">Email</div>
							<div>samashome1@gmail.com</div>
						</div>
					</div>
				</div>
				<div class="contact-social">
					<a href="https://wa.me/34645867244" target="_blank" title="WhatsApp"><i
							class="bi bi-whatsapp"></i></a>
					<a href="mailto:samashome1@gmail.com" title="Email"><i class="bi bi-envelope-fill"></i></a>
					<a href="https://www.instagram.com/" target="_blank" title="Instagram"><i
							class="bi bi-instagram"></i></a>
				</div>
			</div>
			<!-- Panel de formulario -->
			<div class="contact-form-panel">
				<h2>Envíanos un mensaje</h2>
				<form class="contact-form" action="datos" method="post" enctype="multipart/form-data" novalidate>
					<div class="row">
						<div class="col-md-6 mb-2">
							<label class="form-label">Nombre</label>
							<input name="nombre" type="text" class="form-control" placeholder="Nombre" required />
						</div>
						<div class="col-md-6 mb-2">
							<label class="form-label">Apellido</label>
							<input name="apellido" type="text" class="form-control" placeholder="Apellido" required />
						</div>
					</div>
					<div class="mb-2">
						<label class="form-label" for="email">Email</label>
						<input type="email" id="email" name="email" class="form-control"
							placeholder="Tu correo electrónico" required />
						<?php if (isset($err_email))
							echo "<span class='error'>$err_email</span>"; ?>
					</div>
					<div class="mb-2">
						<label class="form-label">Asunto</label>
						<input name="asunto" type="text" class="form-control" placeholder="¿Cómo podemos ayudarte?"
							required />
					</div>
					<div class="mb-3">
						<label class="form-label">Mensaje</label>
						<textarea name="mensaje" class="form-control" rows="5" placeholder="Tu mensaje aquí..."
							required></textarea>
					</div>
					<button type="submit" class="btn btn-submit text-white">
						<i class="bi bi-send"></i> Enviar
					</button>
				</form>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const form = document.querySelector('.contact-form');
			const fields = [
				{ name: 'nombre', label: 'El nombre es obligatorio.' },
				{ name: 'apellido', label: 'El apellido es obligatorio.' },
				{ name: 'email', label: 'El email es obligatorio.', email: true },
				{ name: 'asunto', label: 'El asunto es obligatorio.' },
				{ name: 'mensaje', label: 'El mensaje es obligatorio.' }
			];

			// Elimina errores previos
			function clearErrors() {
				form.querySelectorAll('.input-error-msg').forEach(e => e.remove());
				form.querySelectorAll('.form-control').forEach(e => e.classList.remove('is-invalid'));
			}

			form.addEventListener('submit', function (event) {
				clearErrors();
				let valid = true;

				fields.forEach(field => {
					const input = form.elements[field.name];
					const value = input.value.trim();
					let errorMsg = '';

					if (!value) {
						errorMsg = field.label;
					} else if (field.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
						errorMsg = 'El email no es válido.';
					}

					if (errorMsg) {
						valid = false;
						input.classList.add('is-invalid');
						const error = document.createElement('div');
						error.className = 'input-error-msg';
						error.style.color = '#d32f2f';
						error.style.fontSize = '0.97em';
						error.style.marginTop = '0.2em';
						error.textContent = errorMsg;
						input.parentNode.appendChild(error);
					}
				});

				if (!valid) event.preventDefault();
			});
		});
	</script>
	<?php include('../footer.php'); ?>
	<?php include('../cookies.php'); ?>
	<?php include('../udify-bot.php'); ?>
</body>

</html>