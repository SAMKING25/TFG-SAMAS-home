<?php
$destino = "samashome1@gmail.com";

$nombre = $_POST["nombre"];
$apellido = $_POST["apellido"];
$correo = $_POST["correo"];
$asunto = $_POST["asunto"];
$mensaje = $_POST["mensaje"];

$header = "Enviado desde el formulario de contacto"
$mensajeCompleto =  "Nombre  : " . $nombre .
                    "\n" . "Apellidos  : " . $apellido .
                    "\n" . "Correo  : " . $correo .
                    "\n" . "Asunto  : " . $asunto .
                    "\n" . "Mensaje  : " . $mensaje .;
                    
$enviar = mail($destino, $asunto, $mensajeCompleto, $header);
if($enviar){
    echo "<script> alert('Tu mensaje fue enviado correctamente.') <script>";
}else{
    echo "<script> alert('Tu mensaje no fue enviado.') <script>";
}

} 
?>