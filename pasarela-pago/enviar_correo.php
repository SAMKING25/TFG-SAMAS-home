<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $paypal_name = $_POST['paypal_name'] ?? '';
    $paypal_email = $_POST['paypal_email'] ?? '';
    $importe = number_format($_POST['importe'], 2, ',', '.');

    $asunto = "Confirmación de compra - SAMAS HOME";

    // Cambia la URL del logo por la tuya
    $logo_url = "https://samas-home.com/img/logos/logo-marron-nobg.png";

    $mensajeCompleto = '
    <html>
    <head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Cardo:wght@700&display=swap" rel="stylesheet">
    </head>
    <body style="background: #f4f4f4; margin:0; padding:0;">
    <div style="max-width: 480px; margin: 40px auto; background: #fff8f1; border-radius: 18px; box-shadow: 0 4px 24px #a3908240; padding: 32px 24px; text-align: center;">
        <img src="' . $logo_url . '" alt="SAMAS HOME" style="width: 120px; margin-bottom: 18px; filter: drop-shadow(0 2px 8px #a39082aa);">
        <h2 style="color: #a57d31; font-family:\'Montserrat\', Arial, sans-serif; margin-bottom: 8px; font-size: 1.7rem; letter-spacing: 1px; font-weight:700;">
            ¡Gracias por tu compra!
        </h2>
        <p style="color: #6d4c1b; font-size: 1.08rem; margin-bottom: 28px;">
        Hemos recibido tu pedido y lo estamos procesando.<br>
        Aquí tienes el resumen de tu compra:
        </p>
        <div style="background: #fff; color: #333; border-radius: 12px; padding: 18px 18px 12px 18px; margin-bottom: 24px; text-align: left; box-shadow: 0 2px 8px #a3908240;">
        <div style="margin-bottom: 8px;"><span style="color:#007acc;font-weight:500;">Nombre:</span> ' . htmlspecialchars($nombre . ' ' . $apellidos) . '</div>
        <div style="margin-bottom: 8px;"><span style="color:#007acc;font-weight:500;">Email:</span> <a href="mailto:' . htmlspecialchars($email) . '" style="color:#007acc;">' . htmlspecialchars($email) . '</a></div>
        <div style="margin-bottom: 8px;"><span style="color:#007acc;font-weight:500;">Teléfono:</span> ' . htmlspecialchars($telefono) . '</div>
        <div style="margin-bottom: 8px;"><span style="color:#007acc;font-weight:500;">Dirección:</span> ' . htmlspecialchars($direccion) . '</div>
        <div style="margin-bottom: 0;"><span style="color:#a57d31;font-weight:600;">Precio total:</span> ' . $importe . ' €</div>
        </div>
        <div style="background: #fccb90; color: #a39082; border-radius: 12px; padding: 16px 18px 10px 18px; margin-bottom: 24px; text-align: left;">
        <div style="color: #8d5d33; font-weight: bold; margin-bottom: 8px; font-size: 1.08rem;">Datos de PayPal</div>
        <div style="margin-bottom: 6px;"><span style="color:#007acc;font-weight:500;">Nombre PayPal:</span> ' . htmlspecialchars($paypal_name) . '</div>
        <div><span style="color:#007acc;font-weight:500;">Email PayPal:</span> <a href="mailto:' . htmlspecialchars($paypal_email) . '" style="color:#007acc;">' . htmlspecialchars($paypal_email) . '</a></div>
        </div>
        <p style="color: #a39082; font-size: 0.98rem; margin-bottom: 0;">
        En breve recibirás tu pedido.<br>
        ¡Gracias por confiar en <strong style="color:#a57d31;">SAMAS HOME</strong>!
        </p>
        <hr style="margin: 32px 0 16px 0; border: none; border-top: 1.5px solid #f7e5cb;">
        <p style="color: #bdbdbd; font-size: 0.92rem; margin:0;">SAMAS HOME · Málaga</p>
    </div>
    </body>
    </html>
    ';

    $from = "From: info@samas-home.com\r\n";
    $from .= "Reply-To: $email\r\n";
    $from .= "MIME-Version: 1.0\r\n";
    $from .= "Content-Type: text/html; charset=UTF-8\r\n";

    $destino_empresa = "samashome1@gmail.com";
    $enviado_empresa = mail($destino_empresa, $asunto, $mensajeCompleto, $from);
    $enviado_cliente = mail($email, $asunto, $mensajeCompleto, $from);

    // Devuelve JSON para AJAX
    header('Content-Type: application/json');
    if ($enviado_empresa || $enviado_cliente) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo enviar el correo']);
    }
    exit;
}
