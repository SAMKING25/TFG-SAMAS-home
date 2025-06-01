<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $paypal_name = $_POST['paypal_name'] ?? '';
    $paypal_email = $_POST['paypal_email'] ?? '';

    $asunto = "Confirmación de compra - SAMAS HOME";
    $mensajeCompleto =
        "Nombre: $nombre $apellidos\n" .
        "Email: $email\n" .
        "Teléfono: $telefono\n" .
        "Dirección: $direccion\n" .
        "--------------------------\n" .
        "Datos de PayPal:\n" .
        "Nombre PayPal: $paypal_name\n" .
        "Email PayPal: $paypal_email\n" .
        "--------------------------\n" .
        "En breve recibirás tu pedido. ¡Gracias por confiar en nosotros!";

    // Headers igual que en datos.php
    $from = "From: info@samas-home.com\r\n";
    $from .= "Reply-To: $email\r\n";
    $from .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Enviar a la empresa
    $destino_empresa = "samashome1@gmail.com";
    $enviado_empresa = mail($destino_empresa, $asunto, $mensajeCompleto, $from);

    // Enviar al cliente
    $enviado_cliente = mail($email, $asunto, $mensajeCompleto, $from);

    if ($enviado_empresa || $enviado_cliente) {
        header("Location: /pasarela-pago/completado.html");
        exit;
    } else {
        header("Location: /pasarela-pago/completado.html?error=1");
        exit;
    }
}
?>