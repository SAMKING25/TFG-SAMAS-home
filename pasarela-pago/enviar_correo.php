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
    $mensaje = "
    <h2>¡Gracias por tu compra en SAMAS HOME!</h2>
    <p><b>Nombre:</b> $nombre $apellidos</p>
    <p><b>Email:</b> $email</p>
    <p><b>Teléfono:</b> $telefono</p>
    <p><b>Dirección:</b> $direccion</p>
    <hr>
    <p><b>Datos de PayPal:</b></p>
    <p><b>Nombre PayPal:</b> $paypal_name</p>
    <p><b>Email PayPal:</b> $paypal_email</p>
    <p>En breve recibirás tu pedido. ¡Gracias por confiar en nosotros!</p>
    ";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: SAMAS HOME <info@samas-home.com>\r\n";

    $enviado = mail($email, $asunto, $mensaje, $headers);
    if (!$enviado) {
        file_put_contents('error_mail.log', "No se pudo enviar el correo a $email\n", FILE_APPEND);
    }
}
?>