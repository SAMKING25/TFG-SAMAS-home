<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Incluye la conexión a la base de datos y la sesión
require('../util/conexion.php');
session_start();

// Verifica que se haya recibido el importe por POST
if (!isset($_POST['importe'])) {
  echo "No se recibió el importe. <a href='/carrito/'>Volver al carrito</a>";
  exit;
}
$importe = floatval($_POST['importe']);
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Completa tu compra | SAMAS HOME</title>
  <!-- Bootstrap y estilos principales -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link rel="shortcut icon" href="./img/logos/logo-marron-nobg.ico" />
  <link rel="stylesheet" href="../css/landing.css" />
  <!-- SDK de PayPal -->
  <script src="https://www.paypal.com/sdk/js?client-id=AZiNIbkuxCM_s2y_iYwPg7V4zhQKzZbSJhN_y0P7_Pl5hDT7l3bAdsy8VoRzicjIA7r3JnzwT8e_TTJK&currency=EUR"></script>
  <style>
    body {
      background: #f6f6f6;
    }

    .main-content {
      min-height: 90vh;
      /* Antes: 100vh. Así el contenido no ocupa toda la pantalla */
      display: flex;
      align-items: flex-start;
      justify-content: center;
      padding-top: 2rem;
      padding-bottom: 1rem;
      /* Añadido para menos espacio abajo */
    }

    .form-card {
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 8px 32px rgba(60, 60, 120, 0.12);
      padding: 2.5rem 2rem 2rem 2rem;
      max-width: 420px;
      width: 100%;
      margin: 0;
      display: flex;
      flex-direction: column;
      gap: 1.2rem;
    }

    .paypal-section {
      border-top: 1px solid #eee;
      padding-top: 1.5rem;
      margin-top: 1.5rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.5rem;
    }

    #paypal-buttom-conteiner {
      width: 100%;
      padding: 0;
      background: none;
      border-radius: 0;
      box-shadow: none;
      transform: none;
      transition: none;
      display: flex;
      justify-content: center;
    }

    @media (max-width: 576px) {
      .form-card {
        padding: 1.2rem 0.5rem 1.5rem 0.5rem;
        max-width: 98vw;
      }

      .main-content {
        padding-top: 1rem;
        padding-bottom: 0.5rem;
        /* Menos espacio abajo en móvil */
        min-height: 90vh;
        /* Un poco más pequeño en móvil */
      }
    }
  </style>
</head>

<body>
  <?php include('../navbar.php'); ?>
  <div class="main-content">
    <div class="form-card">
      <h3 class="mb-4 text-center">Datos para la compra</h3>
      <!-- Formulario de datos del comprador -->
      <form id="datosForm" novalidate>
        <input type="hidden" name="id_suscripcion" id="id_suscripcion" value="<?php echo isset($_POST['id_suscripcion']) ? intval($_POST['id_suscripcion']) : ''; ?>">
        <div class="mb-3">
          <label for="nombre" class="form-label">Nombre</label>
          <input type="text" class="form-control" id="nombre" name="nombre" required>
          <div class="invalid-feedback">Introduce tu nombre.</div>
        </div>
        <div class="mb-3">
          <label for="apellidos" class="form-label">Apellidos</label>
          <input type="text" class="form-control" id="apellidos" name="apellidos" required>
          <div class="invalid-feedback">Introduce tus apellidos.</div>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Correo electrónico</label>
          <input type="email" class="form-control" id="email" name="email" required>
          <div class="invalid-feedback">Introduce un correo válido.</div>
        </div>
        <div class="mb-3">
          <label for="telefono" class="form-label">Teléfono</label>
          <input type="tel" class="form-control" id="telefono" name="telefono" required>
          <div class="invalid-feedback">Introduce tu teléfono.</div>
        </div>
        <div class="mb-3">
          <label for="direccion" class="form-label">Dirección</label>
          <input type="text" class="form-control" id="direccion" name="direccion" required>
          <div class="invalid-feedback">Introduce tu dirección.</div>
        </div>
        <div class="mb-3 text-center">
          <strong>Total a pagar: <?php echo number_format($importe, 2, ',', '.'); ?> €</strong>
        </div>
        <div class="paypal-section">
          <div id="paypal-buttom-conteiner"></div>
        </div>
      </form>
    </div>
  </div>
  <?php include('../footer.php'); ?>
  <?php include('../cookies.php'); ?>
  <?php include('../udify-bot.php'); ?>

  <script>
    // Bootstrap validation visual feedback
    (function() {
      'use strict';
      var form = document.getElementById('datosForm');
      form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    })();

    // Configuración de los botones de PayPal
    paypal.Buttons({
      createOrder: function(data, actions) {
        var form = document.getElementById('datosForm');
        // Validación del formulario antes de crear la orden
        if (!form.checkValidity()) {
          form.classList.add('was-validated');
          form.reportValidity();
          return false;
        }
        return actions.order.create({
          purchase_units: [{
            amount: {
              value: "<?php echo number_format($importe, 2, '.', ''); ?>"
            }
          }]
        });
      },
      onApprove: function(data, actions) {
        // Cuando el pago es aprobado por PayPal
        return actions.order.capture().then(function(detalles) {
          // 2. Tramitar pedido solo si el correo fue exitoso
          var form = document.getElementById('datosForm');
          var formData2 = new FormData(form);
          formData2.set('nombre', form.nombre.value);
          formData2.set('apellidos', form.apellidos.value);
          formData2.set('email', form.email.value);
          formData2.set('telefono', form.telefono.value);
          formData2.set('direccion', form.direccion.value);
          formData2.set('importe', "<?php echo number_format($importe, 2, '.', ''); ?>");
          formData2.set('id_suscripcion', form.id_suscripcion.value);
          formData2.append('paypal_name', detalles.payer.name.given_name + ' ' + detalles.payer.name.surname);
          formData2.append('paypal_email', detalles.payer.email_address);

          // Envía los datos del pedido al backend para tramitarlo
          return fetch('/pedidos/tramitar-pedido', {
              method: 'POST',
              body: formData2
            })
            .then(response => response.json())
            .then(data => {
              if (!data.success) {
                alert("Error al tramitar pedido: " + (data.error || ""));
                throw new Error("Error al tramitar pedido");
              }

              var form = document.getElementById('datosForm');
              var formData = new FormData(form);

              // Forzar recolección de los valores actuales del formulario
              formData.set('nombre', form.nombre.value);
              formData.set('apellidos', form.apellidos.value);
              formData.set('email', form.email.value);
              formData.set('telefono', form.telefono.value);
              formData.set('direccion', form.direccion.value);
              formData.set('importe', "<?php echo number_format($importe, 2, '.', ''); ?>");

              formData.append('paypal_name', detalles.payer.name.given_name + ' ' + detalles.payer.name.surname);
              formData.append('paypal_email', detalles.payer.email_address);

              // Envía los datos para enviar el correo de confirmación
              return fetch('enviar_correo', {
                method: 'POST',
                body: formData
              });
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                // Redirige a la página de compra completada si todo fue bien
                window.location.href = "/pasarela-pago/completado";
              } else {
                alert("Error al guardar el pedido");
              }
            })
            .catch(() => {
              alert("Ha ocurrido un error al procesar el pedido.");
            });
        });
      },
      onCancel: function(data) {
        // Si el usuario cancela el pago en PayPal
        alert("Pago cancelado");
        console.log(data);
      }
    }).render("#paypal-buttom-conteiner");
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>