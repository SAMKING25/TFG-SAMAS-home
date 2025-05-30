<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>SAMAS HOME - contacto</title>
	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link id="favicon" rel="shortcut icon" href="/img/logos/loguito_gris.png"/>	
	<!--search-->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.2.4/fabric.min.js"></script>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
	<!-- Archivo CSS personalizado -->
	<link rel="stylesheet" href="/css/landing.css" />
	<style>
		.contact-wrapper {
			background: white;
			border-radius: 20px;
			overflow: hidden;
			box-shadow: 0 5px 30px rgba(0, 0, 0, 0.1);
		}

		.contact-info {
			background: #fccb90;
			background: -webkit-linear-gradient(to right,
					rgb(163, 144, 130),
					rgb(146, 116, 71),
					rgb(165, 125, 49),
					rgb(102, 67, 20));
			background: linear-gradient(to right,
					rgb(163, 144, 130),
					rgb(146, 116, 71),
					rgb(165, 125, 49),
					rgb(102, 67, 20));
			border: 1px solid #f7e5cb;
			padding: 40px;
			color: white;
		}

		.contact-item {
			display: flex;
			align-items: center;
			margin-bottom: 25px;
			transition: all 0.3s ease;
		}

		body {
			background-color: #f4f4f4;
		}

		.main-content {
			padding-bottom: 5rem !important;
		}

		.contact-item:hover {
			transform: translateX(10px);
		}

		.contact-icon {
			width: 40px;
			height: 40px;
			background: rgba(255, 255, 255, 0.2);
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			margin-right: 15px;
		}

		.social-links {
			margin-top: 30px;
		}

		.social-icon {
			width: 35px;
			height: 35px;
			background: rgba(255, 255, 255, 0.4);
			border-radius: 50%;
			display: inline-flex;
			align-items: center;
			justify-content: center;
			margin-right: 10px;
			transition: all 0.3s ease;
			color: white;
		}

		.social-icon:hover {
			background: rgba(255, 255, 255, 0.6);
			color: white;
			transform: translateY(-3px);
		}

		.contact-form {
			padding: 40px;
		}

		.form-control {
			border-radius: 10px;
			padding: 12px 15px;
			border: 2px solid #eee;
			transition: all 0.3s ease;
		}

		.form-control:focus {
			border-color: #0062cc;
			box-shadow: none;
		}

		.form-label {
			font-weight: 500;
			margin-bottom: 8px;
		}

		.btn-submit {
			background: linear-gradient(to right,
					rgb(163, 144, 130),
					rgb(146, 116, 71),
					rgb(165, 125, 49),
					rgb(102, 67, 20));
			border: none;
			color: white;
			padding: 12px 30px;
			border-radius: 10px;
			transition: all 0.3s ease;
		}

		.btn-submit:hover {
			transform: translateY(-2px);
			box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
		}

		.map-container {
			height: 200px;
			border-radius: 10px;
			overflow: hidden;
			margin-top: 20px;
		}

		.logo-contacto {
			display: flex;
			justify-content: center;
			margin-bottom: 20px;
		}
	</style>
	<?php
	error_reporting(E_ALL);
	ini_set("display_errors", 1);

	require('../util/conexion.php');

	session_start();
	?>
</head>

<body>
	<?php
	include('../navbar.php');
	?>
	<div class="container py-5 main-content">
		<div class="row justify-content-center">
			<div class="col-lg-10">
				<div class="contact-wrapper">
					<div class="row g-0">
						<!-- Se elimina el logo de aquí -->
						<div class="col-md-5">
							<div class="contact-info h-100">
								<!-- Logo encima del título -->
								<div class="logo-contacto">
									<a class="navbar-brand" href="#">
										<img src="/img/logos/logo-negro-nobg.png" alt="Logo" height="200px" />
									</a>
								</div>
								<h3 class="mb-4">Ponte en contacto</h3>
								<p class="mb-4">
									Nos encantaría saber de ti. Por favor, completa el
									formulario o contáctanos usando la información de abajo.
								</p>

								<div class="contact-item">
									<div class="contact-icon">
										<i class="fas fa-phone"></i>
									</div>
									<div>
										<h6 class="mb-0">Teléfono</h6>
										<p class="mb-0">+34 645 867 244</p>
									</div>
								</div>

								<div class="contact-item">
									<div class="contact-icon">
										<i class="fas fa-envelope"></i>
									</div>
									<div>
										<h6 class="mb-0">Correo electrónico</h6>
										<p class="mb-0">samashome1@gmail.com</p>
									</div>
								</div>

							</div>
						</div>

						<div class="col-md-7">
							<div class="contact-form">
								<h2 class="mb-4">Envíanos un mensaje</h2>
								<form action="datos" method="post" enctype="multipart/form-data">
									<div class="row">
										<div class="col-md-6 mb-3">
											<label class="form-label">Nombre</label>
											<input name="nombre" type="text" class="form-control"
												placeholder="Nombre" />
										</div>
										<div class="col-md-6 mb-3">
											<label class="form-label">Apellido</label>
											<input name="apellido" type="text" class="form-control"
												placeholder="Apellido" />
										</div>
									</div>

									<div class="mb-3">
										<label class="form-label" for="email">Email</label>
										<input type="email" id="email" name="email" class="form-control"
											placeholder="Inserte su correo electrónico" />
										<?php if (isset($err_email))
											echo "<span class='error'>$err_email</span>"; ?>
									</div>

									<div class="mb-3">
										<label class="form-label">Asunto</label>
										<input name="asunto" type="text" class="form-control"
											placeholder="¿Cómo podemos ayudarte?" />
									</div>

									<div class="mb-4">
										<label class="form-label">Mensaje</label>
										<textarea name="mensaje" class="form-control" rows="5"
											placeholder="Tu mensaje aquí..."></textarea>
									</div>

									<button type="submit" class="btn btn-submit text-white">
										Enviar
									</button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const form = document.querySelector('form');
			const emailInput = document.getElementById('email');

			// Para mostrar errores
			const emailError = document.createElement('span');
			emailError.classList.add('error');
			emailInput.parentNode.appendChild(emailError);

			form.addEventListener('submit', function (event) {
				let valid = true;

				emailError.textContent = '';

				// Email
				const emailValue = emailInput.value.trim();
				if (emailValue === '') {
					emailError.textContent = 'El email es obligatorio.';
					valid = false;
				} else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
					emailError.textContent = 'El email no es válido.';
					valid = false;
				}
				if (!valid) {
					event.preventDefault();
				}
			});
		});
	</script>
	<?php include('../footer.php'); ?>
	<?php include('../cookies.php'); ?>
	<?php include('../udify-bot.php'); ?>

</body>

</html>