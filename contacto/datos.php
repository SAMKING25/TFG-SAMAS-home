<?php
// Dirección de correo destino (empresa)
$destino = "samashome1@gmail.com"; // Tu correo de empresa

// Recoger datos del formulario con valores por defecto si no existen
$nombre = $_POST["nombre"] ?? '';
$apellido = $_POST["apellido"] ?? '';
$correo = $_POST["email"] ?? ''; // Cambia a "email" si tu input se llama así
$asunto = $_POST["asunto"] ?? 'Sin asunto';
$mensaje = $_POST["mensaje"] ?? '';

// URL del logo para el correo
$logo_url = "https://samas-home.com/img/logos/logo-marron-nobg.png";

// Construir el mensaje HTML del correo
$mensajeCompleto = '
<html>
<head>
    <meta charset="UTF-8">
    <title>Nuevo mensaje de contacto</title>
</head>
<body style="font-family: Montserrat, Arial, sans-serif; background: #f7e5cb; margin:0; padding:0;">
    <div style="max-width: 480px; margin: 40px auto; background: #fff8f1; border-radius: 18px; box-shadow: 0 4px 24px #a3908240; padding: 32px 24px; text-align: center;">
        <img src="' . $logo_url . '" alt="SAMAS home" style="width: 120px; margin-bottom: 18px; filter: drop-shadow(0 2px 8px #a39082aa);">
        <h2 style="color: #a57d31; margin-bottom: 8px;">Nuevo mensaje de contacto</h2>
        <p style="color: #6d4c1b; font-size: 1.08rem; margin-bottom: 28px;">
            Has recibido un nuevo mensaje desde el formulario de contacto:
        </p>
        <div style="background: #f7e5cb; color:rgb(0, 0, 0); font-size: 1.1rem; border-radius: 12px; padding: 18px 0; margin-bottom: 24px; text-align:left;">
            <strong>Nombre:</strong> ' . htmlspecialchars($nombre) . '<br>
            <strong>Apellidos:</strong> ' . htmlspecialchars($apellido) . '<br>
            <strong>Correo:</strong> ' . htmlspecialchars($correo) . '<br>
            <strong>Asunto:</strong> ' . htmlspecialchars($asunto) . '<br>
            <strong>Mensaje:</strong><br>' . nl2br(htmlspecialchars($mensaje)) . '
        </div>
        <hr style="margin: 32px 0 16px 0; border: none; border-top: 1.5px solid #f7e5cb;">
        <p style="color: #bdbdbd; font-size: 0.92rem;">SAMAS home · Málaga</p>
    </div>
</body>
</html>
';

// Encabezados para el correo HTML
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: info@samas-home.com\r\n";
$headers .= "Reply-To: $correo\r\n";

// Enviar el correo
$enviar = mail($destino, $asunto, $mensajeCompleto, $headers);

// Redirigir según resultado del envío
if ($enviar) {
    header("Location: /contacto/mensaje-enviado");
    exit;
} else {
    header("Location: /contacto/?error=1");
    exit;
}
