<?php
require_once 'db.php'; // Conexión a la base de datos

// Obtener todos los vehículos y alquileres pendientes
$stmt = $dbh->query("SELECT b.id, v.TituloVehiculo, b.NumeroReserva, b.CostoTotal
                      FROM tblbooking b
                      JOIN tblvehicles v ON b.IdVehiculo = v.id
                      WHERE b.Estado = 1");
$alquileres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Guardar pago en la base de datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $IdReserva = $_POST['IdReserva'];
    $MetodoPago = $_POST['MetodoPago'];
    $Monto = $_POST['Monto'];

    try {
        $dbh->beginTransaction();

        // Validar el monto con la reserva
        $stmt = $dbh->prepare("SELECT CostoTotal FROM tblbooking WHERE id = ?");
        $stmt->execute([$IdReserva]);
        $reserva = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reserva) {
            throw new Exception("La reserva no existe.");
        }

        if ($Monto < $reserva['CostoTotal']) {
            throw new Exception("El monto ingresado no cubre el costo total del alquiler.");
        }

        // Insertar pago
        $stmt = $dbh->prepare("INSERT INTO tblpagos (IdReserva, MetodoPago, Monto, EstadoPago, FechaPago) 
                               VALUES (?, ?, ?, 'Completado', NOW())");
        $stmt->execute([$IdReserva, $MetodoPago, $Monto]);

        // Actualizar el estado de la reserva
        $stmt = $dbh->prepare("UPDATE tblbooking SET Estado = 3 WHERE id = ?");
        $stmt->execute([$IdReserva]);

        $dbh->commit();
        header('Location: facturacion.php?status=success');
        exit();
    } catch (Exception $e) {
        $dbh->rollBack();
        header('Location: facturacion.php?status=error&message=' . urlencode($e->getMessage()));
        exit();
    }
}

// Obtener registros de pagos y facturas
$stmt = $dbh->query("
    SELECT p.IdPago, b.NumeroReserva, v.TituloVehiculo, p.MetodoPago, p.Monto, p.FechaPago, p.EstadoPago
    FROM tblpagos p
    JOIN tblbooking b ON p.IdReserva = b.id
    JOIN tblvehicles v ON b.IdVehiculo = v.id
    ORDER BY p.FechaPago DESC
");
$pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="styleEmpleado.css" rel="stylesheet">
    <title>Facturación y Pagos</title>
</head>
<body>
<div class="container-fluid">
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <img src="assets/LogoRentAuto.png" alt="Car Icon" class="img-fluid" />
        </div>
        <ul class="list-unstyled components">
            <li><a href="alquileres.php"><i class="fas fa-car-side"></i> Gestión de Alquileres</a></li>
            <li><a href="devoluciones.php"><i class="fas fa-undo-alt"></i> Gestión de Devoluciones</a></li>
            <li><a href="disponibilidad.php"><i class="fas fa-search"></i> Consulta de Disponibilidad</a></li>
            <li class="active"><a href="facturacion.php"><i class="fas fa-file-invoice-dollar"></i> Facturación y Pagos</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
        </ul>
    </nav>

    <!-- Contenido Principal -->
    <div class="content-container">
        <h2 class="text-center text-primary mt-3">Facturación y Pagos</h2>

        <!-- Notificaciones -->
        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
            <div class="alert alert-success">Pago registrado con éxito.</div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
            <div class="alert alert-danger">
                Error: <?= htmlspecialchars($_GET['message']); ?>
            </div>
        <?php endif; ?>

        <!-- Formulario de Pago -->
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">Registrar Pago</div>
            <div class="card-body">
                <form method="POST" action="facturacion.php">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="IdReserva" class="form-label">Seleccione un Alquiler</label>
                            <select class="form-select" id="IdReserva" name="IdReserva" required>
                                <option value="" disabled selected>Seleccione un alquiler</option>
                                <?php foreach ($alquileres as $alquiler): ?>
                                    <option value="<?= $alquiler['id']; ?>">
                                        Reserva: <?= $alquiler['NumeroReserva']; ?> - <?= $alquiler['TituloVehiculo']; ?> ($<?= $alquiler['CostoTotal']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="MetodoPago" class="form-label">Método de Pago</label>
                            <select class="form-select" id="MetodoPago" name="MetodoPago" required>
                                <option value="" disabled selected>Seleccione un método de pago</option>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Tarjeta">Tarjeta</option>
                                <option value="Transferencia">Transferencia</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="Monto" class="form-label">Monto del Pago ($)</label>
                            <input type="number" class="form-control" id="Monto" name="Monto" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-success">Registrar Pago</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de Pagos -->
        <div class="table-responsive mt-5">
            <h5 class="text-primary">Registros de Pagos</h5>
            <table class="table table-bordered table-striped">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>#</th>
                        <th>Número de Reserva</th>
                        <th>Vehículo</th>
                        <th>Método de Pago</th>
                        <th>Monto</th>
                        <th>Fecha de Pago</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pagos as $pago): ?>
                        <tr>
                            <td><?= $pago['IdPago']; ?></td>
                            <td><?= $pago['NumeroReserva']; ?></td>
                            <td><?= $pago['TituloVehiculo']; ?></td>
                            <td><?= $pago['MetodoPago']; ?></td>
                            <td>$<?= number_format($pago['Monto'], 2); ?></td>
                            <td><?= $pago['FechaPago']; ?></td>
                            <td>
    <?php if ($pago['EstadoPago'] === 'Completado'): ?>
        <span class="badge" style="background-color: #28a745; color: black;">Completado</span>
    <?php elseif ($pago['EstadoPago'] === 'Pendiente'): ?>
        <span class="badge" style="background-color: #ffc107; color: black;">Pendiente</span>
    <?php elseif ($pago['EstadoPago'] === 'Cancelado'): ?>
        <span class="badge" style="background-color: #dc3545; color: black;">Cancelado</span>
    <?php else: ?>
        <span class="badge" style="background-color: #6c757d; color: black;">Sin definir</span>
    <?php endif; ?>
</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>