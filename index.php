<?php
// Incluir el archivo de conexión
session_start();
require_once 'includes/config.php';
error_reporting(0);
// Consulta SQL para obtener vehículos

$sql = "SELECT id, TituloVehiculo, CapacidadAsientos, TipoCombustible,Transmision, PrecioPorDia, Imagen1 FROM tblvehicles";
$stmt = $dbh->prepare($sql);
$stmt->execute();
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


    <!-- Carousel Start -->
    <div class="container-fluid p-0">
        <div id="header-carousel" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="w-100" src="img/carousel-1.jpg" alt="Image">
                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                        <div class="p-3" style="max-width: 900px;">
                            <h4 class="text-white text-uppercase mb-md-3">Renta un Auto</h4>
                            <h1 class="display-1 text-white mb-md-4">Los Mejores Autos de Renta en Tu Ubicación</h1>
                            <a href="" class="btn btn-primary py-md-3 px-md-5 mt-2">Reserva Ahora</a>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <img class="w-100" src="img/carousel-2.jpg" alt="Image">
                    <div class="carousel-caption d-flex flex-column align-items-center justify-content-center">
                        <div class="p-3" style="max-width: 900px;">
                            <h4 class="text-white text-uppercase mb-md-3">Renta un Auto</h4>
                            <h1 class="display-1 text-white mb-md-4">Autos de Calidad con Kilometraje Ilimitado</h1>
                            <a href="" class="btn btn-primary py-md-3 px-md-5 mt-2">Reserva Ahora</a>
                        </div>
                    </div>
                </div>
            </div>
            <a class="carousel-control-prev" href="#header-carousel" data-slide="prev">
                <div class="btn btn-dark" style="width: 45px; height: 45px;">
                    <span class="carousel-control-prev-icon mb-n2"></span>
                </div>
            </a>
            <a class="carousel-control-next" href="#header-carousel" data-slide="next">
                <div class="btn btn-dark" style="width: 45px; height: 45px;">
                    <span class="carousel-control-next-icon mb-n2"></span>
                </div>
            </a>
        </div>
    </div>
    <!-- Carousel End -->

    <!-- Rent A Car Start -->
    <div class="container-fluid">
        <div class="container pt-5 pb-3">
            <!-- <h1 class="display-4 text-uppercase text-center mb-5">Encuentra un auto</h1> -->
            <div class="row">
                <?php
                // Mostrar los resultados de la consulta
                if ($stmt->rowCount() > 0) {
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                        <div class="col-lg-4 col-md-6 mb-2">
                            <div class="rent-item mb-4">
                                <img class="img-fluid mb-4" src="img/cars/<?php echo htmlspecialchars($row['Imagen1']); ?>"
                                    alt="">
                                <h4 class="text-uppercase mb-2"><?php echo htmlspecialchars($row['TituloVehiculo']); ?></h4>
                                <div class="px-2 mb-2">
                                    <span>$<?php echo htmlspecialchars($row['PrecioPorDia']); ?>/Día</span>
                                </div>
                                <div class="d-flex justify-content-center mb-4">
                                    <div class="px-2">
                                        <i class="fa fa-user text-primary mr-1"></i>
                                        <span><?php echo htmlspecialchars($row['CapacidadAsientos']); ?></span>
                                    </div>
                                    <div class="px-2 border-left border-right">
                                        <i class="fa fa-gas-pump text-primary mr-1"></i>
                                        <span><?php echo htmlspecialchars($row['TipoCombustible']); ?></span>
                                    </div>
                                    <div class="px-2 border-left border-right">
                                        <i class="fa fa-cogs text-primary mr-1"></i>
                                        <span><?php echo htmlspecialchars($row['Transmision']); ?></span>
                                    </div>
                                </div>
                                <a class="btn btn-primary px-3" href="detail.php?id=<?php echo $row['id']; ?>">
                                    Ver detalles
                                </a>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p>No hay vehículos disponibles en este momento.</p>";
                }
                ?>
            </div>
        </div>
    </div>
    <!-- Rent A Car End -->


    <!-- Banner Start -->
    <div class="container-fluid">
        <div class="container py-5">
            <div class="bg-banner py-5 px-4 text-center">
                <div class="py-5">
                    <h1 class="display-1 text-uppercase text-primary mb-4">30% DE DESCUENTO</h1>
                    <h1 class="text-uppercase text-light mb-4">Oferta Especial Para Nuevos Miembros</h1>
                    <!-- <p class="mb-4">Only for Sunday from 1st Jan to 30th Jan 2045</p> -->
                    <a class="btn btn-primary mt-2 py-3 px-5" href="">Registrate Ahora</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Banner End -->


    <!-- Testimonial Start -->
    <div class="container-fluid">
        <div class="container py-5">
            <h1 class="display-4 text-uppercase text-center mb-5">Opiniones de Nuestros Clientes</h1>
            <div class="owl-carousel testimonial-carousel">
                <div class="testimonial-item d-flex flex-column justify-content-center px-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <img class="img-fluid ml-n4" src="img/testimonial-2.jpg" alt="">
                        <h1 class="display-2 text-white m-0 fa fa-quote-right"></h1>
                    </div>
                    <h4 class="text-uppercase mb-2">María Pérez</h4>
                    <i class="mb-2">Empresaria</i>
                    <p class="m-0">"El servicio fue excelente. El auto estaba en perfectas condiciones y el proceso de
                        reserva fue rápido y sencillo. Sin duda volveré a utilizar sus servicios."</p>
                </div>
                <div class="testimonial-item d-flex flex-column justify-content-center px-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <img class="img-fluid ml-n4" src="img/testimonial-1.jpg" alt="">
                        <h1 class="display-2 text-white m-0 fa fa-quote-right"></h1>
                    </div>
                    <h4 class="text-uppercase mb-2">Carlos Mendoza</h4>
                    <i class="mb-2">Turista</i>
                    <p class="m-0">"La experiencia fue increíble. Encontré el auto perfecto para mi viaje y el personal
                        fue muy amable y atento. Muy recomendable."</p>
                </div>
                <div class="testimonial-item d-flex flex-column justify-content-center px-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <img class="img-fluid ml-n4" src="img/testimonial-4.jpg" alt="">
                        <h1 class="display-2 text-white m-0 fa fa-quote-right"></h1>
                    </div>
                    <h4 class="text-uppercase mb-2">Laura Gómez</h4>
                    <i class="mb-2">Fotógrafa</i>
                    <p class="m-0">"Gracias a esta compañía pude alquilar un auto de gran calidad y recorrer la ciudad
                        sin problemas. Los precios son muy accesibles y el servicio de primera."</p>
                </div>
                <div class="testimonial-item d-flex flex-column justify-content-center px-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <img class="img-fluid ml-n4" src="img/testimonial-3.jpg" alt="">
                        <h1 class="display-2 text-white m-0 fa fa-quote-right"></h1>
                    </div>
                    <h4 class="text-uppercase mb-2">Juan Rivera</h4>
                    <i class="mb-2">Ingeniero</i>
                    <p class="m-0">"La variedad de autos es impresionante y el proceso de alquiler es muy fácil.
                        Disfruté cada momento y recomiendo este servicio a cualquiera que necesite un auto."</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Testimonial End -->

    <!--Proveedores-->
    <?php include('includes/proveedores.php'); ?>
    <!-- /Proveedores -->

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