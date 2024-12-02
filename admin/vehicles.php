<?php
session_start();
require_once '../includes/config.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Si no está autenticado, redirigir al login
    header("Location: login.php");
    exit();
}

// Función para obtener todos los vehículos
function getAllVehicles($dbh) {
    $query = "SELECT * FROM tblvehicles ORDER BY FechaRegistro DESC";
    return $dbh->query($query)->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener todas las marcas
function getAllBrands($dbh) {
    $query = "SELECT * FROM tblbrands ORDER BY NombreMarca ASC";
    return $dbh->query($query)->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener un vehículo por ID
function getVehicleById($dbh, $id) {
    $stmt = $dbh->prepare("SELECT * FROM tblvehicles WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>ECUA CARS - Gestión de Vehículos</title>
    <link href="img/favicon.ico" rel="icon">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Rubik&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="css/sidebar.css">
</head>

<body>
    <div class="container-fluid">
        <?php include 'includes/sidebar.php'; ?>
        <!-- Main Content - Now uses full width -->
        <main role="main" class="content-wrapper col-md-9 ml-sm-auto col-lg-10 px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestión de Vehículos</h1>
                <button class="btn btn-primary" data-toggle="modal" data-target="#addVehicleModal">
                    <i class="fas fa-car"></i> Añadir Vehículo
                </button>
            </div>

            <!-- Vehicle List -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Marca</th>
                            <th>Precio/Día</th>
                            <th>Categoría</th>
                            <th>Modelo</th>
                            <th>Disponibilidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $vehicles = getAllVehicles($dbh);
                        foreach ($vehicles as $vehicle) {
                            echo "<tr>
                                <td>{$vehicle['id']}</td>
                                <td>{$vehicle['TituloVehiculo']}</td>
                                <td>{$vehicle['MarcaVehiculo']}</td>
                                <td>\${$vehicle['PrecioPorDia']}</td>
                                <td>{$vehicle['Categoria']}</td>
                                <td>{$vehicle['AnoModelo']}</td>
                                <td>" . ($vehicle['AireAcondicionado'] ? 'Disponible' : 'No Disponible') . "</td>
                                <td>
                                    <button class='btn btn-sm btn-info' onclick='editVehicle({$vehicle['id']})'>
                                        <i class='fas fa-edit'></i>
                                    </button>
                                    <button class='btn btn-sm btn-danger' onclick='deleteVehicle({$vehicle['id']})'>
                                        <i class='fas fa-trash'></i>
                                    </button>
                                </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Add Vehicle Modal -->
    <div class="modal fade" id="addVehicleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Añadir Vehículo</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addVehicleForm" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Título del Vehículo</label>
                                    <input type="text" class="form-control" name="TituloVehiculo" required>
                                </div>
                                <div class="form-group">
                                    <label>Marca del Vehículo</label>
                                    <select class="form-control" name="MarcaVehiculo" required>
                                        <?php
                                        $brands = getAllBrands($dbh);
                                        foreach ($brands as $brand) {
                                            echo "<option value='{$brand['NombreMarca']}'>{$brand['NombreMarca']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Precio por Día</label>
                                    <input type="number" class="form-control" name="PrecioPorDia" required>
                                </div>
                                <div class="form-group">
                                    <label>Tipo de Combustible</label>
                                    <select class="form-control" name="TipoCombustible">
                                        <option value="Gasolina">Gasolina</option>
                                        <option value="Diesel">Diesel</option>
                                        <option value="Eléctrico">Eléctrico</option>
                                        <option value="Híbrido">Híbrido</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Categoría</label>
                                    <select class="form-control" name="Categoria">
                                        <option value="Económico">Económico</option>
                                        <option value="Familiar">Familiar</option>
                                        <option value="Lujo">Lujo</option>
                                        <option value="SUV">SUV</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Transmisión</label>
                                    <select class="form-control" name="Transmision">
                                        <option value="Manual">Manual</option>
                                        <option value="Automático">Automático</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Año Modelo</label>
                                    <input type="number" class="form-control" name="AnoModelo" required>
                                </div>
                                <div class="form-group">
                                    <label>Capacidad de Asientos</label>
                                    <input type="number" class="form-control" name="CapacidadAsientos" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea class="form-control" name="DescripcionVehiculo" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Imagen del Vehículo</label>
                            <input type="file" class="form-control-file" name="Imagen1" required>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="AireAcondicionado" value="1">
                            <label class="form-check-label">Aire Acondicionado</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="GPS" value="1">
                            <label class="form-check-label">GPS</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="saveVehicle()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>

    <script>
        function saveVehicle() {
            const formData = new FormData(document.getElementById('addVehicleForm'));
            
            $.ajax({
                url: 'ajax/save_vehicle.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#addVehicleModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error al guardar vehículo: ' + error);
                }
            });
        }

        function editVehicle(vehicleId) {
            window.location.href = `edit_vehicle.php?id=${vehicleId}`;
        }

        function deleteVehicle(vehicleId) {
            if (confirm('¿Está seguro de que desea eliminar este vehículo?')) {
                $.ajax({
                    url: 'ajax/delete_vehicle.php',
                    method: 'POST',
                    data: { id: vehicleId },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error al eliminar vehículo: ' + error);
                    }
                });
            }
        }
    </script>
</body>
</html>