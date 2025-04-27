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
    body {
        background-color: #f8f9fa;
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
        <h2 class="text-center mb-5">Choose Your Perfect Plan</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <!-- Basic Plan -->
            <div class="col">
                <div class="card h-100 pricing-card">
                    <div class="pricing-header bg-primary text-white text-center">
                        <h3 class="mb-0">Basic</h3>
                        <div class="display-4 fw-bold my-3">$9.99</div>
                        <p class="mb-0">per month</p>
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
                                10 GB Storage
                            </li>
                            <li class="mb-3">
                                <svg class="feature-icon text-primary" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                2 Users
                            </li>
                            <li class="mb-3">
                                <svg class="feature-icon text-primary" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Email Support
                            </li>
                        </ul>
                        <div class="text-center mt-4">
                            <a href="#" class="btn btn-outline-primary btn-custom">Get Started</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pro Plan -->
            <div class="col">
                <div class="card h-100 pricing-card">
                    <div class="pricing-header bg-success text-white text-center">
                        <h3 class="mb-0">Pro</h3>
                        <div class="display-4 fw-bold my-3">$19.99</div>
                        <p class="mb-0">per month</p>
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
                                50 GB Storage
                            </li>
                            <li class="mb-3">
                                <svg class="feature-icon text-success" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                5 Users
                            </li>
                            <li class="mb-3">
                                <svg class="feature-icon text-success" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Priority Support
                            </li>
                        </ul>
                        <div class="text-center mt-4">
                            <a href="#" class="btn btn-success btn-custom">Get Started</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enterprise Plan -->
            <div class="col">
                <div class="card h-100 pricing-card">
                    <div class="pricing-header bg-dark text-white text-center">
                        <h3 class="mb-0">Enterprise</h3>
                        <div class="display-4 fw-bold my-3">$49.99</div>
                        <p class="mb-0">per month</p>
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
                                Unlimited Storage
                            </li>
                            <li class="mb-3">
                                <svg class="feature-icon text-dark" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Unlimited Users
                            </li>
                            <li class="mb-3">
                                <svg class="feature-icon text-dark" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                24/7 Dedicated Support
                            </li>
                        </ul>
                        <div class="text-center mt-4">
                            <a href="#" class="btn btn-dark btn-custom">Get Started</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>