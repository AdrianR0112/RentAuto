<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="utf-8">
    <title>ECUA CARS - Panel del Empleado</title>

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
        <?php
        include 'includes/sidebar.php';
        ?>
        <main role="main" class="content-wrapper col-md-9 ml-sm-auto col-lg-10 px-4">
            <h2 class="text-center text-primary mt-3">Gestión de Devoluciones</h2>

            <!-- Mensajes -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['success']); ?></div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <!-- Formulario de Devolución -->
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">Registrar Devolución</div>
                <div class="card-body">
                    <form method="POST" action="devoluciones_proceso.php">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="IdReserva" class="form-label">Número de Reserva</label>
                                <select class="form-select" id="IdReserva" name="IdReserva" required>
                                    <option value="" disabled selected>Seleccione una reserva</option>
                                    <?php
                                    require_once 'db.php';
                                    try {
                                        $stmt = $dbh->query("
                                        SELECT b.id, b.NumeroReserva, v.TituloVehiculo, b.FechaDesde, b.FechaHasta
                                        FROM tblbooking b
                                        JOIN tblvehicles v ON b.IdVehiculo = v.id
                                        WHERE b.Estado = 1
                                    ");
                                        $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                        foreach ($reservas as $reserva) {
                                            echo "<option value='{$reserva['id']}'>
                                                {$reserva['NumeroReserva']} - {$reserva['TituloVehiculo']} ({$reserva['FechaDesde']} - {$reserva['FechaHasta']})
                                            </option>";
                                        }
                                    } catch (PDOException $e) {
                                        echo "<option disabled>Error al cargar reservas: " . htmlspecialchars($e->getMessage()) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="NumeroMotor" class="form-label">Número de Motor</label>
                                <input type="text" class="form-control" id="NumeroMotor" name="NumeroMotor" required>
                            </div>
                        </div>
                        <!-- Número de Chasis y Retraso -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="NumeroChasis" class="form-label">Número de Chasis</label>
                                <input type="text" class="form-control" id="NumeroChasis" name="NumeroChasis" required>
                            </div>
                            <div class="col-md-6">
                                <label for="RetrasoHoras" class="form-label">Horas de Retraso</label>
                                <input type="number" class="form-control" id="RetrasoHoras" name="RetrasoHoras" min="0"
                                    value="0" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="CargoRetraso" class="form-label">Cargo por Retraso ($)</label>
                                <input type="number" class="form-control" id="CargoRetraso" name="CargoRetraso"
                                    step="0.01" value="0.00" required>
                            </div>
                            <div class="col-md-6">
                                <label for="CargoDanios" class="form-label">Cargo por Daños ($)</label>
                                <input type="number" class="form-control" id="CargoDanios" name="CargoDanios"
                                    step="0.01" value="0.00" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="EstadoDanios" class="form-label">Estado de Daños</label>
                            <textarea class="form-control" id="EstadoDanios" name="EstadoDanios" rows="3"
                                required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="Observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="Observaciones" name="Observaciones" rows="3"></textarea>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Registrar Devolución</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de Registros Existentes -->
            <div class="table-responsive mt-5">
                <h5 class="text-primary">Registros de Alquileres Existentes</h5>
                <table class="table table-bordered table-striped">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>#</th>
                            <th>Número de Reserva</th>
                            <th>Correo Usuario</th>
                            <th>Vehículo</th>
                            <th>Conductor</th>
                            <th>Fecha Desde</th>
                            <th>Fecha Hasta</th>
                            <th>Costo Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $stmt = $dbh->query("
                    SELECT 
                        b.id,
                        b.NumeroReserva,
                        b.CorreoUsuario,
                        v.TituloVehiculo,
                        c.Nombre AS NombreConductor,
                        b.FechaDesde,
                        b.FechaHasta,
                        b.CostoTotal,
                        CASE 
                            WHEN b.Estado = 1 THEN 'Activo'
                            WHEN b.Estado = 3 THEN 'Devolución'
                            ELSE 'Devolucion'
                        END AS Estado
                    FROM tblbooking b
                    JOIN tblvehicles v ON b.IdVehiculo = v.id
                    JOIN tblconductores c ON b.IdConductor = c.id
                    ORDER BY b.FechaDesde DESC
                ");

                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>";
                                echo "<td>{$row['id']}</td>";
                                echo "<td>{$row['NumeroReserva']}</td>";
                                echo "<td>{$row['CorreoUsuario']}</td>";
                                echo "<td>{$row['TituloVehiculo']}</td>";
                                echo "<td>{$row['NombreConductor']}</td>";
                                echo "<td>{$row['FechaDesde']}</td>";
                                echo "<td>{$row['FechaHasta']}</td>";
                                echo "<td>\${$row['CostoTotal']}</td>";
                                echo "<td>{$row['Estado']}</td>";
                                echo "</tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='9'>Error al cargar los registros: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>