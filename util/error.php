<?php
// Obtiene el código de error HTTP, usando un valor por defecto si no se pasa por GET
$codigo_error = http_response_code(); // Esto devuelve el código actual, pero por seguridad usamos fallback

if (isset($_GET['codigo'])) {
    $codigo_error = (int)$_GET['codigo'];
} else {
    $codigo_error = 500; // Por defecto
}

// Define mensajes personalizados según el código de error
switch ($codigo_error) {
    case 400:
        $mensaje1 = "Petición incorrecta.";
        $mensaje2 = "Verifica la URL o los parámetros enviados.";
        break;
    case 401:
        $mensaje1 = "No estás autorizado para ver esta página.";
        $mensaje2 = "Inicia sesión para continuar.";
        break;
    case 403:
        $mensaje1 = "Acceso denegado.";
        $mensaje2 = "No tienes permiso para acceder a esta página.";
        break;
    case 404:
        $mensaje1 = "Página no encontrada.";
        $mensaje2 = "La página que buscas no existe o ha sido movida.";
        break;
    case 500:
        $mensaje1 = "Error interno del servidor.";
        $mensaje2 = "Por favor, intenta más tarde o contacta al administrador.";
        break;
    case 503:
        $mensaje1 = "Servicio no disponible temporalmente.";
        $mensaje2 = "Estamos realizando tareas de mantenimiento. Por favor, vuelve más tarde.";
        break;
    default:
        $mensaje1 = "Ha ocurrido un error inesperado.";
        $mensaje2 = "Por favor, intenta más tarde o contacta al administrador.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada</title>
    <!-- Bootstrap CSS principal -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        /* Estilos generales de la página de error */
        .error-page {
            min-height: 100vh;
            background: linear-gradient(45deg, rgb(255, 255, 255) 0%, rgb(165, 125, 49) 100%);
        }

        .error-container {
            max-width: 600px;
        }

        .error-code {
            font-size: 12rem;
            font-weight: 900;
            background: linear-gradient(to right, #fff, rgba(255, 255, 255, 0.5));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: pulse 2s infinite;
        }

        .error-message {
            color: rgba(255, 255, 255, 0.9);
        }

        /* Animación de pulso para el código de error */
        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Botón de estilo glass para volver al inicio */
        .btn-glass {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            transition: all 0.3s ease;
        }

        .btn-glass:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
        }
    </style>
</head>

<body>
    <div class="error-page d-flex align-items-center justify-content-center">
        <div class="error-container text-center p-4">
            <!-- Código de error grande -->
            <h1 class="error-code mb-0"><?php echo $codigo_error ?></h1>
            <!-- Mensaje principal del error -->
            <h2 class="display-6 error-message mb-3"><?php echo $mensaje1 ?></h2>
            <!-- Mensaje secundario o explicación -->
            <p class="lead error-message mb-5"><?php echo $mensaje2 ?></p>
            <div class="d-flex justify-content-center gap-3">
                <!-- Botón para volver al inicio -->
                <a href="../../" class="btn btn-glass px-4 py-2">Volver al inicio</a>
            </div>
        </div>
    </div>
</body>

</html>