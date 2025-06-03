<?php 
    // Función para depurar una cadena de entrada
    // Convierte caracteres especiales en entidades HTML, elimina espacios al inicio y final,
    // y reemplaza múltiples espacios por uno solo
    function depurar(string $entrada) : string{
        $salida = htmlspecialchars($entrada);
        $salida = trim($salida);
        $salida = preg_replace('!\s+!',' ',$salida);
        return $salida;
    }
?>