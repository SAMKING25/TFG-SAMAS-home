<?php
    function depurar (string $entrada) : string {
        $salida = htmlspecialchars($entrada);
        $salida = trim($salida);
        $salida = stripcslashes($salida);
        $salida = preg_replace('!\s+!', ' ', $salida);
        return $salida;
    }
?>