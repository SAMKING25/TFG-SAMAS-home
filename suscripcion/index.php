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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.2.4/fabric.min.js"></script>
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
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .pricing-header {
            border-radius: 15px 15px 0 0;
            padding: 2rem;
        }

        .pricing-header.basic {
            background-color: rgba(163, 144, 130);
        }

        .pricing-header.premium {
            background-color: rgba(146, 116, 71);
        }

        .pricing-header.vip {
            background-color: rgba(165, 125, 49);
        }

        .pricing-features {
            padding: 2rem;
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

        .btn.premium {
            background-color: rgba(146, 116, 71, 0.7);
        }

        .btn.vip {
            background-color: rgba(165, 125, 49, 0.7);
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
    <?php
    include('../navbar.php');
    ?>
    <div class="container py-5">
        <h2 class="text-center mb-5">¡Escoge el plan perfecto para tí!</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <!-- Basic Plan -->
            <div class="col">
                <div class="card h-100 pricing-card">
                    <div class="pricing-header basic text-white text-center">
                        <h3 class="mb-0">Básica</h3>
                        <div class="display-4 fw-bold my-3">0€</div>
                        <p class="mb-0">cada mes</p>
                    </div>
                    <div class="card-body pricing-features">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <svg class="feature-icon text-primary" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                1 uso del plano por trimestre
                            </li>
                            <li class="mb-3">
                                <svg class="feature-icon text-primary" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Acceso a ofertas genéricas
                            </li>
                        </ul>
                        <div class="text-center mt-4">
                            <a href="#" class="btn btn-outline-secondary btn-custom disabled">Plan Actual</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pro Plan -->
            <div class="col">
                <div class="card h-100 pricing-card">
                    <div class="pricing-header premium text-white text-center">
                        <h3 class="mb-0">Premium</h3>
                        <div class="display-4 fw-bold my-3">10€</div>
                        <p class="mb-0">cada mes</p>
                    </div>
                    <div class="card-body pricing-features">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <svg class="feature-icon text-success" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                5 usos del plano por trimestre
                            </li>
                            <li class="mb-3">
                                <svg class="feature-icon text-success" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Acceso a ofertas mayores
                            </li>
                            <li class="mb-3">
                                <svg class="feature-icon text-success" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Acceso a descuentos exclusivos
                            </li>
                        </ul>
                        <div class="text-center mt-4">
                            <a href="/pasarela-pago/index.php" class="btn premium btn-custom">Adquirir</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enterprise Plan -->
            <div class="col">
                <div class="card h-100 pricing-card">
                    <div class="pricing-header vip text-white text-center">
                        <h3 class="mb-0">VIP</h3>
                        <div class="display-4 fw-bold my-3">25€</div>
                        <p class="mb-0">cada mes</p>
                    </div>
                    <div class="card-body pricing-features">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <svg class="feature-icon text-dark" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Uso infinito del plano
                            </li>
                            <li class="mb-3">
                                <svg class="feature-icon text-dark" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Mayores ofertas exclusivas
                            </li>
                            <li class="mb-3">
                                <svg class="feature-icon text-dark" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Mayores descuentos exclusivos
                            </li>
                            <li class="mb-3">
                                <svg class="feature-icon text-dark" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Prioridad a la hora de atenderte
                            </li>
                        </ul>
                        <div class="text-center mt-4">
                            <a href="/pasarela-pago/index.php" class="btn vip btn-custom">Adquirir</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>