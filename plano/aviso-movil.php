<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso solo desde tablet u ordenador</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            min-height: 100vh;
            min-width: 100vw;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
        }
        .aviso-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 32px 16px;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 2px 16px rgba(0,0,0,0.06);
            max-width: 90vw;
        }
        img {
            width: 80px;
            margin-bottom: 24px;
        }
        h2 {
            color: #333;
            margin-bottom: 12px;
            font-size: 1.5rem;
        }
        p {
            color: #888;
            margin-bottom: 24px;
            font-size: 1.1rem;
        }
        .btn-inicio {
            display: inline-block;
            padding: 12px 28px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            text-decoration: none;
            transition: background 0.2s;
            cursor: pointer;
        }
        .btn-inicio:hover, .btn-inicio:focus {
            background: #0056b3;
            color: #fff;
        }
        @media (max-width: 480px) {
            .aviso-container {
                padding: 16px 4px;
            }
            h2 {
                font-size: 1.1rem;
            }
            p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="aviso-container">
        <img src="/img/logos/loguito_gris.png" alt="SAMAS HOME">
        <h2>El plano solo está disponible en tablets y ordenadores.</h2>
        <p>Por favor, accede desde un dispositivo compatible.</p>
        <a href="/" class="btn-inicio">Volver a inicio</a>
    </div>
</body>
</html>