<?php
session_start();
require_once '../includes/config.php';

function validarFechas($fechaDesde, $fechaHasta) {
    // Convertir fechas a objetos DateTime
    $fechaInicio = new DateTime($fechaDesde);
    $fechaFin = new DateTime($fechaHasta);
    $fechaHoy = new DateTime();

    // Validaciones
    if ($fechaInicio > $fechaFin) {
        throw new Exception("La fecha de inicio no puede ser posterior a la fecha de fin.");
    }

    if ($fechaInicio < $fechaHoy) {
        throw new Exception("La fecha de inicio no puede ser anterior a la fecha actual.");
    }

    // Límite máximo de reserva (por ejemplo, 30 días)
    $diferencia = $fechaInicio->diff($fechaFin)->days;
    if ($diferencia > 30) {
        throw new Exception("El periodo de alquiler no puede superar los 30 días.");
    }

    return true;
}

try {
    $dbh->beginTransaction();

    $tipoUsuario = $_POST['tipoUsuario'];
    $correoUsuario = '';

    // Validar fechas primero
    $fechaDesde = $_POST['FechaDesde'];
    $fechaHasta = $_POST['FechaHasta'];
    validarFechas($fechaDesde, $fechaHasta);

    if ($tipoUsuario === 'existente') {
        $correoUsuario = $_POST['CorreoExistente'];
        
        // Validar que el correo exista
        $stmt = $dbh->prepare("SELECT COUNT(*) FROM tblusers WHERE CorreoElectronico = :correo");
        $stmt->execute(['correo' => $correoUsuario]);
        if ($stmt->fetchColumn() == 0) {
            throw new Exception("El usuario seleccionado no existe.");
        }
    } else {
        // Registrar nuevo usuario
        $nombreCompleto = $_POST['NombreCompleto'];
        $correo = $_POST['Correo'];
        $numeroContacto = $_POST['NumeroContacto'];

        // Validaciones adicionales para nuevo usuario
        if (empty($nombreCompleto) || empty($correo) || empty($numeroContacto)) {
            throw new Exception("Todos los campos de usuario nuevo son obligatorios.");
        }

        // Validar formato de correo
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El formato del correo electrónico no es válido.");
        }

        // Verificar si el correo ya existe
        $stmt = $dbh->prepare("SELECT COUNT(*) FROM tblusers WHERE CorreoElectronico = :correo");
        $stmt->execute(['correo' => $correo]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("El correo electrónico ya está registrado.");
        }

        $stmt = $dbh->prepare("
            INSERT INTO tblusers 
            (NombreCompleto, CorreoElectronico, NumeroContacto) 
            VALUES (:nombre, :correo, :contacto)
        ");
        $stmt->execute([
            'nombre' => $nombreCompleto,
            'correo' => $correo,
            'contacto' => $numeroContacto
        ]);

        $correoUsuario = $correo;
    }

    // Verificar disponibilidad del vehículo
    $idVehiculo = $_POST['IdVehiculo'];
    $idConductor = $_POST['IdConductor'];

    // Usar función de verificación de disponibilidad
    if (!verificarDisponibilidadVehiculo($idVehiculo, $fechaDesde, $fechaHasta)) {
        throw new Exception("El vehículo no está disponible en las fechas seleccionadas.");
    }

    // Calcular precio
    $precioCalculado = calcularPrecioAlquiler($idVehiculo, $fechaDesde, $fechaHasta);

    // Generar número de reserva único
    $numeroReserva = 'RES-' . date('Ymd') . '-' . rand(1000, 9999);

    $stmt = $dbh->prepare("
        INSERT INTO tblbooking 
        (NumeroReserva, CorreoUsuario, IdVehiculo, IdConductor, FechaDesde, FechaHasta, CostoTotal, Estado, FechaPublicacion) 
        VALUES (:numReserva, :correo, :vehiculo, :conductor, :fechaDesde, :fechaHasta, :costoTotal, 1, NOW())
    ");
    $stmt->execute([
        'numReserva' => $numeroReserva,
        'correo' => $correoUsuario,
        'vehiculo' => $idVehiculo,
        'conductor' => $idConductor,
        'fechaDesde' => $fechaDesde,
        'fechaHasta' => $fechaHasta,
        'costoTotal' => $precioCalculado['precioFinal']
    ]);

    // Actualizar estado del vehículo
    $stmt = $dbh->prepare("UPDATE tblvehicles SET Estado = 'Reservado' WHERE id = :idVehiculo");
    $stmt->execute(['idVehiculo' => $idVehiculo]);

    $dbh->commit();

    // Redirigir con mensaje de éxito
    header("Location: alquileres.php?status=success&numReserva=" . $numeroReserva);
    exit();

} catch (Exception $e) {
    $dbh->rollBack();
    // Redirigir con mensaje de error
    header("Location: alquileres.php?status=error&message=" . urlencode($e->getMessage()));
    exit();
}
?>