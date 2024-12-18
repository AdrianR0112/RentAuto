<?php
// Conexión a la base de datos
require_once 'db.php';

// Capturar datos del formulario
$NombreCompleto = $_POST['NombreCompleto'];
$Correo = $_POST['Correo'];
$NumeroContacto = $_POST['NumeroContacto'];
$FechaNacimiento = $_POST['FechaNacimiento'];
$Direccion = $_POST['Direccion'];
$Ciudad = $_POST['Ciudad'];
$Pais = $_POST['Pais'];
$IdVehiculo = $_POST['IdVehiculo'];
$NombreConductor = $_POST['NombreConductor'];
$Licencia = $_POST['Licencia'];
$Telefono = $_POST['Telefono'];
$CorreoConductor = $_POST['CorreoConductor'];
$FechaDesde = $_POST['FechaDesde'];
$FechaHasta = $_POST['FechaHasta'];
$FormaPago = $_POST['FormaPago'];
$mensaje = $_POST['mensaje'];

try {
    // Validar fechas
    $today = new DateTime();
    $fechaDesdeObj = new DateTime($FechaDesde);
    $fechaHastaObj = new DateTime($FechaHasta);

    if ($fechaDesdeObj < $today || $fechaHastaObj < $fechaDesdeObj) {
        throw new Exception("Las fechas seleccionadas no son válidas.");
    }

    // Validar disponibilidad del vehículo
    $stmt = $dbh->prepare("
        SELECT COUNT(*) AS count
        FROM tblbooking
        WHERE IdVehiculo = ? AND Estado = 1 AND Devuelto = 0 AND (
            (FechaDesde <= ? AND FechaHasta >= ?) OR
            (FechaDesde <= ? AND FechaHasta >= ?) OR
            (FechaDesde >= ? AND FechaHasta <= ?)
        )
    ");
    $stmt->execute([
        $IdVehiculo,
        $FechaDesde, $FechaDesde,
        $FechaHasta, $FechaHasta,
        $FechaDesde, $FechaHasta
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] > 0) {
        throw new Exception("El vehículo seleccionado no está disponible en las fechas indicadas.");
    }

    // Iniciar transacción
    $dbh->beginTransaction();

    // Generar un número de reserva aleatorio de 9 dígitos
    $NumeroReserva = rand(100000000, 999999999);

    // Calcular el número de días entre las fechas
    $interval = $fechaDesdeObj->diff($fechaHastaObj);
    $numDias = $interval->days;

    // Obtener el precio por día del vehículo
    $stmt = $dbh->prepare("SELECT PrecioPorDia FROM tblvehicles WHERE id = ?");
    $stmt->execute([$IdVehiculo]);
    $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vehiculo) {
        throw new Exception("Vehículo no encontrado.");
    }

    $PrecioPorDia = $vehiculo['PrecioPorDia'];
    $CostoTotal = $PrecioPorDia * $numDias;

    // Insertar o actualizar cliente
    $stmt = $dbh->prepare("
        INSERT INTO tblusers (NombreCompleto, CorreoElectronico, NumeroContacto, FechaNacimiento, Direccion, Ciudad, Pais)
        VALUES (?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            NombreCompleto = VALUES(NombreCompleto),
            NumeroContacto = VALUES(NumeroContacto),
            FechaNacimiento = VALUES(FechaNacimiento),
            Direccion = VALUES(Direccion),
            Ciudad = VALUES(Ciudad),
            Pais = VALUES(Pais)
    ");
    $stmt->execute([$NombreCompleto, $Correo, $NumeroContacto, $FechaNacimiento, $Direccion, $Ciudad, $Pais]);

    // Insertar datos del conductor
    $stmt = $dbh->prepare("
        INSERT INTO tblconductores (Nombre, Licencia, Telefono, Correo)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$NombreConductor, $Licencia, $Telefono, $CorreoConductor]);
    $IdConductor = $dbh->lastInsertId();

    // Insertar datos de la reserva
    $stmt = $dbh->prepare("
        INSERT INTO tblbooking (NumeroReserva, CorreoUsuario, IdVehiculo, IdConductor, FechaDesde, FechaHasta, FormaPago, mensaje, Estado, CostoTotal, Devuelto)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?, 0)
    ");
    $stmt->execute([$NumeroReserva, $Correo, $IdVehiculo, $IdConductor, $FechaDesde, $FechaHasta, $FormaPago, $mensaje, $CostoTotal]);
    $IdReserva = $dbh->lastInsertId();

    // Insertar registro de pago en tblpagos con estado "Pendiente"
    $stmt = $dbh->prepare("
        INSERT INTO tblpagos (IdReserva, MetodoPago, Monto, EstadoPago)
        VALUES (?, ?, ?, 'Pendiente')
    ");
    $stmt->execute([$IdReserva, $FormaPago, $CostoTotal]);

    // Confirmar transacción
    $dbh->commit();

    // Redirigir con éxito
    header('Location: alquileres.php?status=success');
    exit();
} catch (Exception $e) {
    // Verificar si hay una transacción activa
    if ($dbh->inTransaction()) {
        $dbh->rollBack();
    }

    // Redirigir con error
    header('Location: alquileres.php?status=error&message=' . urlencode($e->getMessage()));
    exit();
}
?>
