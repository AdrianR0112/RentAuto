<?php
require_once '../includes/config.php';
?>

<!DOCTYPE html>
<html lang="es">

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
    <!-- Contenedor principal -->
    <div class="container-fluid">
        <?php
        include 'includes/sidebar.php';
        ?>
        <main role="main" class="content-wrapper col-md-9 ml-sm-auto col-lg-10 px-4">
            <h2 class="text-center text-primary mt-3">Gestión de Alquileres</h2>
            <div class="form-container mt-4">
                <!-- Contenedor para Notificaciones -->
                <?php
                if (isset($_GET['status'])) {
                    $status = $_GET['status'];
                    if ($status === 'success') {
                        echo '<div id="alert" class="alert alert-success alert-dismissible fade show" role="alert">
                            Alquiler registrado con éxito.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
                    } elseif ($status === 'error') {
                        echo '<div id="alert" class="alert alert-danger alert-dismissible fade show" role="alert">
                            Ocurrió un error al registrar el alquiler. Por favor, inténtelo de nuevo.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>';
                    }
                }
                ?>

                <div class="card">
                    <div class="card-header bg-primary text-white">Registrar Nuevo Alquiler</div>
                    <div class="card-body">
                        <form action="procesar_alquiler.php" method="POST">
                            <!-- Datos del Alquiler -->
                            <h5 class="mb-3 text-primary">Datos del Alquiler</h5>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="NombreCompleto" class="form-label">Nombre Completo</label>
                                    <input type="text" class="form-control" id="NombreCompleto" name="NombreCompleto"
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <label for="Correo" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="Correo" name="Correo" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="NumeroContacto" class="form-label">Número de Contacto</label>
                                    <input type="text" class="form-control" id="NumeroContacto" name="NumeroContacto"
                                        required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="FechaNacimiento" class="form-label">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control" id="FechaNacimiento" name="FechaNacimiento"
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <label for="Direccion" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="Direccion" name="Direccion" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="Ciudad" class="form-label">Ciudad</label>
                                    <input type="text" class="form-control" id="Ciudad" name="Ciudad" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="Pais" class="form-label">País</label>
                                    <input type="text" class="form-control" id="Pais" name="Pais" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="mensaje" class="form-label">Mensaje</label>
                                    <textarea class="form-control" id="mensaje" name="mensaje" rows="3"
                                        placeholder="Ingrese algún comentario o consulta..."></textarea>
                                </div>
                            </div>

                            <!-- Seleccionar Vehículo -->
                            <h5 class="mb-3 text-primary">Seleccionar Vehículo</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="IdVehiculo" class="form-label">Vehículo</label>
                                    <select class="form-select" id="IdVehiculo" name="IdVehiculo" required>
                                        <option value="" disabled selected>Seleccione un vehículo</option>
                                        <?php
                                        include 'db.php';
                                        $stmt = $dbh->query("SELECT id, TituloVehiculo FROM tblvehicles WHERE Estado = 'Disponible'");
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            echo "<option value='{$row['id']}'>{$row['TituloVehiculo']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Fecha de Alquiler -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="FechaDesde" class="form-label">Fecha Desde</label>
                                    <input type="date" class="form-control" id="FechaDesde" name="FechaDesde" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="FechaHasta" class="form-label">Fecha Hasta</label>
                                    <input type="date" class="form-control" id="FechaHasta" name="FechaHasta" required>
                                </div>
                            </div>

                            <!-- Datos del Conductor -->
                            <h5 class="mb-3 text-primary">Datos del Conductor</h5>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="NombreConductor" class="form-label">Nombre del Conductor</label>
                                    <input type="text" class="form-control" id="NombreConductor" name="NombreConductor"
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <label for="Licencia" class="form-label">Tipo de Licencia</label>
                                    <select class="form-select" id="Licencia" name="Licencia" required>
                                        <option value="" disabled selected>Seleccione un tipo de licencia</option>
                                        <option value="E">Licencia Tipo E</option>
                                        <option value="D">Licencia Tipo D</option>
                                        <option value="C">Licencia Tipo C</option>
                                        <option value="B">Licencia Tipo B</option>
                                        <option value="F">Licencia Tipo F</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="Telefono" class="form-label">Teléfono del Conductor</label>
                                    <input type="text" class="form-control" id="Telefono" name="Telefono" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="CorreoConductor" class="form-label">Correo del Conductor</label>
                                    <input type="email" class="form-control" id="CorreoConductor" name="CorreoConductor"
                                        required>
                                </div>
                            </div>

                            <!-- Forma de Pago -->
                            <h5 class="mb-3 text-primary">Forma de Pago</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="FormaPago" class="form-label">Método de Pago</label>
                                    <select class="form-select" id="FormaPago" name="FormaPago" required>
                                        <option value="" disabled selected>Seleccione la forma de pago</option>
                                        <option value="Efectivo">Efectivo</option>
                                        <option value="Transferencia">Transferencia</option>
                                        <option value="Tarjeta">Tarjeta de Crédito</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Botón de Envío -->
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Registrar Alquiler</button>
                            </div>
                        </form>
                    </div>
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
                            <th>Contrato</th>
                            <th>Factura</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Consultar registros de alquiler
                        $stmt = $dbh->query("
    SELECT 
        b.id,
        b.NumeroReserva,
        b.CorreoUsuario,
        CONCAT(br.NombreMarca, ' ', m.NombreModelo, ' (', v.AnoModelo, ')') AS TituloVehiculo,
        c.Nombre AS NombreConductor,
        b.FechaDesde,
        b.FechaHasta,
        b.CostoTotal
    FROM tblbooking b
    JOIN tblvehicles v ON b.IdVehiculo = v.id
    JOIN tblmodels m ON v.IdModelo = m.id
    JOIN tblbrands br ON v.MarcaVehiculo = br.id
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
                            // Botón para ver contrato
                            echo "<td><a href='generar_contrato.php?id={$row['id']}' class='btn btn-sm btn-primary' target='_blank'>Ver Contrato</a></td>";
                            // Botón para ver factura
                            echo "<td><a href='generar_factura.php?id={$row['id']}' class='btn btn-sm btn-success' target='_blank'>Ver Factura</a></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <?php
    if (isset($_GET['status'])) {
        $status = $_GET['status'];
        $message = isset($_GET['message']) ? urldecode($_GET['message']) : '';
        if ($status === 'success') {
            echo '<div id="alert" class="alert alert-success alert-dismissible fade show" role="alert">
                Alquiler registrado con éxito.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
        } elseif ($status === 'error') {
            echo '<div id="alert" class="alert alert-danger alert-dismissible fade show" role="alert">
                ' . htmlspecialchars($message) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
        }
    }
    ?>
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script>
        // Ocultar la alerta después de 5 segundos
        const alert = document.getElementById('alert');
        if (alert) {
            setTimeout(() => {
                alert.classList.remove('show');
                alert.classList.add('hide');
            }, 5000);
        }
    </script>

    <script>
        // Configurar fechas mínimas
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('FechaDesde').setAttribute('min', today);
        document.getElementById('FechaHasta').setAttribute('min', today);

        // Sincronizar FechaHasta con FechaDesde
        document.getElementById('FechaDesde').addEventListener('change', function () {
            const fechaDesde = this.value;
            document.getElementById('FechaHasta').setAttribute('min', fechaDesde);
        });
    </script>
</body>

</html>