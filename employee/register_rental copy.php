<?php
session_start();
require_once '../includes/config.php';

// Verificar inicio de sesión
// if (!isset($_SESSION['empleado_id'])) {
//     header("Location: login.php");
//     exit();
// }

// Función para obtener vehículos disponibles
function getAvailableVehicles($dbh)
{
    $stmt = $dbh->prepare("SELECT v.id, b.NombreMarca, m.NombreModelo, v.AnoModelo, v.PrecioPorDia 
                           FROM tblvehicles v
                           JOIN tblbrands b ON v.MarcaVehiculo = b.id
                           JOIN tblmodels m ON v.IdModelo = m.id
                           WHERE v.Estado = 'Disponible'");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener conductores disponibles
function getAvailableDrivers($dbh)
{
    $stmt = $dbh->prepare("SELECT id, Nombre FROM tblconductores");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Procesar el formulario de registro de alquiler
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validación del lado del servidor
    $errors = [];

    // Validar correo electrónico
    $correo_usuario = filter_input(INPUT_POST, 'correo_usuario', FILTER_VALIDATE_EMAIL);
    if (!$correo_usuario) {
        $errors[] = "Correo electrónico inválido";
    }

    // Validar vehículo
    $id_vehiculo = filter_input(INPUT_POST, 'vehiculo', FILTER_VALIDATE_INT);
    if (!$id_vehiculo) {
        $errors[] = "Seleccione un vehículo válido";
    }

    // Validar conductor
    $id_conductor = filter_input(INPUT_POST, 'conductor', FILTER_VALIDATE_INT);
    if (!$id_conductor) {
        $errors[] = "Seleccione un conductor válido";
    }

    // Validar fechas
    $fecha_desde = $_POST['fecha_desde'];
    $fecha_hasta = $_POST['fecha_hasta'];

    if (empty($fecha_desde) || empty($fecha_hasta)) {
        $errors[] = "Las fechas de alquiler son obligatorias";
    } else {
        $fecha_desde_obj = new DateTime($fecha_desde);
        $fecha_hasta_obj = new DateTime($fecha_hasta);

        if ($fecha_desde_obj >= $fecha_hasta_obj) {
            $errors[] = "La fecha de inicio debe ser anterior a la fecha de fin";
        }
    }

    // Validar forma de pago
    $formas_pago_validas = ['Efectivo', 'Tarjeta', 'Transferencia'];
    $forma_pago = $_POST['forma_pago'];
    if (!in_array($forma_pago, $formas_pago_validas)) {
        $errors[] = "Seleccione una forma de pago válida";
    }

    // Calcular costo total
    if (empty($errors)) {
        try {
            // Obtener precio por día del vehículo
            $stmt = $dbh->prepare("SELECT PrecioPorDia FROM tblvehicles WHERE id = ?");
            $stmt->execute([$id_vehiculo]);
            $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);

            $dias = $fecha_desde_obj->diff($fecha_hasta_obj)->days + 1;
            $costo_total = $vehiculo['PrecioPorDia'] * $dias;

            // Verificar disponibilidad del vehículo
            $stmt = $dbh->prepare("SELECT COUNT(*) FROM tblbooking 
                                   WHERE IdVehiculo = ? AND 
                                   ((FechaDesde BETWEEN ? AND ?) OR 
                                    (FechaHasta BETWEEN ? AND ?))");
            $stmt->execute([$id_vehiculo, $fecha_desde, $fecha_hasta, $fecha_desde, $fecha_hasta]);
            $reservas_existentes = $stmt->fetchColumn();

            if ($reservas_existentes > 0) {
                $errors[] = "El vehículo no está disponible en las fechas seleccionadas";
            } else {
                // Insertar reserva
                $stmt = $dbh->prepare("INSERT INTO tblbooking 
                                       (CorreoUsuario, IdVehiculo, IdConductor, 
                                        FechaDesde, FechaHasta, FormaPago, 
                                        mensaje, CostoTotal) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $resultado = $stmt->execute([
                    $correo_usuario,
                    $id_vehiculo,
                    $id_conductor,
                    $fecha_desde,
                    $fecha_hasta,
                    $forma_pago,
                    $_POST['mensaje'] ?? '',
                    $costo_total
                ]);

                if ($resultado) {
                    // Actualizar estado del vehículo
                    $stmt = $dbh->prepare("UPDATE tblvehicles SET Estado = 'Reservado' WHERE id = ?");
                    $stmt->execute([$id_vehiculo]);

                    $_SESSION['mensaje_exito'] = "Reserva creada exitosamente. Costo total: $" . number_format($costo_total, 2);
                    header("Location: register_rental.php");
                    exit();
                }
            }
        } catch (PDOException $e) {
            $errors[] = "Error al procesar la reserva: " . $e->getMessage();
        }
    }
}

// Obtener vehículos y conductores disponibles
$vehiculos = getAvailableVehicles($dbh);
$conductores = getAvailableDrivers($dbh);
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
    <div class="container-fluid">
        <?php include 'includes/sidebar.php'; ?>
        <main role="main" class="content-wrapper col-md-9 ml-sm-auto col-lg-10 px-4">
            <div
                class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Registro de Alquiler</h1>
            </div>

            <?php if (isset($_SESSION['mensaje_exito'])): ?>
                <div class="alert alert-success">
                    <?= $_SESSION['mensaje_exito'] ?>
                    <?php unset($_SESSION['mensaje_exito']); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form id="rental-form" method="POST" action="" novalidate>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="correo_usuario">Correo Electrónico</label>
                        <input type="email" class="form-control" id="correo_usuario" name="correo_usuario" required
                            value="<?= isset($_POST['correo_usuario']) ? htmlspecialchars($_POST['correo_usuario']) : '' ?>">
                        <div class="invalid-feedback">Por favor ingrese un correo electrónico válido.</div>
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="vehiculo">Vehículo</label>
                        <select class="form-control" id="vehiculo" name="vehiculo" required>
                            <option value="">Seleccione un vehículo</option>
                            <?php foreach ($vehiculos as $vehiculo): ?>
                                <option value="<?= $vehiculo['id'] ?>" <?= (isset($_POST['vehiculo']) && $_POST['vehiculo'] == $vehiculo['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($vehiculo['NombreMarca'] . ' ' . $vehiculo['NombreModelo'] . ' (' . $vehiculo['AnoModelo'] . ') - $' . $vehiculo['PrecioPorDia'] . '/día') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione un vehículo.</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="conductor">Conductor</label>
                        <select class="form-control" id="conductor" name="conductor" required>
                            <option value="">Seleccione un conductor</option>
                            <?php foreach ($conductores as $conductor): ?>
                                <option value="<?= $conductor['id'] ?>" <?= (isset($_POST['conductor']) && $_POST['conductor'] == $conductor['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($conductor['Nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione un conductor.</div>
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="forma_pago">Forma de Pago</label>
                        <select class="form-control" id="forma_pago" name="forma_pago" required>
                            <option value="">Seleccione forma de pago</option>
                            <option value="Efectivo" <?= (isset($_POST['forma_pago']) && $_POST['forma_pago'] == 'Efectivo') ? 'selected' : '' ?>>Efectivo</option>
                            <option value="Tarjeta" <?= (isset($_POST['forma_pago']) && $_POST['forma_pago'] == 'Tarjeta') ? 'selected' : '' ?>>Tarjeta</option>
                            <option value="Transferencia" <?= (isset($_POST['forma_pago']) && $_POST['forma_pago'] == 'Transferencia') ? 'selected' : '' ?>>Transferencia</option>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione una forma de pago.</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="fecha_desde">Fecha de Inicio</label>
                        <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" required
                            value="<?= isset($_POST['fecha_desde']) ? htmlspecialchars($_POST['fecha_desde']) : '' ?>">
                        <div class="invalid-feedback">Por favor seleccione la fecha de inicio.</div>
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="fecha_hasta">Fecha de Fin</label>
                        <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" required
                            value="<?= isset($_POST['fecha_hasta']) ? htmlspecialchars($_POST['fecha_hasta']) : '' ?>">
                        <div class="invalid-feedback">Por favor seleccione la fecha de fin.</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="mensaje">Mensaje Adicional (Opcional)</label>
                    <textarea class="form-control" id="mensaje" name="mensaje"
                        rows="3"><?= isset($_POST['mensaje']) ? htmlspecialchars($_POST['mensaje']) : '' ?></textarea>
                </div>

                <div id="costo-total" class="alert alert-info mt-3" style="display:none;">
                    Costo Total Estimado: $<span id="monto-total"></span>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Registrar Alquiler</button>
            </form>
        </main>
    </div>
    </div>

    <!-- Scripts de Bootstrap y validación -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Función para calcular costo total
            function calcularCostoTotal() {
                const vehiculo = $('#vehiculo');
                const fechaDesde = $('#fecha_desde');
                const fechaHasta = $('#fecha_hasta');
                const costoTotalDiv = $('#costo-total');
                const montoTotalSpan = $('#monto-total');

                if (vehiculo.val() && fechaDesde.val() && fechaHasta.val()) {
                    const precioStr = vehiculo.find('option:selected').text().match(/\$(\d+)/)[1];
                    const precio = parseFloat(precioStr);

                    const desde = new Date(fechaDesde.val());
                    const hasta = new Date(fechaHasta.val());
                    const dias = Math.round((hasta - desde) / (1000 * 60 * 60 * 24)) + 1;

                    const costoTotal = precio * dias;
                    montoTotalSpan.text(costoTotal.toFixed(2));
                    costoTotalDiv.show();
                } else {
                    costoTotalDiv.hide();
                }
            }

            // Calcular costo total cuando cambian los campos
            $('#vehiculo, #fecha_desde, #fecha_hasta').on('change', calcularCostoTotal);

            // Validación de formulario con Bootstrap
            $('#rental-form').on('submit', function (event) {
                const form = this;

                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                form.classList.add('was-validated');
            });

            // Restricciones de fechas
            const today = new Date().toISOString().split('T')[0];
            $('#fecha_desde, #fecha_hasta').attr('min', today);

            $('#fecha_desde').on('change', function () {
                $('#fecha_hasta').attr('min', this.value);
            });

            // Validación adicional de correo electrónico
            $('#correo_usuario').on('input', function () {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                const email = $(this).val();

                if (!emailRegex.test(email)) {
                    this.setCustomValidity('Correo electrónico inválido');
                } else {
                    this.setCustomValidity('');
                }
            });
        });
    </script>
</body>

</html>