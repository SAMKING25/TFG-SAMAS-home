<!-- Archivo CSS personalizado -->
<style>
	/* Estilos para el aviso de cookies */
	.aviso-cookies {
		display: none;
		background: #fff;
		padding: 20px;
		width: calc(100% - 40px);
		max-width: 300px;
		line-height: 150%;
		border-radius: 10px;
		position: fixed;
		/* Centra el aviso en pantalla */
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		z-index: 100001;
		padding-top: 60px;
		box-shadow: 0px 2px 20px 10px rgba(222, 222, 222, .25);
		text-align: center;
	}

	.aviso-cookies.activo {
		display: block;
	}

	.aviso-cookies .galleta {
		max-width: 100px;
		position: absolute;
		top: -50px;
		left: calc(50% - 50px);
	}

	.aviso-cookies .titulo,
	.aviso-cookies .parrafo {
		margin-bottom: 15px;
	}

	.aviso-cookies .boton {
		width: 100%;
		background: #595959;
		border: none;
		color: #fff;
		font-family: 'Roboto', sans-serif;
		text-align: center;
		padding: 15px 20px;
		font-weight: 700;
		cursor: pointer;
		transition: .3s ease all;
		border-radius: 5px;
		margin-bottom: 15px;
		font-size: 14px;
	}

	.aviso-cookies .boton:hover {
		background: #000;
	}

	.aviso-cookies .enlace {
		color: #8d5d33;
		text-decoration: none;
		font-size: 14px;
	}

	.aviso-cookies .enlace:hover {
		text-decoration: underline;
	}

	.fondo-aviso-cookies {
		display: none;
		background: rgba(0, 0, 0, .20);
		position: fixed;
		z-index: 100000;
		width: 100vw;
		height: 100vh;
		top: 0;
		left: 0;
	}

	.fondo-aviso-cookies.activo {
		display: block;
	}
</style>

<!-- Estructura del aviso de cookies -->
<div class="aviso-cookies" id="aviso-cookies">
	<img src="/img/logos/cookie.svg" alt="Galleta" class="galleta">
	<h3 class="titulo">Cookies</h3>
	<p class="parrafo">Utilizamos cookies propias y de terceros para mejorar nuestros servicios.</p>
	<button class="boton" id="btn-aceptar-cookies">De acuerdo</button>
	<a href="/util/archivos/politica-cookies" class="enlace">Política de cookies</a>
</div>
<div class="fondo-aviso-cookies" id="fondo-aviso-cookies"></div>

<script>
	// Elementos del aviso de cookies
	const botonAceptarCookies = document.getElementById('btn-aceptar-cookies');
	const avisoCookies = document.getElementById('aviso-cookies');
	const fondoAvisoCookies = document.getElementById('fondo-aviso-cookies');

	// Capa para Google Tag Manager o analítica
	dataLayer = [];

	// Si no se han aceptado las cookies, muestra el aviso
	if (!localStorage.getItem('cookies-aceptadas')) {
		avisoCookies.classList.add('activo');
		fondoAvisoCookies.classList.add('activo');
	} else {
		dataLayer.push({
			'event': 'cookies-aceptadas'
		});
	}

	// Al aceptar cookies, oculta el aviso y guarda la preferencia
	botonAceptarCookies.addEventListener('click', () => {
		avisoCookies.classList.remove('activo');
		fondoAvisoCookies.classList.remove('activo');

		localStorage.setItem('cookies-aceptadas', true);

		dataLayer.push({
			'event': 'cookies-aceptadas'
		});
	});
</script>

<script>
	// Cambia el favicon según el tema del sistema (oscuro/claro)
	function updateFavicon(theme) {
		const favicon = document.getElementById('favicon');
		if (theme === 'dark') {
			favicon.href = '/img/logos/loguito_gris.png';
		} else {
			favicon.href = '/img/logos/loguito_negro.png';
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