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
function getAllVehicles($dbh)
{
    $query = "SELECT v.*, b.NombreMarca, m.NombreModelo 
              FROM tblvehicles v 
              JOIN tblbrands b ON v.MarcaVehiculo = b.id
              JOIN tblmodels m ON v.IdModelo = m.id
              ORDER BY v.FechaRegistro DESC";
    return $dbh->query($query)->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener todas las marcas
function getAllBrands($dbh)
{
    $query = "SELECT * FROM tblbrands ORDER BY NombreMarca ASC";
    return $dbh->query($query)->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener todos los modelos
function getAllModels($dbh)
{
    $query = "SELECT * FROM tblmodels ORDER BY NombreModelo ASC";
    return $dbh->query($query)->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener modelos por marca
function getModelsByBrand($dbh, $brandId)
{
    $stmt = $dbh->prepare("SELECT * FROM tblmodels WHERE IdMarca = ? ORDER BY NombreModelo ASC");
    $stmt->execute([$brandId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener un vehículo por ID
function getVehicleById($dbh, $id)
{
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
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Rubik&display=swap"
        rel="stylesheet">
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
            <div
                class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-2 mb-3 border-bottom">
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
            <td>{$vehicle['NombreMarca']} {$vehicle['NombreModelo']}</td>
            <td>{$vehicle['NombreMarca']}</td>
            <td>\${$vehicle['PrecioPorDia']}</td>
            <td>{$vehicle['Categoria']}</td>
            <td>{$vehicle['AnoModelo']}</td>
            <td>{$vehicle['Estado']}</td>
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
                                    <label>Marca del Vehículo</label>
                                    <select class="form-control" name="MarcaVehiculo" id="MarcaVehiculo" required>
                                        <option value="">Seleccionar Marca</option>
                                        <?php
                                        $brands = getAllBrands($dbh);
                                        foreach ($brands as $brand) {
                                            echo "<option value='{$brand['id']}'>{$brand['NombreMarca']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Modelo del Vehículo</label>
                                    <select class="form-control" name="IdModelo" id="IdModelo" required>
                                        <option value="">Seleccionar Modelo</option>
                                        <?php
                                        $models = getAllModels($dbh);
                                        foreach ($models as $model) {
                                            echo "<option value='{$model['id']}' data-marca='{$model['IdMarca']}'>{$model['NombreModelo']}</option>";
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
                                <div class="form-group">
                                    <label>Número de Motor</label>
                                    <input type="text" class="form-control" name="NumeroMotor" required>
                                </div>
                                <div class="form-group">
                                    <label>Tipo de Motor</label>
                                    <input type="text" class="form-control" name="TipoMotor">
                                </div>
                                <div class="form-group">
                                    <label>Potencia de Motor (HP)</label>
                                    <input type="number" class="form-control" name="PotenciaMotor">
                                </div>
                                <div class="form-group">
                                    <label>Fabricante de Motor</label>
                                    <input type="text" class="form-control" name="FabricanteMotor">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Categoría</label>
                                    <select class="form-control" name="Categoria">
                                        <option value="Compacto">Compacto</option>
                                        <option value="Familiar">Familiar</option>
                                        <option value="Lujo">Lujo</option>
                                        <option value="SUV">SUV</option>
                                        <option value="Camioneta">Camioneta</option>
                                        <option value="Deportivo">Deportivo</option>
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
                                <div class="form-group">
                                    <label>Número de Chasis</label>
                                    <input type="text" class="form-control" name="NumeroChasis" required>
                                </div>
                                <div class="form-group">
                                    <label>Tipo de Chasis</label>
                                    <input type="text" class="form-control" name="TipoChasis">
                                </div>
                                <div class="form-group">
                                    <label>Material de Chasis</label>
                                    <input type="text" class="form-control" name="MaterialChasis">
                                </div>
                                <div class="form-group">
                                    <label>Fabricante de Chasis</label>
                                    <input type="text" class="form-control" name="FabricanteChasis">
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
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Características Adicionales</label>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="AireAcondicionado"
                                        name="AireAcondicionado" value="1">
                                    <label class="form-check-label" for="AireAcondicionado">Aire Acondicionado</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="GPS" name="GPS" value="1">
                                    <label class="form-check-label" for="GPS">GPS</label>
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="Estado">Estado del Vehículo</label>
                                <select class="form-control" id="Estado" name="Estado">
                                    <option value="Disponible">Disponible</option>
                                    <option value="Reservado">Reservado</option>
                                    <option value="En mantenimiento">En mantenimiento</option>
                                </select>
                            </div>
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

    <!-- Edit Vehicle Modal -->
    <div class="modal fade" id="editVehicleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Vehículo</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editVehicleForm" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Marca del Vehículo</label>
                                    <select class="form-control" name="MarcaVehiculo" id="editMarcaVehiculo" required>
                                        <option value="">Seleccionar Marca</option>
                                        <?php
                                        $brands = getAllBrands($dbh);
                                        foreach ($brands as $brand) {
                                            echo "<option value='{$brand['id']}'>{$brand['NombreMarca']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Modelo del Vehículo</label>
                                    <select class="form-control" name="IdModelo" id="editIdModelo" required>
                                        <option value="">Seleccionar Modelo</option>
                                        <?php
                                        $models = getAllModels($dbh);
                                        foreach ($models as $model) {
                                            echo "<option value='{$model['id']}' data-marca='{$model['IdMarca']}'>{$model['NombreModelo']}</option>";
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
                                <div class="form-group">
                                    <label>Número de Motor</label>
                                    <input type="text" class="form-control" name="NumeroMotor" required>
                                </div>
                                <div class="form-group">
                                    <label>Tipo de Motor</label>
                                    <input type="text" class="form-control" name="TipoMotor">
                                </div>
                                <div class="form-group">
                                    <label>Potencia de Motor (HP)</label>
                                    <input type="number" class="form-control" name="PotenciaMotor">
                                </div>
                                <div class="form-group">
                                    <label>Fabricante de Motor</label>
                                    <input type="text" class="form-control" name="FabricanteMotor">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Categoría</label>
                                    <select class="form-control" name="Categoria">
                                        <option value="Compacto">Compacto</option>
                                        <option value="Familiar">Familiar</option>
                                        <option value="Lujo">Lujo</option>
                                        <option value="SUV">SUV</option>
                                        <option value="Camioneta">Camioneta</option>
                                        <option value="Deportivo">Deportivo</option>
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
                                <div class="form-group">
                                    <label>Número de Chasis</label>
                                    <input type="text" class="form-control" name="NumeroChasis" required>
                                </div>
                                <div class="form-group">
                                    <label>Tipo de Chasis</label>
                                    <input type="text" class="form-control" name="TipoChasis">
                                </div>
                                <div class="form-group">
                                    <label>Material de Chasis</label>
                                    <input type="text" class="form-control" name="MaterialChasis">
                                </div>
                                <div class="form-group">
                                    <label>Fabricante de Chasis</label>
                                    <input type="text" class="form-control" name="FabricanteChasis">
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
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Características Adicionales</label>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="AireAcondicionado"
                                        name="AireAcondicionado" value="1">
                                    <label class="form-check-label" for="AireAcondicionado">Aire Acondicionado</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="GPS" name="GPS" value="1">
                                    <label class="form-check-label" for="GPS">GPS</label>
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="Estado">Estado del Vehículo</label>
                                <select class="form-control" id="Estado" name="Estado">
                                    <option value="Disponible">Disponible</option>
                                    <option value="Reservado">Reservado</option>
                                    <option value="En mantenimiento">En mantenimiento</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="updateVehicle()">Guardar Cambios</button>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>

        <script>
            $(document).ready(function () {
                // Filtrar modelos según la marca seleccionada
                $('#MarcaVehiculo').change(function () {
                    const marcaId = $(this).val();
                    $('#IdModelo option').each(function () {
                        const modelMarcaId = $(this).data('marca');
                        if (marcaId === '' || marcaId == modelMarcaId) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                    $('#IdModelo').val('');
                });

                function saveVehicle() {
                    const formData = new FormData(document.getElementById('addVehicleForm'));

                    // Añadir el título del vehículo generado por marca y modelo
                    const marca = $('#MarcaVehiculo option:selected').text();
                    const modelo = $('#IdModelo option:selected').text().split(' (')[0];
                    const tituloVehiculo = `${marca} ${modelo}`;
                    formData.append('TituloVehiculo', tituloVehiculo);

                    $.ajax({
                        url: 'ajax/save_vehicle.php',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            if (response.success) {
                                $('#addVehicleModal').modal('hide');
                                location.reload();
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function (xhr, status, error) {
                            alert('Error al guardar vehículo: ' + error);
                        }
                    });
                }
            });

            function deleteVehicle(vehicleId) {
                if (confirm('¿Está seguro de que desea eliminar este vehículo?')) {
                    $.ajax({
                        url: 'ajax/delete_vehicle.php',
                        method: 'POST',
                        data: { id: vehicleId },
                        success: function (response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function (xhr, status, error) {
                            alert('Error al eliminar vehículo: ' + error);
                        }
                    });
                }
            }

            // Función para cargar los datos del vehículo en el modal de edición
            function editVehicle(vehicleId) {
                $.ajax({
                    url: 'ajax/get_vehicle_details.php',
                    method: 'GET',
                    data: { id: vehicleId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            const vehicle = response.vehicle;

                            // Llenar el formulario de edición con los datos del vehículo
                            $('#editMarcaVehiculo').val(vehicle.MarcaVehiculo);

                            // Filtrar y mostrar solo los modelos de la marca seleccionada
                            $('#editIdModelo option').each(function () {
                                const modelMarcaId = $(this).data('marca');
                                if (vehicle.MarcaVehiculo == modelMarcaId) {
                                    $(this).show();
                                } else {
                                    $(this).hide();
                                }
                            });

                            $('#editIdModelo').val(vehicle.IdModelo);
                            $('input[name="PrecioPorDia"]').val(vehicle.PrecioPorDia);
                            $('select[name="TipoCombustible"]').val(vehicle.TipoCombustible);
                            $('input[name="NumeroMotor"]').val(vehicle.NumeroMotor);
                            $('input[name="TipoMotor"]').val(vehicle.TipoMotor);
                            $('input[name="PotenciaMotor"]').val(vehicle.PotenciaMotor);
                            $('input[name="FabricanteMotor"]').val(vehicle.FabricanteMotor);
                            $('select[name="Categoria"]').val(vehicle.Categoria);
                            $('select[name="Transmision"]').val(vehicle.Transmision);
                            $('input[name="AnoModelo"]').val(vehicle.AnoModelo);
                            $('input[name="CapacidadAsientos"]').val(vehicle.CapacidadAsientos);
                            $('input[name="NumeroChasis"]').val(vehicle.NumeroChasis);
                            $('input[name="TipoChasis"]').val(vehicle.TipoChasis);
                            $('input[name="MaterialChasis"]').val(vehicle.MaterialChasis);
                            $('input[name="FabricanteChasis"]').val(vehicle.FabricanteChasis);
                            $('textarea[name="DescripcionVehiculo"]').val(vehicle.DescripcionVehiculo);
                            $('select[name="Estado"]').val(vehicle.Estado);

                            // Manejar checkboxes
                            $('#AireAcondicionado').prop('checked', vehicle.AireAcondicionado === 1);
                            $('#GPS').prop('checked', vehicle.GPS === 1);

                            // Añadir campo oculto para el ID del vehículo
                            $('#editVehicleForm').append(`<input type="hidden" name="id" value="${vehicle.id}">`);

                            // Mostrar el modal de edición
                            $('#editVehicleModal').modal('show');
                        } else {
                            alert('Error al cargar los datos del vehículo: ' + response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        alert('Error al cargar los datos del vehículo: ' + error);
                    }
                });
            }

            // Función para guardar los cambios del vehículo
            function updateVehicle() {
                const formData = new FormData(document.getElementById('editVehicleForm'));

                $.ajax({
                    url: 'ajax/edit_vehicle.php',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (response) {
                        if (response && response.success) {
                            // Mostrar alerta de éxito
                            alert('Vehículo actualizado correctamente');

                            $('#editVehicleModal').modal('hide');
                            location.reload();
                        } else {
                            alert('Error: ' + (response ? response.message : 'Respuesta inválida'));
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            alert('Error al actualizar vehículo: ' + (errorResponse.message || error));
                        } catch (e) {
                            alert('Error al actualizar vehículo: ' + error);
                        }
                    }
                });
            }

            // Añadir en el script existente, junto con los otros event listeners
            $(document).ready(function () {
                // Filtrar modelos según la marca seleccionada (para modal de edición)
                $('#editMarcaVehiculo').change(function () {
                    const marcaId = $(this).val();
                    $('#editIdModelo option').each(function () {
                        const modelMarcaId = $(this).data('marca');
                        if (marcaId === '' || marcaId == modelMarcaId) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                    $('#editIdModelo').val('');
                });
            });
        </script>
</body>

</html>