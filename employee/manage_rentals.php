<?php
session_start();
require_once '../includes/config.php';

// Verificar autenticación de empleado
// if (!isset($_SESSION['empleado_id'])) {
//     header("Location: login.php");
//     exit();
// }

// Mensajes de alerta
$alertMessage = '';
$alertType = '';

if (isset($_SESSION['mensaje'])) {
    $alertMessage = $_SESSION['mensaje'];
    $alertType = 'success';
    unset($_SESSION['mensaje']);
}

if (isset($_SESSION['error'])) {
    $alertMessage = $_SESSION['error'];
    $alertType = 'danger';
    unset($_SESSION['error']);
}

// Paginación
$registrosPorPagina = 10;
$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$inicio = ($pagina > 1) ? ($pagina * $registrosPorPagina) - $registrosPorPagina : 0;

// Filtros de búsqueda
$filtroEstado = isset($_GET['estado']) ? $_GET['estado'] : '';
$filtroFechaDesde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : '';
$filtroFechaHasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : '';

// Construcción de consulta dinámica
$whereConditions = [];
$params = [];

if (!empty($filtroEstado)) {
    $whereConditions[] = "b.Estado = :estado";
    $params[':estado'] = $filtroEstado;
}

if (!empty($filtroFechaDesde)) {
    $whereConditions[] = "b.FechaDesde >= :fecha_desde";
    $params[':fecha_desde'] = $filtroFechaDesde;
}

if (!empty($filtroFechaHasta)) {
    $whereConditions[] = "b.FechaHasta <= :fecha_hasta";
    $params[':fecha_hasta'] = $filtroFechaHasta;
}


$whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

try {
    // Consulta para contar total de registros
    $sqlTotal = "SELECT COUNT(*) as total FROM tblbooking b 
                 INNER JOIN tblvehicles v ON b.IdVehiculo = v.id 
                 INNER JOIN tblconductores c ON b.IdConductor = c.id
                 $whereClause";

    $stmtTotal = $dbh->prepare($sqlTotal);
    $stmtTotal->execute($params);
    $totalRegistros = $stmtTotal->fetchColumn();

    $totalPaginas = ceil($totalRegistros / $registrosPorPagina);

    // Consulta para obtener reservas con paginación
    $sqlReservas = "SELECT b.*, 
                b2.NombreMarca, 
                m.NombreModelo, 
                v.AnoModelo,
                CONCAT(b2.NombreMarca, ' ', m.NombreModelo, ' ', v.AnoModelo) AS NombreVehiculo,
                c.Nombre AS NombreConductor 
                FROM tblbooking b 
                INNER JOIN tblvehicles v ON b.IdVehiculo = v.id 
                INNER JOIN tblmodels m ON v.IdModelo = m.id
                INNER JOIN tblbrands b2 ON v.MarcaVehiculo = b2.id
                INNER JOIN tblconductores c ON b.IdConductor = c.id
                $whereClause
                ORDER BY b.FechaDesde DESC
                LIMIT :limite OFFSET :inicio";

    $stmtReservas = $dbh->prepare($sqlReservas);

    // Bindear parámetros existentes
    foreach ($params as $key => $value) {
        $stmtReservas->bindValue($key, $value);
    }

    // Bindear parámetros LIMIT y OFFSET como enteros
    $stmtReservas->bindValue(':limite', $registrosPorPagina, PDO::PARAM_INT);
    $stmtReservas->bindValue(':inicio', $inicio, PDO::PARAM_INT);

    $stmtReservas->execute();
    $reservas = $stmtReservas->fetchAll(PDO::FETCH_ASSOC);

    // Función para editar reservas
    function editarReserva($id, $datos)
    {
        global $dbh;
        try {
            $sql = "UPDATE tblbooking SET 
                IdVehiculo = :id_vehiculo,
                IdConductor = :id_conductor,
                FechaDesde = :fecha_desde,
                FechaHasta = :fecha_hasta,
                CostoTotal = :costo_total,
                Estado = :estado,
                CorreoUsuario = :correo_usuario,
                FormaPago = :forma_pago,
                mensaje = :mensaje
                WHERE id = :id";

            $stmt = $dbh->prepare($sql);
            $stmt->execute([
                ':id_vehiculo' => $datos['id_vehiculo'],
                ':id_conductor' => $datos['id_conductor'],
                ':fecha_desde' => $datos['fecha_desde'],
                ':fecha_hasta' => $datos['fecha_hasta'],
                ':costo_total' => $datos['costo_total'],
                ':estado' => $datos['estado'],
                ':correo_usuario' => $datos['correo_usuario'],
                ':forma_pago' => $datos['forma_pago'],
                ':mensaje' => $datos['mensaje'],
                ':id' => $id
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Error al editar reserva: " . $e->getMessage());
            return false;
        }
    }

    // Función para cancelar/eliminar reservas
    function cancelarReserva($id)
    {
        global $dbh;
        try {
            // Opción 1: Cambiar estado a cancelado
            $sql = "UPDATE tblbooking SET Estado = 3 WHERE id = :id";

            // Opción 2: Eliminar completamente la reserva (descomentar si se prefiere)
            // $sql = "DELETE FROM tblbooking WHERE id = :id";

            $stmt = $dbh->prepare($sql);
            $stmt->execute([':id' => $id]);
            return true;
        } catch (PDOException $e) {
            error_log("Error al cancelar reserva: " . $e->getMessage());
            return false;
        }
    }

    // Manejar acciones de edición y cancelación
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Lógica de edición de reserva
        if (isset($_POST['accion']) && $_POST['accion'] == 'editar_reserva') {
            $id = $_POST['id_reserva'];
            $datosReserva = [
                'id_vehiculo' => $_POST['id_vehiculo'],
                'id_conductor' => $_POST['id_conductor'],
                'fecha_desde' => $_POST['fecha_desde'],
                'fecha_hasta' => $_POST['fecha_hasta'],
                'costo_total' => $_POST['costo_total'],
                'estado' => $_POST['estado'],
                'correo_usuario' => $_POST['correo_usuario'],
                'forma_pago' => $_POST['forma_pago'],
                'mensaje' => $_POST['mensaje']
            ];

            if (editarReserva($id, $datosReserva)) {
                $_SESSION['mensaje'] = "Reserva actualizada exitosamente";
                header("Location: manage_rentals.php");
                exit();
            } else {
                $_SESSION['error'] = "Error al actualizar la reserva";
            }
        }
    }

    // Manejar cancelación de reserva via GET
    if (isset($_GET['accion']) && $_GET['accion'] == 'cancelar' && isset($_GET['id'])) {
        $id = $_GET['id'];
        if (cancelarReserva($id)) {
            $_SESSION['mensaje'] = "Reserva cancelada exitosamente";
            header("Location: manage_rentals.php");
            exit();
        } else {
            $_SESSION['error'] = "Error al cancelar la reserva";
        }
    }
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
                <?php if (!empty($alertMessage)): ?>
                    <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($alertMessage); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Gestión de Alquileres</h1>
                </div>

                <!-- Filtros de Búsqueda -->
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <select name="estado" class="form-control">
                                <option value="">Todos los Estados</option>
                                <option value="1">Activo</option>
                                <option value="2">Completado</option>
                                <option value="3">Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="fecha_desde" class="form-control" placeholder="Fecha Desde">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="fecha_hasta" class="form-control" placeholder="Fecha Hasta">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Buscar</button>
                            <a href="manage_rentals.php" class="btn btn-secondary">Limpiar</a>
                        </div>
                    </div>
                </form>

                <!-- Tabla de Reservas -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Número Reserva</th>
                                <th>Vehículo</th>
                                <th>Conductor</th>
                                <th>Fecha Desde</th>
                                <th>Fecha Hasta</th>
                                <th>Costo Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservas as $reserva): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reserva['NumeroReserva']); ?></td>
                                    <td><?php echo htmlspecialchars($reserva['NombreVehiculo']); ?></td>
                                    <td><?php echo htmlspecialchars($reserva['NombreConductor']); ?></td>
                                    <td><?php echo htmlspecialchars($reserva['FechaDesde']); ?></td>
                                    <td><?php echo htmlspecialchars($reserva['FechaHasta']); ?></td>
                                    <td>$<?php echo number_format($reserva['CostoTotal'], 2); ?></td>
                                    <td>
                                        <?php
                                        switch ($reserva['Estado']) {
                                            case 1:
                                                echo '<span class="badge badge-primary">Activo</span>';
                                                break;
                                            case 2:
                                                echo '<span class="badge badge-success">Completado</span>';
                                                break;
                                            case 3:
                                                echo '<span class="badge badge-danger">Cancelado</span>';
                                                break;
                                            default:
                                                echo '<span class="badge badge-warning">Sin Definir</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                                data-target="#detallesReserva<?php echo $reserva['id']; ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-warning btn-sm"
                                                onclick='cargarDatosEdicion(<?php echo json_encode($reserva); ?>)'>
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm"
                                                onclick="confirmarCancelacion(<?php echo $reserva['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Detalles Reserva -->
                                <div class="modal fade" id="detallesReserva<?php echo $reserva['id']; ?>" tabindex="-1"
                                    role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Detalles Reserva
                                                    #<?php echo htmlspecialchars($reserva['NumeroReserva']); ?></h5>
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <!-- Aquí irían los detalles completos de la reserva -->
                                                <p><strong>Correo Usuario:</strong>
                                                    <?php echo htmlspecialchars($reserva['CorreoUsuario']); ?></p>
                                                <p><strong>Forma de Pago:</strong>
                                                    <?php echo htmlspecialchars($reserva['FormaPago']); ?></p>
                                                <p><strong>Mensaje:</strong>
                                                    <?php echo htmlspecialchars($reserva['mensaje']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                            <li class="page-item <?php echo ($i == $pagina) ? 'active' : ''; ?>">
                                <a class="page-link" href="?pagina=<?php echo $i;
                                echo !empty($filtroEstado) ? "&estado=$filtroEstado" : '';
                                echo !empty($filtroFechaDesde) ? "&fecha_desde=$filtroFechaDesde" : '';
                                echo !empty($filtroFechaHasta) ? "&fecha_hasta=$filtroFechaHasta" : '';
                                ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </main>
        </div>

        <div class="modal fade" id="modalEditarReserva" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" action="">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Reserva</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="accion" value="editar_reserva">
                            <input type="hidden" name="id_reserva" id="edit_id_reserva">

                            <div class="form-group">
                                <label>Vehículo</label>
                                <select name="id_vehiculo" id="edit_id_vehiculo" class="form-control" required>
                                    <?php
                                    // Consulta para obtener vehículos con marca, modelo y año
                                    $stmt = $dbh->query("SELECT v.id, 
                                    b.NombreMarca, 
                                    m.NombreModelo, 
                                    v.AnoModelo,
                                    CONCAT(b.NombreMarca, ' ', m.NombreModelo, ' ', v.AnoModelo) AS NombreVehiculo
                                FROM tblvehicles v
                                INNER JOIN tblbrands b ON v.MarcaVehiculo = b.id
                                INNER JOIN tblmodels m ON v.IdModelo = m.id");
                                    while ($vehicle = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value='{$vehicle['id']}'>{$vehicle['NombreVehiculo']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Conductor</label>
                                <select name="id_conductor" id="edit_id_conductor" class="form-control" required>
                                    <?php
                                    // Cargar conductores desde la base de datos
                                    $stmt = $dbh->query("SELECT id, Nombre FROM tblconductores");
                                    while ($driver = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value='{$driver['id']}'>{$driver['Nombre']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Fecha Desde</label>
                                <input type="date" name="fecha_desde" id="edit_fecha_desde" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Fecha Hasta</label>
                                <input type="date" name="fecha_hasta" id="edit_fecha_hasta" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Costo Total</label>
                                <input type="number" step="0.01" name="costo_total" id="edit_costo_total"
                                    class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Estado</label>
                                <select name="estado" id="edit_estado" class="form-control" required>
                                    <option value="1">Activo</option>
                                    <option value="2">Completado</option>
                                    <option value="3">Cancelado</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Correo Usuario</label>
                                <input type="email" name="correo_usuario" id="edit_correo_usuario" class="form-control"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>Forma de Pago</label>
                                <input type="text" name="forma_pago" id="edit_forma_pago" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Mensaje</label>
                                <textarea name="mensaje" id="edit_mensaje" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            function confirmarCancelacion(idReserva) {
                if (confirm('¿Está seguro de que desea cancelar esta reserva?')) {
                    window.location.href = 'cancelar_reserva.php?id=' + idReserva;
                }
            }

            function cargarDatosEdicion(reserva) {
                // Cargar datos de la reserva en el modal de edición
                document.getElementById('edit_id_reserva').value = reserva.id;
                document.getElementById('edit_id_vehiculo').value = reserva.IdVehiculo;
                document.getElementById('edit_id_conductor').value = reserva.IdConductor;
                document.getElementById('edit_fecha_desde').value = reserva.FechaDesde;
                document.getElementById('edit_fecha_hasta').value = reserva.FechaHasta;
                document.getElementById('edit_costo_total').value = reserva.CostoTotal;
                document.getElementById('edit_estado').value = reserva.Estado;
                document.getElementById('edit_correo_usuario').value = reserva.CorreoUsuario;
                document.getElementById('edit_forma_pago').value = reserva.FormaPago;
                document.getElementById('edit_mensaje').value = reserva.mensaje;

                // Abrir modal
                $('#modalEditarReserva').modal('show');
            }

            function confirmarCancelacion(idReserva) {
                if (confirm('¿Está seguro de que desea cancelar esta reserva?')) {
                    window.location.href = 'manage_rentals.php?accion=cancelar&id=' + idReserva;
                }
            }
        </script>
    </body>

    </html>

    <?php
} catch (PDOException $e) {
    // Manejo de errores
    die("Error en la consulta: " . $e->getMessage());
}
?>