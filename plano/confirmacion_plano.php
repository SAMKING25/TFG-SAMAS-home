<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("location: /suscripcion/");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Confirmar acceso al plano</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/css/landing.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(120deg, #f8f6f2 0%, #f4e5cc 100%);
            font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif;
            color: #444;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                title: "¿Estás seguro?",
                text: "¿Quieres ir al plano? Se perderá un uso del plano disponible.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, ir al plano",
                cancelButtonText: "Cancelar",
                background: "#fff",
                color: "#444",
                customClass: {
                    title: 'fw-bold',
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-danger'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "/plano/";
                } else {
                    window.location.href = "/"; // O la página que prefieras
                }
            });
        });
    </script>
</body>

</html>