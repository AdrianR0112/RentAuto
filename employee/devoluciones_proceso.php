<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $IdReserva = $_POST['IdReserva'];
    $NumeroMotor = $_POST['NumeroMotor'];
    $NumeroChasis = $_POST['NumeroChasis'];
    $EstadoDanios = $_POST['EstadoDanios'];
    $RetrasoHoras = $_POST['RetrasoHoras'];
    $CargoRetraso = $_POST['CargoRetraso'];
    $CargoDanios = $_POST['CargoDanios'];
    $Observaciones = $_POST['Observaciones'];

    try {
        // Verificar si el número de motor y chasis coinciden
        $stmt = $dbh->prepare("
            SELECT b.FechaDesde, b.FechaHasta, v.PrecioPorDia, v.NumeroMotor, v.NumeroChasis
            FROM tblbooking b
            JOIN tblvehicles v ON b.IdVehiculo = v.id
            WHERE b.id = ? AND b.Estado = 1
        ");
        $stmt->execute([$IdReserva]);
        $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$vehiculo) {
            header('Location: devoluciones.php?error=La reserva no existe o no está activa.');
            exit();
        }

        if ($vehiculo['NumeroMotor'] !== $NumeroMotor || $vehiculo['NumeroChasis'] !== $NumeroChasis) {
            header('Location: devoluciones.php?error=El número de motor o chasis no coincide con el vehículo asociado.');
            exit();
        }

        // Calcular días realmente utilizados
        $fechaInicio = new DateTime($vehiculo['FechaDesde']);
        $fechaFin = new DateTime($vehiculo['FechaHasta']);
        $fechaActual = new DateTime(); // Fecha de devolución

        $diasOcupados = $fechaInicio->diff($fechaActual)->days;
        $diasTotales = $fechaInicio->diff($fechaFin)->days;

        // Calcular costo total
        $costoDiasOcupados = $diasOcupados * $vehiculo['PrecioPorDia'];
        $totalAdicional = $CargoRetraso + $CargoDanios;
        $costoTotal = $costoDiasOcupados + $totalAdicional;

        // Iniciar transacción
        $dbh->beginTransaction();

        // Insertar en la tabla de devoluciones
        $stmt = $dbh->prepare("
            INSERT INTO tbldevoluciones (IdReserva, NumeroMotor, NumeroChasis, EstadoDanios, RetrasoHoras, CargoRetraso, CargoDanios, Observaciones)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$IdReserva, $NumeroMotor, $NumeroChasis, $EstadoDanios, $RetrasoHoras, $CargoRetraso, $CargoDanios, $Observaciones]);

        // Actualizar la reserva a estado "Devolución"
        $stmt = $dbh->prepare("UPDATE tblbooking SET Estado = 3, CostoTotal = ? WHERE id = ?");
        $stmt->execute([$costoTotal, $IdReserva]);

        $dbh->commit();
        header('Location: devoluciones.php?success=Devolución registrada con éxito.');
        exit();
    } catch (PDOException $e) {
        $dbh->rollBack();
        header('Location: devoluciones.php?error=Error al registrar la devolución: ' . urlencode($e->getMessage()));
        exit();
    }
}
?>