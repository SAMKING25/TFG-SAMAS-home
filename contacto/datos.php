<?php
$destino = "samashome1@gmail.com"; // Tu correo de empresa

$nombre = $_POST["nombre"] ?? '';
$apellido = $_POST["apellido"] ?? '';
$correo = $_POST["email"] ?? ''; // Cambia a "email" si tu input se llama así
$asunto = $_POST["asunto"] ?? 'Sin asunto';
$mensaje = $_POST["mensaje"] ?? '';

$mensajeCompleto =  "Nombre: " . $nombre . "\n" .
                    "Apellidos: " . $apellido . "\n" .
                    "Correo: " . $correo . "\n" .
                    "Asunto: " . $asunto . "\n" .
                    "Mensaje: " . $mensaje;

// Usa un correo de tu dominio como remitente
$from = "From: info@samas-home.com\r\n"; 
$from .= "Reply-To: $correo\r\n";
$from .= "Content-Type: text/plain; charset=UTF-8\r\n";

$enviar = mail($destino, $asunto, $mensajeCompleto, $from);

if ($enviar) {
    header("Location: /contacto/mensaje-enviado");
    exit;
} else {
    header("Location: /contacto/?error=1");
    exit;
}
?>