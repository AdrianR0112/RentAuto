<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>ECUA CARS - Alquiler de Autos</title>

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Rubik&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">

    <!-- Librerías de Estilo -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Estilos Personalizados de Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilo de Plantilla -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>

    <!--Header-->
    <?php include('includes/header.php'); ?>
    <!-- /Header -->

    <!-- Inicio del Encabezado de la Página -->
    <div class="container-fluid page-header">
        <h1 class="display-3 text-uppercase text-white mb-3">Acerca de Nosotros</h1>
    </div>
    <!-- Fin del Encabezado de la Página -->


    <!-- Inicio de Acerca de -->
    <div class="container-fluid">
        <div class="container pt-5 pb-3">
            <h1 class="display-4 text-uppercase text-center mb-5">Bienvenido a <span class="text-primary">Ecua
                    Cars</span></h1>
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <img class="w-75 mb-4" src="img/about.png" alt="">
                    <p>En Ecua Cars, nos apasiona ayudarte a explorar el mundo sobre ruedas. Somos una empresa
                        ecuatoriana dedicada al alquiler de autos, comprometida con ofrecer un servicio de calidad,
                        confiable y accesible. Nuestro objetivo es brindarte la libertad de viajar a tu manera, ya sea
                        para negocios, turismo o cualquier aventura que tengas en mente.

                        Con una flota moderna y diversa, garantizamos comodidad y seguridad en cada kilómetro recorrido.
                        <br> En Ecua Cars, no solo rentamos autos; construimos experiencias inolvidables.
                    </p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-lg-4 mb-2">
                    <div class="d-flex align-items-center bg-light p-4 mb-4" style="height: 150px;">
                        <div class="d-flex align-items-center justify-content-center flex-shrink-0 bg-primary ml-n4 mr-4"
                            style="width: 100px; height: 100px;">
                            <i class="fa fa-2x fa-headset text-secondary"></i>
                        </div>
                        <h4 class="text-uppercase m-0">Soporte de Alquiler de Autos 24/7</h4>
                    </div>
                </div>
                <div class="col-lg-4 mb-2">
                    <div class="d-flex align-items-center bg-secondary p-4 mb-4" style="height: 150px;">
                        <div class="d-flex align-items-center justify-content-center flex-shrink-0 bg-primary ml-n4 mr-4"
                            style="width: 100px; height: 100px;">
                            <i class="fa fa-2x fa-car text-secondary"></i>
                        </div>
                        <h4 class="text-light text-uppercase m-0">Reserva de Autos en Cualquier Momento</h4>
                    </div>
                </div>
                <div class="col-lg-4 mb-2">
                    <div class="d-flex align-items-center bg-light p-4 mb-4" style="height: 150px;">
                        <div class="d-flex align-items-center justify-content-center flex-shrink-0 bg-primary ml-n4 mr-4"
                            style="width: 100px; height: 100px;">
                            <i class="fa fa-2x fa-map-marker-alt text-secondary"></i>
                        </div>
                        <h4 class="text-uppercase m-0">Muchas Ubicaciones de Recogida</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin de Acerca de -->


    <!-- Inicio del Banner -->
    <div class="container-fluid">
        <div class="container py-5">
            <div class="row mx-0">
                <div class="col-lg-6 px-0">
                    <div class="px-5 bg-secondary d-flex align-items-center justify-content-between"
                        style="height: 350px;">
                        <img class="img-fluid flex-shrink-0 ml-n5 w-50 mr-4" src="img/banner-left.png"
                            alt="Alquiler de autos en Ecuador">
                        <div class="text-right">
                            <h3 class="text-uppercase text-light mb-3">Viaja sin límites</h3>
                            <p class="mb-4">Encuentra el auto ideal para tus aventuras en cualquier rincón de Ecuador.
                                Ofrecemos vehículos confiables y accesibles.</p>
                            <a class="btn btn-primary py-2 px-4" href="car.php">Explorar Autos</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 px-0">
                    <div class="px-5 bg-dark d-flex align-items-center justify-content-between" style="height: 350px;">
                        <div class="text-left">
                            <h3 class="text-uppercase text-light mb-3">Fácil y rápido</h3>
                            <p class="mb-4">Reserva en pocos pasos y disfruta de un servicio rápido y seguro. Tu
                                comodidad es nuestra prioridad.</p>
                            <a class="btn btn-primary py-2 px-4" href="about.php">Conoce Más</a>
                        </div>
                        <img class="img-fluid flex-shrink-0 mr-n5 w-50 ml-4" src="img/banner-right.png"
                            alt="Alquiler confiable de autos">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin del Banner -->


    <!-- Team Start -->
    <div class="container-fluid">
        <div class="container py-5">
            <div class="owl-carousel team-carousel position-relative" style="padding: 0 30px;">
                <div class="team-item">
                    <img class="img-fluid w-100" src="img/dev.png" alt="">
                    <div class="position-relative py-4">
                        <h4 class="text-uppercase">Nombre</h4>
                        <p class="m-0">Cargo</p>
                        <div
                            class="team-social position-absolute w-100 h-100 d-flex align-items-center justify-content-center">
                            <a class="btn btn-lg btn-primary btn-lg-square mx-1" href="#"><i
                                    class="fab fa-twitter"></i></a>
                            <a class="btn btn-lg btn-primary btn-lg-square mx-1" href="#"><i
                                    class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-lg btn-primary btn-lg-square mx-1" href="#"><i
                                    class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
                <div class="team-item">
                    <img class="img-fluid w-100" src="img/dev.png" alt="">
                    <div class="position-relative py-4">
                        <h4 class="text-uppercase">Nombre</h4>
                        <p class="m-0">Cargo</p>
                        <div
                            class="team-social position-absolute w-100 h-100 d-flex align-items-center justify-content-center">
                            <a class="btn btn-lg btn-primary btn-lg-square mx-1" href="#"><i
                                    class="fab fa-twitter"></i></a>
                            <a class="btn btn-lg btn-primary btn-lg-square mx-1" href="#"><i
                                    class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-lg btn-primary btn-lg-square mx-1" href="#"><i
                                    class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
                <div class="team-item">
                    <img class="img-fluid w-100" src="img/dev.png" alt="">
                    <div class="position-relative py-4">
                        <h4 class="text-uppercase">Nombre</h4>
                        <p class="m-0">Cargo</p>
                        <div
                            class="team-social position-absolute w-100 h-100 d-flex align-items-center justify-content-center">
                            <a class="btn btn-lg btn-primary btn-lg-square mx-1" href="#"><i
                                    class="fab fa-twitter"></i></a>
                            <a class="btn btn-lg btn-primary btn-lg-square mx-1" href="#"><i
                                    class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-lg btn-primary btn-lg-square mx-1" href="#"><i
                                    class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
                <div class="team-item">
                    <img class="img-fluid w-100" src="img/dev.png" alt="">
                    <div class="position-relative py-4">
                        <h4 class="text-uppercase">Nombre</h4>
                        <p class="m-0">Cargo</p>
                        <div
                            class="team-social position-absolute w-100 h-100 d-flex align-items-center justify-content-center">
                            <a class="btn btn-lg btn-primary btn-lg-square mx-1" href="#"><i
                                    class="fab fa-twitter"></i></a>
                            <a class="btn btn-lg btn-primary btn-lg-square mx-1" href="#"><i
                                    class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-lg btn-primary btn-lg-square mx-1" href="#"><i
                                    class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Team End -->

    <!--Footer-->
    <?php include('includes/footer.php'); ?>
    <!-- /Footer -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="fa fa-angle-double-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <script src="js/main.js"></script>
</body>

</html>