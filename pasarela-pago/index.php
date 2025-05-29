<?php
if (!isset($_POST['importe'])) {
  echo "No se recibió el importe. <a href='/carrito/index.php'>Volver al carrito</a>";
  exit;
}

$importe = floatval($_POST['importe']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>paypal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link id="favicon" rel="shortcut icon" href="/img/logos/loguito_gris.png"/> 
  <link rel="stylesheet" href="../css/landing.css" />
  <script src="https://www.paypal.com/sdk/js?client-id=AdqsJ63fSAtdJRDBbE-PH4NKFJuBJAnnTMG1NQUIu22PoUhSnYKQrBGPWBzg0ZFk6DAYOROp5g3zTkZu&currency=EUR"></script>

  <style>
    #paypal-buttom-conteiner {
      padding: 40px;
      background: white;
      border-radius: 16px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
      transform: scale(1.4);
      /* Aumenta el tamaño visual del botón */
      transition: transform 0.3s ease;
    }

    #paypal-buttom-conteiner:hover {
      transform: scale(1.45);
    }

    .main-content {
      flex: 1 0 auto;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 60vh;
      /* Ajusta según el tamaño del footer/navbar */
      width: 100%;
      background: transparent;
    }

    footer {
      flex-shrink: 0;
    }
  </style>
</head>

<body>
  <?php include('../navbar.php'); ?>
  <div class="main-content">
    <div id="paypal-buttom-conteiner"></div>
  </div>
  <?php include('../footer.php'); ?>
  <?php include('../cookies.php'); ?>
  <?php include('../udify-bot.php'); ?>

  <script>
    paypal.Buttons({
      createOrder: function(data, actions) {
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
          window.location.href = "completado.html";
        });
      },
      onCancel: function(data) {
        alert("Pago cancelado");
        console.log(data);
      }
    }).render("#paypal-buttom-conteiner");
  </script>
</body>

</html>