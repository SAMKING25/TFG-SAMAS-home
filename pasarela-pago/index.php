<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>paypal</title>

  <script src="https://www.paypal.com/sdk/js?client-id=AZiNIbkuxCM_s2y_iYwPg7V4zhQKzZbSJhN_y0P7_Pl5hDT7l3bAdsy8VoRzicjIA7r3JnzwT8e_TTJK&currency=EUR"></script>

  <style>
    body {
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: #f5f5f5;
      font-family: Arial, sans-serif;
    }

    #paypal-buttom-conteiner {
      padding: 40px;
      background: white;
      border-radius: 16px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
      transform: scale(1.4); /* Aumenta el tamaño visual del botón */
      transition: transform 0.3s ease;
    }

    #paypal-buttom-conteiner:hover {
      transform: scale(1.45);
    }
  </style>
</head>

<body>
  <div id="paypal-buttom-conteiner"></div>

  <!--
    Esta creado para hacer prueba con sandbox el cual paypal te da para poder hacer pruebas de pagos para los
    desarrolladores, te dan un usuario y contraseña fake para hacer el pago
  -->

  <script>
    paypal.Buttons({
      createOrder: function (data, actions) {
        return actions.order.create({
          purchase_units: [{
            amount: {
              value: 100 // precio
            }
          }]
        });
      },
      onApprove: function (data, actions) {
        return actions.order.capture().then(function (detalles) {
          window.location.href = "completado.html";
        });
      },
      onCancel: function (data) {
        alert("Pago cancelado");
        console.log(data);
      }
    }).render("#paypal-buttom-conteiner");
  </script>
</body>

</html>
