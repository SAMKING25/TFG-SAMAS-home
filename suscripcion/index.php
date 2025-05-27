<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAMAS HOME - suscripcion</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="shortcut icon" href="/img/logos/logo-marron-nobg.ico" />
    <!-- Archivo CSS personalizado -->
    <link rel="stylesheet" href="/css/landing.css" />
    <!--search-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @font-face {
            font-family: "TrebuchetMS";
            src: url("/fonts/TrebuchetMS.ttf");
            font-weight: normal;
            font-style: normal;
        }

        html {
            font-family: "TrebuchetMS";
        }

        body {
            background-color: rgb(247, 229, 203);
        }

        .pricing-card {
            display: flex;
            flex-direction: column;
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            min-width: 280px;
        }

        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .pricing-header {
            border-radius: 15px 15px 0 0;
            padding: 2rem;
        }

        .pricing-header h3,
        .pricing-header .display-4 {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pricing-header.basica {
            background-color: rgba(163, 144, 130, 1);
        }

        .pricing-header.premium {
            background-color: rgba(146, 116, 71, 1);
        }

        .pricing-header.vip {
            background-color: rgba(165, 125, 49, 1);
        }

        .pricing-features {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2rem;
        }

        .pricing-features .text-center {
            margin-top: auto;
        }

        .btn-custom {
            border-radius: 50px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            transform: scale(1.05);
        }

        .Básica {
            background-color: rgba(163, 144, 130, 1);
        }

        .Premium {
            background-color: rgba(146, 116, 71, 1);
        }

        .VIP {
            background-color: rgba(165, 125, 49, 1);
        }

        .feature-icon {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }
    </style>
    <?php
        error_reporting(E_ALL);
        ini_set("display_errors", 1);

        require('../util/conexion.php');

        session_start();
        ?>
</head>

<body>
    <?php include('../navbar.php'); ?>

    <div class="container py-5">
        <h2 class="text-center mb-5">¡Escoge el plan perfecto para ti!</h2>

        <?php
            $sql = "SELECT * FROM suscripciones ORDER BY id_suscripcion ASC";
            $suscripciones = $_conexion->query($sql);
            $i = 0;
        ?>

        <div class="row justify-content-center g-4">
            <?php while ($suscripcion = $suscripciones->fetch_assoc()): ?>
                <div class="col-12 col-md-6 col-lg-4 d-flex">
                    <div class="card h-100 pricing-card w-100">
                        <div class="pricing-header <?php echo $suscripcion['nombre'] ?> text-white text-center">
                            <h3 class="mb-0"><?php echo $suscripcion['nombre'] ?></h3>
                            <div class="display-4 fw-bold my-3"><?php echo $suscripcion['precio'] ?>€</div>
                            <p class="mb-0">cada mes</p>
                        </div>
                        <div class="card-body pricing-features d-flex flex-column justify-content-between">
                            <ul class="list-unstyled">
                                <?php
                                    $usos = $suscripcion['max_usos_plano'];
                                    if ($usos == -1) {
                                        $texto_uso = "Uso infinito del plano";
                                    } elseif ($usos == 1) {
                                        $texto_uso = "1 uso del plano por mes";
                                    } else {
                                        $texto_uso = "$usos usos del plano por mes";
                                    }
                                ?>
                                <li class="mb-3">
                                    <svg class="feature-icon text-success" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                    </svg>
                                    <?php echo $texto_uso ?></li>
                                <li class="mb-3">
                                    <svg class="feature-icon text-success" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                    </svg>
                                    Nuevas ofertas</li>
                                <li class="mb-3">
                                    <?php if ($i === 0): ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x text-danger feature-icon" viewBox="0 0 16 16">
                                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                        </svg>
                                        Descuentos especiales
                                    <?php else: ?>
                                        <svg class="feature-icon text-success" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Descuentos especiales
                                    <?php endif; ?>
                                </li>
                                <li class="mb-3">
                                    <?php if ($i < 2): ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x text-danger feature-icon" viewBox="0 0 16 16">
                                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                        </svg>
                                        Soporte prioritario
                                    <?php else: ?>
                                        <svg class="feature-icon text-success" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Soporte prioritario
                                    <?php endif; ?>
                                </li>
                            </ul>
                            <div class="text-center">
                                <?php if ($i === 0): ?>
                                    <a href="#" class="btn btn-outline-secondary btn-custom disabled">Plan Actual</a>
                                <?php else: ?>
                                    <a href="/pasarela-pago/" class="btn btn-custom <?php echo $suscripcion['nombre'] ?>">Adquirir</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $i++; ?>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>


</html>