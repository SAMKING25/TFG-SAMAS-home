
<?php
if (!isset($_POST['importe'])) {
  echo "No se recibió el importe. <a href='/carrito/index.php'>Volver al carrito</a>";
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link rel="shortcut icon" href="./img/logos/logo-marron-nobg.ico" />
  <link rel="stylesheet" href="../css/landing.css" />
  <script src="https://www.paypal.com/sdk/js?client-id=AZiNIbkuxCM_s2y_iYwPg7V4zhQKzZbSJhN_y0P7_Pl5hDT7l3bAdsy8VoRzicjIA7r3JnzwT8e_TTJK&currency=EUR"></script>
  <style>
    body {
      background: #f6f6f6;
    }
    .main-content {
      min-height: 90vh; /* Antes: 100vh. Así el contenido no ocupa toda la pantalla */
      display: flex;
      align-items: flex-start;
      justify-content: center;
      padding-top: 2rem;
      padding-bottom: 1rem; /* Añadido para menos espacio abajo */
    }
    .form-card {
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 8px 32px rgba(60,60,120,0.12);
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
        padding-bottom: 0.5rem; /* Menos espacio abajo en móvil */
        min-height: 90vh; /* Un poco más pequeño en móvil */
      }
    }
  </style>
</head>
<body>
  <?php include('../navbar.php'); ?>
  <div class="main-content">
    <div class="form-card">
      <h3 class="mb-4 text-center">Datos para la compra</h3>
      <form id="datosForm" novalidate>
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
    (function () {
      'use strict';
      var form = document.getElementById('datosForm');
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    })();

    paypal.Buttons({
      createOrder: function(data, actions) {
        var form = document.getElementById('datosForm');
        // Validación 
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
        return actions.order.capture().then(function(detalles) {
          var form = document.getElementById('datosForm');
          var formData = new FormData(form);
          formData.append('paypal_name', detalles.payer.name.given_name + ' ' + detalles.payer.name.surname);
          formData.append('paypal_email', detalles.payer.email_address);

          fetch('enviar_correo.php', {
            method: 'POST',
            body: formData
          }).then(() => {
            window.location.href = "completado.html";
          });
        });
      },
      onCancel: function(data) {
        alert("Pago cancelado");
        console.log(data);
      }
    }).render("#paypal-buttom-conteiner");
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>