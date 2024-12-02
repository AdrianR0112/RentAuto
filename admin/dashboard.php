<?php
session_start();
require_once '../includes/config.php';
error_reporting(0);

// Verificar si el usuario es administrador
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Si no está autenticado, redirigir al login
    header("Location: login.php");
    exit();
}

// Función para obtener estadísticas generales expandidas
function getExpandedStats($dbh)
{
    $stats = array();

    // Total de usuarios
    $stmt = $dbh->query("SELECT COUNT(*) as total FROM tblusers");
    $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Total de vehículos
    $stmt = $dbh->query("SELECT COUNT(*) as total FROM tblvehicles");
    $stats['total_vehicles'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Total de reservas
    $stmt = $dbh->query("SELECT COUNT(*) as total FROM tblbooking");
    $stats['total_bookings'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Ingresos totales (calculado multiplicando días de reserva por precio por día)
    $stmt = $dbh->query("
        SELECT 
            COALESCE(SUM(
                DATEDIFF(FechaHasta, FechaDesde) * v.PrecioPorDia
            ), 0) as total_revenue
        FROM tblbooking b
        JOIN tblvehicles v ON b.IdVehiculo = v.id
    ");
    $stats['total_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'];

    // Reservas por estado
    $stmt = $dbh->query("
        SELECT 
            SUM(CASE WHEN Estado = 1 THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN Estado = 2 THEN 1 ELSE 0 END) as confirmed,
            SUM(CASE WHEN Estado = 3 THEN 1 ELSE 0 END) as cancelled
        FROM tblbooking
    ");
    $bookingStats = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['pending_bookings'] = $bookingStats['pending'];
    $stats['confirmed_bookings'] = $bookingStats['confirmed'];
    $stats['cancelled_bookings'] = $bookingStats['cancelled'];

    // Vehículos por estado (asumiendo que tienes un campo de estado en tblvehicles)
    $stmt = $dbh->query("
    SELECT 
        SUM(CASE WHEN Estado = 1 THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN Estado = 2 THEN 1 ELSE 0 END) as confirmed,
        SUM(CASE WHEN Estado = 3 THEN 1 ELSE 0 END) as cancelled
    FROM tblbooking
    ");
    $vehicleStats = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['available_vehicles'] = $vehicleStats['available'];
    $stats['maintenance_vehicles'] = $vehicleStats['maintenance'];
    $stats['rented_vehicles'] = $vehicleStats['rented'];

    return $stats;
}

// Función para obtener datos para gráficos
function getChartData($dbh)
{
    $chartData = array();

    // Ingresos mensuales
    $stmt = $dbh->query("
        SELECT 
            DATE_FORMAT(FechaDesde, '%Y-%m') as month, 
            COALESCE(SUM(
                DATEDIFF(FechaHasta, FechaDesde) * v.PrecioPorDia
            ), 0) as monthly_revenue
        FROM tblbooking b
        JOIN tblvehicles v ON b.IdVehiculo = v.id
        GROUP BY month
        ORDER BY month DESC
        LIMIT 6
    ");
    $chartData['monthly_revenue'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Top vehículos más rentados
    $stmt = $dbh->query("
        SELECT 
            v.TituloVehiculo as Model, 
            COUNT(b.id) as rental_count
        FROM tblbooking b
        JOIN tblvehicles v ON b.IdVehiculo = v.id
        GROUP BY b.IdVehiculo, v.TituloVehiculo
        ORDER BY rental_count DESC
        LIMIT 5
    ");
    $chartData['top_vehicles'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $chartData;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>ECUA CARS - Panel de Administración</title>

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Rubik&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">

    <!-- Estilos Personalizados de Bootstrap -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilo de Plantilla -->
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="css/sidebar.css">
</head>

<body>
    <div class="container-fluid">
        <?php include 'includes/sidebar.php'; ?>

        <main role="main" class="content-wrapper col-md-9 ml-sm-auto col-lg-10 px-4">
            <div
                class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard Principal</h1>
            </div>

            <?php
            $stats = getExpandedStats($dbh);
            $chartData = getChartData($dbh);
            ?>

            <!-- Fila de Estadísticas Principales -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Total Usuarios</h5>
                            <p class="card-text display-4"><?php echo $stats['total_users']; ?></p>
                            <small class="text-white-50">Usuarios registrados</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Total Vehículos</h5>
                            <p class="card-text display-4"><?php echo $stats['total_vehicles']; ?></p>
                            <small class="text-white-50">
                                Disponibles: <?php echo $stats['available_vehicles']; ?>
                                | En uso: <?php echo $stats['rented_vehicles']; ?>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Total Reservas</h5>
                            <p class="card-text display-4"><?php echo $stats['total_bookings']; ?></p>
                            <small class="text-white-50">
                                Confirmadas: <?php echo $stats['confirmed_bookings']; ?>
                                | Pendientes: <?php echo $stats['pending_bookings']; ?>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Ingresos Totales</h5>
                            <p class="card-text display-4">$<?php echo number_format($stats['total_revenue'], 2); ?></p>
                            <small class="text-white-50">Ingresos acumulados</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Gráficos -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            Ingresos Mensuales
                        </div>
                        <div class="card-body">
                            <canvas id="revenueChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            Top 5 Vehículos más Rentados
                        </div>
                        <div class="card-body">
                            <canvas id="vehiclesChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Accesos Rápidos -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">Accesos Rápidos</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <a href="reservas.php" class="btn btn-outline-primary btn-block mb-2">
                                        <i class="fas fa-calendar-alt"></i> Gestionar Reservas
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="vehiculos.php" class="btn btn-outline-success btn-block mb-2">
                                        <i class="fas fa-car"></i> Catálogo de Vehículos
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="clientes.php" class="btn btn-outline-info btn-block mb-2">
                                        <i class="fas fa-users"></i> Gestión de Clientes
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="reportes.php" class="btn btn-outline-warning btn-block mb-2">
                                        <i class="fas fa-chart-bar"></i> Informes y Reportes
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Preparar datos para gráficos
        const revenueData = {
            labels: [
                <?php
                foreach ($chartData['monthly_revenue'] as $data) {
                    echo "'" . $data['month'] . "', ";
                }
                ?>
            ],
            datasets: [{
                label: 'Ingresos Mensuales ($)',
                data: [
                    <?php
                    foreach ($chartData['monthly_revenue'] as $data) {
                        echo $data['monthly_revenue'] . ", ";
                    }
                    ?>
                ],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        };

        const vehiclesData = {
            labels: [
                <?php
                foreach ($chartData['top_vehicles'] as $data) {
                    echo "'" . $data['Model'] . "', ";
                }
                ?>
            ],
            datasets: [{
                label: 'Número de Alquileres',
                data: [
                    <?php
                    foreach ($chartData['top_vehicles'] as $data) {
                        echo $data['rental_count'] . ", ";
                    }
                    ?>
                ],
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        };

        // Inicializar gráficos
        document.addEventListener('DOMContentLoaded', function () {
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'bar',
                data: revenueData,
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            const vehiclesCtx = document.getElementById('vehiclesChart').getContext('2d');
            new Chart(vehiclesCtx, {
                type: 'horizontalBar',
                data: vehiclesData,
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>