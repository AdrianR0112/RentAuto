<?php
require_once 'db.php'; // Conexión a la base de datos

// Inicializar variables
$vehiculos = [];
$disponibilidad = [];
$vehiculoSeleccionado = null; // Inicialización

// Obtener todos los vehículos de la base de datos
$stmt = $dbh->query("SELECT id, TituloVehiculo FROM tblvehicles");
$vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verificar disponibilidad si se envió un formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['IdVehiculo'])) {
    $IdVehiculo = $_POST['IdVehiculo'];

    // Obtener detalles del vehículo seleccionado
    $stmt = $dbh->prepare("SELECT TituloVehiculo FROM tblvehicles WHERE id = ?");
    $stmt->execute([$IdVehiculo]);
    $vehiculoSeleccionado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Obtener registros de alquiler asociados al vehículo seleccionado
    $stmt = $dbh->prepare("
        SELECT NumeroReserva, FechaDesde, FechaHasta, Estado 
        FROM tblbooking 
        WHERE IdVehiculo = ?
        ORDER BY FechaDesde ASC
    ");
    $stmt->execute([$IdVehiculo]);
    $disponibilidad = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="styleEmpleado.css" rel="stylesheet">
    <title>Consulta de Disponibilidad</title>
</head>
<body>
<div class="container-fluid">
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <img src="assets/LogoRentAuto.png" alt="Car Icon" class="img-fluid" />
        </div>
        <ul class="list-unstyled components">
        <li>
                <a href="alquileres.php">
                    <i class="fas fa-car-side"></i> Gestión de Alquileres
                </a>
            </li>
            <li>
                <a href="devoluciones.php">
                    <i class="fas fa-undo-alt"></i> Gestión de Devoluciones
                </a>
            </li>
            <li>
                <a href="disponibilidad.php">
                    <i class="fas fa-search"></i> Consulta de Disponibilidad
                </a>
            </li>
            <li>
                <a href="facturacion.php">
                    <i class="fas fa-file-invoice-dollar"></i> Facturación y Pagos
                </a>
            </li>
            <li>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </li>
        </ul>
    </nav>

    <!-- Contenido principal -->
    <div class="content-container">
        <h2 class="text-center text-primary mt-3">Consulta de Disponibilidad</h2>
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">Seleccionar Vehículo</div>
            <div class="card-body">
                <!-- Formulario de Selección -->
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="IdVehiculo" class="form-label">Seleccione un Vehículo</label>
                        <select class="form-select" id="IdVehiculo" name="IdVehiculo" required>
                            <option value="" disabled selected>Seleccione un vehículo</option>
                            <?php foreach ($vehiculos as $vehiculo): ?>
                                <option value="<?= htmlspecialchars($vehiculo['id']); ?>">
                                    <?= htmlspecialchars($vehiculo['TituloVehiculo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Consultar Disponibilidad</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Resultado de la Consulta -->
        <?php if (!empty($vehiculoSeleccionado)): ?>
            <div class="table-responsive mt-4">
                <h5 class="text-primary">Disponibilidad del Vehículo: <?= htmlspecialchars($vehiculoSeleccionado['TituloVehiculo']); ?></h5>
                <?php if (!empty($disponibilidad)): ?>
                    <table class="table table-bordered table-striped">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Número de Reserva</th>
                                <th>Fecha Desde</th>
                                <th>Fecha Hasta</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($disponibilidad as $registro): ?>
                                <tr>
                                    <td><?= htmlspecialchars($registro['NumeroReserva']); ?></td>
                                    <td><?= htmlspecialchars($registro['FechaDesde']); ?></td>
                                    <td><?= htmlspecialchars($registro['FechaHasta']); ?></td>
                                    <td>
                                        <?= ($registro['Estado'] == 1) ? 'Activo' : 'Devolución'; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        El vehículo seleccionado no tiene registros de alquiler y está disponible.
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>