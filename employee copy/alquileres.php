<?php
require_once '../includes/config.php';
require_once 'includes/funciones.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>ECUA CARS - Gestión de Alquileres</title>

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
            <h2 class="text-center text-primary mt-3">Gestión de Alquileres</h2>

            <!-- Contenedor para Notificaciones -->
            <?php
            if (isset($_GET['status'])) {
                $status = $_GET['status'];
                $message = isset($_GET['message']) ? urldecode($_GET['message']) : '';

                if ($status === 'success') {
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Alquiler registrado con éxito. Número de Reserva: ' . htmlspecialchars($_GET['numReserva']) . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                } elseif ($status === 'error') {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        ' . htmlspecialchars($message) . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                } elseif ($status === 'devolucion_success') {
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Devolución de vehículo procesada correctamente.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                }
            }
            ?>

            <!-- Formulario de Nuevo Alquiler -->
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">Registrar Nuevo Alquiler</div>
                <div class="card-body">
                    <form action="procesar_alquiler.php" method="POST" id="formularioAlquiler">
                        <!-- Datos del Usuario -->
                        <h5 class="mb-3 text-primary">Datos del Usuario</h5>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-check form-check-inline mb-3">
                                    <input class="form-check-input" type="radio" name="tipoUsuario"
                                        id="usuarioExistente" value="existente" checked>
                                    <label class="form-check-label" for="usuarioExistente">
                                        Usuario Existente
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="tipoUsuario" id="usuarioNuevo"
                                        value="nuevo">
                                    <label class="form-check-label" for="usuarioNuevo">
                                        Nuevo Usuario
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Contenedor de Usuarios -->
                        <div id="contenedorUsuarioExistente" class="row mb-3">
                            <div class="col-md-12">
                                <label for="CorreoExistente" class="form-label">Seleccionar Usuario Existente</label>
                                <select class="form-select" id="CorreoExistente" name="CorreoExistente">
                                    <option value="" disabled selected>Seleccione un usuario</option>
                                    <?php
                                    $stmt = $dbh->query("SELECT CorreoElectronico, NombreCompleto FROM tblusers");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value='{$row['CorreoElectronico']}'>{$row['NombreCompleto']} ({$row['CorreoElectronico']})</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div id="contenedorUsuarioNuevo" class="row mb-3" style="display:none;">
                            <div class="col-md-4">
                                <label for="NombreCompleto" class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" id="NombreCompleto" name="NombreCompleto">
                            </div>
                            <div class="col-md-4">
                                <label for="Correo" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="Correo" name="Correo">
                            </div>
                            <div class="col-md-4">
                                <label for="NumeroContacto" class="form-label">Número de Contacto</label>
                                <input type="text" class="form-control" id="NumeroContacto" name="NumeroContacto">
                            </div>
                        </div>

                        <!-- Selección de Vehículo -->
                        <h5 class="mb-3 text-primary">Seleccionar Vehículo</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="IdVehiculo" class="form-label">Vehículo</label>
                                <select class="form-select" id="IdVehiculo" name="IdVehiculo" required>
                                    <option value="" disabled selected>Seleccione un vehículo</option>
                                    <?php
                                    $stmt = $dbh->query("
                                        SELECT v.id, 
                                               CONCAT(b.NombreMarca, ' ', m.NombreModelo, ' (', v.AnoModelo, ')') as TituloVehiculo,
                                               v.PrecioPorDia
                                        FROM tblvehicles v
                                        JOIN tblbrands b ON v.MarcaVehiculo = b.id
                                        JOIN tblmodels m ON v.IdModelo = m.id
                                        WHERE v.Estado = 'Disponible'
                                    ");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value='{$row['id']}' data-precio='{$row['PrecioPorDia']}'>
                                                {$row['TituloVehiculo']} - ${$row['PrecioPorDia']}/día
                                              </option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- Conductor -->
                        <h5 class="mb-3 text-primary">Datos del Conductor</h5>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="IdConductor" class="form-label">Conductor</label>
                                <select class="form-select" id="IdConductor" name="IdConductor" required>
                                    <option value="" disabled selected>Seleccione un conductor</option>
                                    <?php
                                    $stmt = $dbh->query("SELECT id, Nombre FROM tblconductores");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value='{$row['id']}'>{$row['Nombre']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="Licencia" class="form-label">Tipo de Licencia</label>
                                <select class="form-select" id="Licencia" name="Licencia" required>
                                    <option value="" disabled selected>Seleccione tipo de licencia</option>
                                    <option value="E">Licencia Tipo E</option>
                                    <option value="D">Licencia Tipo D</option>
                                    <option value="C">Licencia Tipo C</option>
                                    <option value="B">Licencia Tipo B</option>
                                    <option value="F">Licencia Tipo F</option>
                                </select>
                            </div>
                        </div>

                        <!-- Fechas de Alquiler -->
                        <h5 class="mb-3 text-primary">Periodo de Alquiler</h5>
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

                        <!-- Resumen de Precio -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Resumen de Precio</h5>
                                        <p>Días de Alquiler: <span id="diasAlquiler">0</span></p>
                                        <p>Precio por Día: $<span id="precioPorDia">0</span></p>
                                        <p>Precio Base: $<span id="precioBase">0</span></p>
                                        <p>Descuento: $<span id="descuento">0</span></p>
                                        <p class="text-primary"><strong>Precio Total: $<span
                                                    id="precioTotal">0</span></strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h5 class="mb-3 text-primary">Método de Pago</h5>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <select class="form-select" id="MetodoPago" name="MetodoPago" required>
                                    <option value="" disabled selected>Seleccione un método de pago</option>
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Tarjeta de Crédito">Tarjeta de Crédito</option>
                                    <option value="Tarjeta de Débito">Tarjeta de Débito</option>
                                    <option value="Transferencia Bancaria">Transferencia Bancaria</option>
                                    <option value="PayPal">PayPal</option>
                                </select>
                            </div>
                        </div>

                        <!-- Información de Pago Adicional (opcional) -->
                        <div id="infoPagoAdicional" style="display:none;">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="NumeroTarjeta" class="form-label">Número de Tarjeta</label>
                                    <input type="text" class="form-control" id="NumeroTarjeta" name="NumeroTarjeta"
                                        pattern="[0-9]{16}" placeholder="Solo números (16 dígitos)">
                                </div>
                                <div class="col-md-3">
                                    <label for="FechaExpiracion" class="form-label">Fecha Expiración</label>
                                    <input type="month" class="form-control" id="FechaExpiracion"
                                        name="FechaExpiracion">
                                </div>
                                <div class="col-md-3">
                                    <label for="CVV" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="CVV" name="CVV" pattern="[0-9]{3}"
                                        placeholder="3 dígitos">
                                </div>
                            </div>
                        </div>

                        <!-- Botón de Envío -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Registrar Alquiler</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de Alquileres -->
            <div class="table-responsive mt-5">
                <h5 class="text-primary">Registros de Alquileres</h5>
                <table class="table table-bordered table-striped">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>#</th>
                            <th>Número de Reserva</th>
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
                        <?php
                        $stmt = $dbh->query("
                            SELECT 
                                b.id, 
                                b.NumeroReserva,
                                CONCAT(br.NombreMarca, ' ', m.NombreModelo, ' (', v.AnoModelo, ')') AS Vehiculo,
                                c.Nombre AS Conductor,
                                b.FechaDesde,
                                b.FechaHasta,
                                b.CostoTotal,
                                b.Estado
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
                            echo "<td>{$row['Vehiculo']}</td>";
                            echo "<td>{$row['Conductor']}</td>";
                            echo "<td>{$row['FechaDesde']}</td>";
                            echo "<td>{$row['FechaHasta']}</td>";
                            echo "<td>$" . number_format($row['CostoTotal'], 2) . "</td>";
                            echo "<td>" . ($row['Estado'] == 1 ? 'Activo' : 'Finalizado') . "</td>";
                            echo "<td>
                                    <a href='devolucion.php?id={$row['id']}' class='btn btn-sm btn-warning'>Devolución</a>
                                    <a href='contrato.php?id={$row['id']}' class='btn btn-sm btn-info' target='_blank'>Contrato</a>
                                  </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="../js/bootstrap.bundle.min.js"></script>
    <script>
        // Configuración de fechas
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('FechaDesde').setAttribute('min', today);
        document.getElementById('FechaHasta').setAttribute('min', today);

        // Sincronizar fechas
        document.getElementById('FechaDesde').addEventListener('change', function () {
            const fechaDesde = this.value;
            document.getElementById('FechaHasta').setAttribute('min', fechaDesde);
        });

        // Cálculo de precios dinámico
        const vehiculoSelect = document.getElementById('IdVehiculo');
        const fechaDesdeInput = document.getElementById('FechaDesde');
        const fechaHastaInput = document.getElementById('FechaHasta');

        function calcularPrecio() {
            const vehiculo = vehiculoSelect.options[vehiculoSelect.selectedIndex];
            const precioPorDia = parseFloat(vehiculo.getAttribute('data-precio'));
            const fechaDesde = new Date(fechaDesdeInput.value);
            const fechaHasta = new Date(fechaHastaInput.value);

            if (fechaDesde && fechaHasta) {
                const diasAlquiler = Math.ceil((fechaHasta - fechaDesde) / (1000 * 60 * 60 * 24)) + 1;
                const precioBase = diasAlquiler * precioPorDia;
                let descuento = 0;

                if (diasAlquiler > 7) {
                    descuento = precioBase * 0.10;
                }

                const precioTotal = precioBase - descuento;

                document.getElementById('diasAlquiler').textContent = diasAlquiler;
                document.getElementById('precioPorDia').textContent = precioPorDia.toFixed(2);
                document.getElementById('precioBase').textContent = precioBase.toFixed(2);
                document.getElementById('descuento').textContent = descuento.toFixed(2);
                document.getElementById('precioTotal').textContent = precioTotal.toFixed(2);
            }
        }

        vehiculoSelect.addEventListener('change', calcularPrecio);
        fechaDesdeInput.addEventListener('change', calcularPrecio);
        fechaHastaInput.addEventListener('change', calcularPrecio);

        // Selector de tipo de usuario
        const usuarioExistente = document.getElementById('usuarioExistente');
        const usuarioNuevo = document.getElementById('usuarioNuevo');
        const contenedorExistente = document.getElementById('contenedorUsuarioExistente');
        const contenedorNuevo = document.getElementById('contenedorUsuarioNuevo');

        // Campos requeridos
        const camposExistente = document.getElementById('CorreoExistente');
        const camposNuevos = [
            document.getElementById('NombreCompleto'),
            document.getElementById('Correo'),
            document.getElementById('NumeroContacto')
        ];

        usuarioExistente.addEventListener('change', function () {
            contenedorExistente.style.display = 'block';
            contenedorNuevo.style.display = 'none';

            // Hacer campos requeridos
            camposExistente.setAttribute('required', 'required');
            camposNuevos.forEach(campo => campo.removeAttribute('required'));
        });

        usuarioNuevo.addEventListener('change', function () {
            contenedorExistente.style.display = 'none';
            contenedorNuevo.style.display = 'block';

            // Hacer campos requeridos
            camposExistente.removeAttribute('required');
            camposNuevos.forEach(campo => campo.setAttribute('required', 'required'));
        });

        // Manejo de métodos de pago
        const metodoPagoSelect = document.getElementById('MetodoPago');
        const infoPagoAdicional = document.getElementById('infoPagoAdicional');
        const camposTarjeta = [
            document.getElementById('NumeroTarjeta'),
            document.getElementById('FechaExpiracion'),
            document.getElementById('CVV')
        ];

        metodoPagoSelect.addEventListener('change', function() {
            const metodoPago = this.value;
            
            // Mostrar/ocultar campos adicionales según el método de pago
            if (['Tarjeta de Crédito', 'Tarjeta de Débito'].includes(metodoPago)) {
                infoPagoAdicional.style.display = 'block';
                camposTarjeta.forEach(campo => campo.setAttribute('required', 'required'));
            } else {
                infoPagoAdicional.style.display = 'none';
                camposTarjeta.forEach(campo => campo.removeAttribute('required'));
            }
        });
    </script>
</body>

</html>