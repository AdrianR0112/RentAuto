<?php
session_start();
require_once '../includes/config.php';
error_reporting(0);
function calcularPrecioAlquiler($idVehiculo, $fechaDesde, $fechaHasta) {
    global $dbh;

    $stmt = $dbh->prepare("SELECT PrecioPorDia FROM tblvehicles WHERE id = :id");
    $stmt->execute(['id' => $idVehiculo]);
    $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);
    $precioPorDia = $vehiculo['PrecioPorDia'];

    $fechaInicio = new DateTime($fechaDesde);
    $fechaFin = new DateTime($fechaHasta);
    $diasAlquiler = $fechaInicio->diff($fechaFin)->days + 1;

    $precioBase = $precioPorDia * $diasAlquiler;

    $descuento = 0;
    if ($diasAlquiler > 7) {
        $descuento = $precioBase * 0.10;
    }

    $precioFinal = $precioBase - $descuento;

    return [
        'diasAlquiler' => $diasAlquiler,
        'precioPorDia' => $precioPorDia,
        'precioBase' => $precioBase,
        'descuento' => $descuento,
        'precioFinal' => $precioFinal
    ];
}

function verificarDisponibilidadVehiculo($idVehiculo, $fechaDesde, $fechaHasta) {
    global $dbh;

    $stmt = $dbh->prepare("SELECT Estado FROM tblvehicles WHERE id = :id");
    $stmt->execute(['id' => $idVehiculo]);
    $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($vehiculo['Estado'] !== 'Disponible') {
        return false;
    }

    $stmt = $dbh->prepare("
        SELECT COUNT(*) as conflictos 
        FROM tblbooking 
        WHERE IdVehiculo = :idVehiculo 
        AND (
            (FechaDesde BETWEEN :fechaDesde AND :fechaHasta) 
            OR (FechaHasta BETWEEN :fechaDesde AND :fechaHasta)
            OR (:fechaDesde BETWEEN FechaDesde AND FechaHasta)
        )
    ");
    $stmt->execute([
        'idVehiculo' => $idVehiculo,
        'fechaDesde' => $fechaDesde,
        'fechaHasta' => $fechaHasta
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['conflictos'] == 0;
}

function calcularMultaRetraso($idReserva) {
    global $dbh;

    $stmt = $dbh->prepare("
        SELECT FechaHasta, IdVehiculo 
        FROM tblbooking 
        WHERE id = :id
    ");
    $stmt->execute(['id' => $idReserva]);
    $reserva = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $dbh->prepare("
        SELECT FechaDevolucion, 
               TIMESTAMPDIFF(HOUR, :fechaEsperada, FechaDevolucion) as horasRetraso
        FROM tbldevoluciones 
        WHERE IdReserva = :idReserva
    ");
    $stmt->execute([
        'fechaEsperada' => $reserva['FechaHasta'],
        'idReserva' => $idReserva
    ]);
    $devolucion = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $dbh->prepare("SELECT PrecioPorDia FROM tblvehicles WHERE id = :id");
    $stmt->execute(['id' => $reserva['IdVehiculo']]);
    $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);

    $horasRetraso = $devolucion['horasRetraso'];
    $diasRetraso = ceil($horasRetraso / 24);
    $multaRetraso = $vehiculo['PrecioPorDia'] * 1.5 * $diasRetraso;

    return [
        'horasRetraso' => $horasRetraso,
        'diasRetraso' => $diasRetraso,
        'multaRetraso' => $multaRetraso
    ];
}

function registrarMantenimiento($idVehiculo, $descripcion, $fechaMantenimiento, $costo) {
    global $dbh;

    $stmt = $dbh->prepare("
        INSERT INTO tblmantenimiento 
        (IdVehiculo, Descripcion, FechaMantenimiento, Costo) 
        VALUES (:idVehiculo, :descripcion, :fechaMantenimiento, :costo)
    ");
    $stmt->execute([
        'idVehiculo' => $idVehiculo,
        'descripcion' => $descripcion,
        'fechaMantenimiento' => $fechaMantenimiento,
        'costo' => $costo
    ]);

    $stmt = $dbh->prepare("
        UPDATE tblvehicles 
        SET Estado = 'En mantenimiento' 
        WHERE id = :idVehiculo
    ");
    $stmt->execute(['idVehiculo' => $idVehiculo]);
}

function generarContrato($idReserva) {
    global $dbh;

    $stmt = $dbh->prepare("
        SELECT b.*, 
               v.TituloVehiculo, 
               c.Nombre as NombreConductor,
               u.NombreCompleto as NombreUsuario
        FROM tblbooking b
        JOIN tblvehicles v ON b.IdVehiculo = v.id
        JOIN tblconductores c ON b.IdConductor = c.id
        JOIN tblusers u ON b.CorreoUsuario = u.CorreoElectronico
        WHERE b.id = :id
    ");
    $stmt->execute(['id' => $idReserva]);
    $contrato = $stmt->fetch(PDO::FETCH_ASSOC);

    $contenidoContrato = "
    CONTRATO DE ALQUILER DE VEHÍCULO

    Fecha de Reserva: {$contrato['FechaPublicacion']}
    Número de Reserva: {$contrato['NumeroReserva']}

    ARRENDADOR: ECUA CARS

    ARRENDATARIO: {$contrato['NombreUsuario']}

    CONDUCTOR: {$contrato['NombreConductor']}

    VEHÍCULO: {$contrato['TituloVehiculo']}

    Fecha de Inicio: {$contrato['FechaDesde']}
    Fecha de Finalización: {$contrato['FechaHasta']}

    Costo Total: \${$contrato['CostoTotal']}

    TÉRMINOS Y CONDICIONES
    1. El vehículo debe ser devuelto en las mismas condiciones.
    2. Cualquier daño será responsabilidad del arrendatario.
    3. Los retrasos generarán multas adicionales.

    Firma Arrendatario: ___________________
    Firma Arrendador: ____________________
    ";

    return $contenidoContrato;
}
?>