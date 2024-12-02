<?php
// Incluir el archivo de conexión
require_once 'includes/config.php';

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


    <!-- Page Header Start -->
    <div class="container-fluid page-header">
        <h1 class="display-3 text-uppercase text-white mb-3">Lista de Autos</h1>
    </div>
    <!-- Page Header Start -->


    <!-- Rent A Car Start -->
    <div class="container-fluid py-5">
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