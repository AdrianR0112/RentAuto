<?php
// Incluir el archivo de conexión
session_start();
require_once 'includes/config.php';
error_reporting(0);

// Verificar si se ha proporcionado un ID de vehículo
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de vehículo no proporcionado.");
}

$vehicleId = intval($_GET['id']);

// Consulta SQL para obtener los detalles del vehículo
$sql = "SELECT * FROM tblvehicles WHERE id = :id";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':id', $vehicleId, PDO::PARAM_INT);
$stmt->execute();

// Verificar si se encontró el vehículo
if ($stmt->rowCount() == 0) {
    die("Vehículo no encontrado.");
}

$vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

// Consulta para obtener vehículos relacionados (misma categoría)
$relatedSql = "SELECT id, TituloVehiculo, AnoModelo, Transmision, PrecioPorDia, Imagen1 
               FROM tblvehicles 
               WHERE Categoria = :categoria 
               AND id != :id 
               LIMIT 4";
$relatedStmt = $dbh->prepare($relatedSql);
$relatedStmt->bindParam(':categoria', $vehicle['Categoria'], PDO::PARAM_STR);
$relatedStmt->bindParam(':id', $vehicleId, PDO::PARAM_INT);
$relatedStmt->execute();
$relatedVehicles = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);
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

    <!-- Inicio de Encabezado de Página -->
    <div class="container-fluid page-header">
        <h1 class="display-3 text-uppercase text-white mb-3">Detalles del Automóvil</h1>
    </div>
    <!-- Fin de Encabezado de Página -->

    <!-- Inicio de Detalles -->
    <div class="container-fluid pt-5">
        <div class="container pt-5">
            <div class="row">
                <div class="col-lg-8 mb-5">
                    <h1 class="display-4 text-uppercase mb-5">
                        <?php echo htmlspecialchars($vehicle['TituloVehiculo']); ?>
                    </h1>
                    <div class="col-lg-6 mb-4">
                        <img class="img-fluid w-100" src="img/cars/<?php echo htmlspecialchars($vehicle['Imagen1']); ?>"
                            alt="<?php echo htmlspecialchars($vehicle['TituloVehiculo']); ?>">
                    </div>
                    <p><?php echo htmlspecialchars($vehicle['DescripcionVehiculo']); ?></p>
                    <div class="row pt-2">
                        <div class="col-md-3 col-6 mb-2">
                            <i class="fa fa-car text-primary mr-2"></i>
                            <span>Modelo: <?php echo htmlspecialchars($vehicle['AnoModelo']); ?></span>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <i class="fa fa-cogs text-primary mr-2"></i>
                            <span><?php echo htmlspecialchars($vehicle['Transmision']); ?></span>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <i class="fa fa-gas-pump text-primary mr-2"></i>
                            <span><?php echo htmlspecialchars($vehicle['TipoCombustible']); ?></span>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <i class="fa fa-user text-primary mr-2"></i>
                            <span><?php echo htmlspecialchars($vehicle['CapacidadAsientos']); ?> Pasajeros</span>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <i class="fa fa-eye text-primary mr-2"></i>
                            <span>GPS: <?php echo $vehicle['GPS'] ? 'Sí' : 'No'; ?></span>
                        </div>
                        <div class="col-md-3 col-6 mb-2">
                            <i class="fa fa-snowflake text-primary mr-2"></i>
                            <span>A/C: <?php echo $vehicle['AireAcondicionado'] ? 'Sí' : 'No'; ?></span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-5">
                    <div class="bg-secondary p-5">
                        <h3 class="text-primary text-center mb-4">Verificar Disponibilidad</h3>
                        <div class="form-group">
                            <select class="custom-select px-4" style="height: 50px;">
                                <option selected>Lugar de Recogida</option>
                                <option value="1">Ubicación 1</option>
                                <option value="2">Ubicación 2</option>
                                <option value="3">Ubicación 3</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="custom-select px-4" style="height: 50px;">
                                <option selected>Lugar de Entrega</option>
                                <option value="1">Ubicación 1</option>
                                <option value="2">Ubicación 2</option>
                                <option value="3">Ubicación 3</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="date" id="date1" data-target-input="nearest">
                                <input type="text" class="form-control p-4 datetimepicker-input"
                                    placeholder="Fecha de Recogida" data-target="#date1" data-toggle="datetimepicker" />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="time" id="time1" data-target-input="nearest">
                                <input type="text" class="form-control p-4 datetimepicker-input"
                                    placeholder="Hora de Recogida" data-target="#time1" data-toggle="datetimepicker" />
                            </div>
                        </div>
                        <div class="form-group">
                            <select class="custom-select px-4" style="height: 50px;">
                                <option selected>Seleccionar Personas</option>
                                <option value="1">Persona 1</option>
                                <option value="2">Persona 2</option>
                                <option value="3">Persona 3</option>
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <button class="btn btn-primary btn-block" type="submit" style="height: 50px;">Verificar
                                Ahora</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin de Detalles -->

    <!-- Inicio de Autos Relacionados -->
    <!-- <div class="container-fluid pb-5">
        <div class="container pb-5">
            <h2 class="mb-4">Autos Relacionados</h2>
            <div class="owl-carousel related-carousel position-relative" style="padding: 0 30px;">
                <?php foreach ($relatedVehicles as $relatedVehicle): ?>
                    <div class="rent-item">
                        <img class="img-fluid mb-4"
                            src="img/cars/<?php echo htmlspecialchars($relatedVehicle['Imagen1']); ?>" alt="">
                        <h4 class="text-uppercase mb-4"><?php echo htmlspecialchars($relatedVehicle['TituloVehiculo']); ?>
                        </h4>
                        <div class="d-flex justify-content-center mb-4">
                            <div class="px-2">
                                <i class="fa fa-car text-primary mr-1"></i>
                                <span><?php echo htmlspecialchars($relatedVehicle['AnoModelo']); ?></span>
                            </div>
                            <div class="px-2 border-left border-right">
                                <i class="fa fa-cogs text-primary mr-1"></i>
                                <span><?php echo htmlspecialchars($relatedVehicle['Transmision']); ?></span>
                            </div>
                            <div class="px-2">
                                <i class="fa fa-road text-primary mr-1"></i>
                                <span>$<?php echo htmlspecialchars($relatedVehicle['PrecioPorDia']); ?>/Día</span>
                            </div>
                        </div>
                        <a class="btn btn-primary px-3" href="detail.php?id=<?php echo $relatedVehicle['id']; ?>">
                            Ver detalles
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div> -->
    <!-- Fin de Autos Relacionados -->

    <!-- Inicio de Autos Relacionados -->
    <div class="container-fluid py-5">
        <div class="container">
            <h2 class="text-uppercase mb-4">Autos Relacionados</h2>
            <div class="row">
                <?php
                // Mostrar vehículos relacionados
                if (!empty($relatedVehicles)) {
                    foreach ($relatedVehicles as $relatedVehicle) {
                        ?>
                        <div class="col-lg-4 col-md-6 mb-2">
                            <div class="rent-item mb-4">
                                <img class="img-fluid mb-4"
                                    src="img/cars/<?php echo htmlspecialchars($relatedVehicle['Imagen1']); ?>"
                                    alt="<?php echo htmlspecialchars($relatedVehicle['TituloVehiculo']); ?>">
                                <h4 class="text-uppercase mb-2">
                                    <?php echo htmlspecialchars($relatedVehicle['TituloVehiculo']); ?>
                                </h4>
                                <div class="px-2 mb-2">
                                    <span>$<?php echo htmlspecialchars($relatedVehicle['PrecioPorDia']); ?>/Día</span>
                                </div>
                                <div class="d-flex justify-content-center mb-4">
                                    <div class="px-2">
                                        <i class="fa fa-car text-primary mr-1"></i>
                                        <span><?php echo htmlspecialchars($relatedVehicle['AnoModelo']); ?></span>
                                    </div>
                                    <div class="px-2 border-left border-right">
                                        <i class="fa fa-cogs text-primary mr-1"></i>
                                        <span><?php echo htmlspecialchars($relatedVehicle['Transmision']); ?></span>
                                    </div>
                                </div>
                                <a class="btn btn-primary px-3" href="detail.php?id=<?php echo $relatedVehicle['id']; ?>">
                                    Ver detalles
                                </a>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<div class='col-12'><p>No hay vehículos relacionados disponibles.</p></div>";
                }
                ?>
            </div>
        </div>
    </div>
    <!-- Fin de Autos Relacionados -->


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