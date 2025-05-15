<?php
$destino = "samashome1@gmail.com";

$nombre = $_POST["nombre"] ?? '';
$apellido = $_POST["apellido"] ?? '';
$correo = $_POST["correo"] ?? '';
$asunto = $_POST["asunto"] ?? 'Sin asunto';
$mensaje = $_POST["mensaje"] ?? '';

$mensajeCompleto =  "Nombre: " . $nombre . "\n" .
                    "Apellidos: " . $apellido . "\n" .
                    "Correo: " . $correo . "\n" .
                    "Asunto: " . $asunto . "\n" .
                    "Mensaje: " . $mensaje;

$header = "From: $correo\r\n";
$header .= "Reply-To: $correo\r\n";
$header .= "Content-Type: text/plain; charset=UTF-8\r\n";

$enviar = mail($destino, $asunto, $mensajeCompleto, $header);

if ($enviar) {
    echo "<script>alert('Tu mensaje fue enviado correctamente.');</script>";
} else {
    echo "<script>alert('Tu mensaje no fue enviado.');</script>";
}
?>