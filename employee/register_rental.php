<?php
session_start();
require_once '../includes/config.php';

// Función para obtener todos los usuarios
function getAllUsers($dbh)
{
    $stmt = $dbh->prepare("SELECT id, NombreCompleto, CorreoElectronico, NumeroContacto FROM tblusers");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Resto de las funciones previas (getAvailableVehicles, getAvailableDrivers)
function getAvailableVehicles($dbh)
{
    $stmt = $dbh->prepare("SELECT v.id, b.NombreMarca, m.NombreModelo, v.AnoModelo, 
                           v.PrecioPorDia, v.Categoria, v.CapacidadAsientos, 
                           v.Transmision, v.TipoCombustible 
                           FROM tblvehicles v
                           JOIN tblbrands b ON v.MarcaVehiculo = b.id
                           JOIN tblmodels m ON v.IdModelo = m.id
                           WHERE v.Estado = 'Disponible'");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAvailableDrivers($dbh)
{
    $stmt = $dbh->prepare("SELECT id, Nombre, Licencia, Telefono FROM tblconductores");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Procesar registro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];

    // Validación de usuario
    $user_type = $_POST['user_type'] ?? '';

    if ($user_type === 'nuevo') {
        // Validación para nuevo usuario
        $correo = filter_input(INPUT_POST, 'correo_nuevo', FILTER_VALIDATE_EMAIL);
        $nombre = trim($_POST['nombre_nuevo'] ?? '');
        $telefono = trim($_POST['telefono_nuevo'] ?? '');

        if (!$correo) $errors[] = "Correo electrónico inválido";
        if (empty($nombre)) $errors[] = "Nombre completo es obligatorio";
        if (empty($telefono)) $errors[] = "Número de contacto es obligatorio";
    } elseif ($user_type === 'existente') {
        // Validación para usuario existente
        $usuario_id = $_POST['usuario_existente'] ?? null;
        if (!$usuario_id) $errors[] = "Debe seleccionar un usuario";
    } else {
        $errors[] = "Debe seleccionar un tipo de usuario";
    }

    // Resto de validaciones (vehiculo, fechas, etc.)
    $id_vehiculo = filter_input(INPUT_POST, 'vehiculo', FILTER_VALIDATE_INT);
    $fecha_desde = $_POST['fecha_desde'] ?? '';
    $fecha_hasta = $_POST['fecha_hasta'] ?? '';
    $hora_recogida = $_POST['hora_recogida'] ?? '';
    $hora_devolucion = $_POST['hora_devolucion'] ?? '';
    $mismo_conductor = $_POST['mismo_conductor'] ?? 'no';
    $conductor_id = $_POST['conductor'] ?? null;
    $forma_pago = $_POST['forma_pago'] ?? '';
    $formas_pago_validas = ['Efectivo', 'Tarjeta', 'Transferencia'];

    // Validaciones adicionales
    if (!$id_vehiculo) $errors[] = "Debe seleccionar un vehículo";
    if (empty($fecha_desde) || empty($fecha_hasta)) $errors[] = "Las fechas son obligatorias";
    if (empty($hora_recogida) || empty($hora_devolucion)) $errors[] = "Las horas son obligatorias";
    if (!in_array($forma_pago, $formas_pago_validas)) $errors[] = "Seleccione una forma de pago válida";

    // Si no hay errores, procesar registro
    if (empty($errors)) {
        // Aquí iría la lógica de registro en base de datos
        // Dependiendo de si es usuario nuevo o existente
    }
}

// Obtener datos necesarios
$vehiculos = getAvailableVehicles($dbh);
$conductores = getAvailableDrivers($dbh);
$usuarios = getAllUsers($dbh);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>ECUA CARS - Registro de Alquiler</title>
    <link href="img/favicon.ico" rel="icon">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Rubik&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="css/sidebar.css">
    <style>
        .seccion {
            margin-bottom: 2rem;
            padding: 1rem;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
        }
        .user-form {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <?php include 'includes/sidebar.php'; ?>
        
        <main role="main" class="content-wrapper col-md-9 ml-sm-auto col-lg-10 px-4">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form id="rental-form" method="POST" action="" novalidate>
                <!-- Sección 1: Información del Cliente -->
                <div class="seccion" id="seccion-cliente">
                    <h2>Información del Cliente</h2>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="user_type" id="usuario_nuevo" value="nuevo" required>
                            <label class="form-check-label" for="usuario_nuevo">
                                Crear Nuevo Usuario
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="user_type" id="usuario_existente" value="existente">
                            <label class="form-check-label" for="usuario_existente">
                                Seleccionar Usuario Existente
                            </label>
                        </div>
                    </div>

                    <!-- Formulario para Nuevo Usuario -->
                    <div id="nuevo-usuario-form" class="user-form">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Correo Electrónico</label>
                                <input type="email" class="form-control" name="correo_nuevo">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Nombre Completo</label>
                                <input type="text" class="form-control" name="nombre_nuevo">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Número de Contacto</label>
                                <input type="tel" class="form-control" name="telefono_nuevo">
                            </div>
                        </div>
                    </div>

                    <!-- Lista de Usuarios Existentes -->
                    <div id="usuarios-existentes" class="user-form">
                        <div class="form-group">
                            <label>Seleccionar Usuario</label>
                            <select name="usuario_existente" class="form-control">
                                <option value="">Seleccione un usuario</option>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option value="<?= $usuario['id'] ?>">
                                        <?= htmlspecialchars($usuario['NombreCompleto']) ?> 
                                        (<?= htmlspecialchars($usuario['CorreoElectronico']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Resto de secciones (igual que en el ejemplo anterior) -->
                <!-- Sección 2: Selección de Vehículo -->
                <div class="seccion" id="seccion-vehiculo">
                    <h2>Selección de Vehículo</h2>
                    <div class="row">
                        <?php foreach ($vehiculos as $vehiculo): ?>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <?= htmlspecialchars($vehiculo['NombreMarca'] . ' ' . $vehiculo['NombreModelo']) ?>
                                        </h5>
                                        <p>
                                            Año: <?= $vehiculo['AnoModelo'] ?><br>
                                            Categoría: <?= $vehiculo['Categoria'] ?><br>
                                            Asientos: <?= $vehiculo['CapacidadAsientos'] ?><br>
                                            Transmisión: <?= $vehiculo['Transmision'] ?><br>
                                            Combustible: <?= $vehiculo['TipoCombustible'] ?><br>
                                            Precio: $<?= $vehiculo['PrecioPorDia'] ?>/día
                                        </p>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="vehiculo"
                                                id="vehiculo<?= $vehiculo['id'] ?>" value="<?= $vehiculo['id'] ?>" required>
                                            <label class="form-check-label" for="vehiculo<?= $vehiculo['id'] ?>">
                                                Seleccionar
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Resto de secciones como en el ejemplo anterior -->
                <!-- Sección 3: Fechas y Horas de Alquiler -->
                <div class="seccion" id="seccion-fechas">
                    <h2>Fechas y Horas de Alquiler</h2>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Fecha de Recogida</label>
                            <input type="date" class="form-control" name="fecha_desde" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Fecha de Devolución</label>
                            <input type="date" class="form-control" name="fecha_hasta" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Hora de Recogida</label>
                            <input type="time" class="form-control" name="hora_recogida" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Hora de Devolución</label>
                            <input type="time" class="form-control" name="hora_devolucion" required>
                        </div>
                    </div>
                </div>

                <!-- Sección 4: Selección de Conductor -->
                <div class="seccion" id="seccion-conductor">
                    <h2>Información del Conductor</h2>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="mismo_conductor" id="mismo_conductor_si"
                                value="si" required>
                            <label class="form-check-label" for="mismo_conductor_si">
                                El cliente será el conductor
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="mismo_conductor" id="mismo_conductor_no"
                                value="no">
                            <label class="form-check-label" for="mismo_conductor_no">
                                Otro conductor
                            </label>
                        </div>
                    </div>

                    <div id="conductores-lista" style="display:none;">
                        <?php foreach ($conductores as $conductor): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="conductor"
                                    id="conductor<?= $conductor['id'] ?>" value="<?= $conductor['id'] ?>">
                                <label class="form-check-label" for="conductor<?= $conductor['id'] ?>">
                                    <?= htmlspecialchars($conductor['Nombre']) ?>
                                    - Licencia: <?= $conductor['Licencia'] ?>
                                    - Tel: <?= $conductor['Telefono'] ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Sección 5: Forma de Pago -->
                <div class="seccion" id="seccion-pago">
                    <h2>Forma de Pago</h2>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="forma_pago" id="pago_efectivo"
                                value="Efectivo" required>
                            <label class="form-check-label" for="pago_efectivo">
                                Efectivo
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="forma_pago" id="pago_transferencia"
                                value="Transferencia">
                            <label class="form-check-label" for="pago_transferencia">
                                Transferencia Bancaria
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Botón de Envío -->
                <div class="mt-3">
                    <button type="submit" class="btn btn-success btn-lg btn-block">Confirmar Alquiler</button>
                </div>
            </form>
        </main>
    </div>
<!-- Previous code remains the same until the script section -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Manejo de selección de tipo de usuario
            $('input[name="user_type"]').on('change', function() {
                // Ocultar todos los formularios de usuario
                $('.user-form').hide();
                
                // Limpiar campos de los formularios
                $('#nuevo-usuario-form input').val('');
                $('#usuarios-existentes select').prop('selectedIndex', 0);
                
                // Mostrar el formulario correspondiente
                if ($(this).val() === 'nuevo') {
                    $('#nuevo-usuario-form').show();
                    $('#nuevo-usuario-form input').prop('required', true);
                    $('#usuarios-existentes select').prop('required', false);
                } else {
                    $('#usuarios-existentes').show();
                    $('#usuarios-existentes select').prop('required', true);
                    $('#nuevo-usuario-form input').prop('required', false);
                }
            });

            // Mostrar/ocultar lista de conductores
            $('input[name="mismo_conductor"]').on('change', function () {
                if ($(this).val() === 'no') {
                    $('#conductores-lista').show();
                } else {
                    $('#conductores-lista').hide();
                    $('input[name="conductor"]').prop('checked', false);
                }
            });

            // Manejo de fechas
            const today = new Date().toISOString().split('T')[0];
            $('input[name="fecha_desde"], input[name="fecha_hasta"]').attr('min', today);

            $('input[name="fecha_desde"]').on('change', function () {
                $('input[name="fecha_hasta"]').attr('min', this.value);
            });

            // Validación del formulario
            $('#rental-form').on('submit', function (event) {
                let isValid = true;
                const requiredFields = $('[required]:visible');

                requiredFields.each(function () {
                    if (!this.checkValidity()) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                if (!isValid) {
                    event.preventDefault();
                    alert('Por favor, complete todos los campos requeridos correctamente.');
                }
            });

            // Si hay errores al cargar la página, mostrar el formulario correcto
            <?php if (!empty($errors)): ?>
                $('input[name="user_type"][value="<?= $user_type ?? '' ?>"]').prop('checked', true).trigger('change');
            <?php endif; ?>
        });
    </script>
</body>
</html>