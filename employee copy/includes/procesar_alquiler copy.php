<?php
require_once 'config.php';
require_once 'funciones.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Validación de datos de entrada
        $idVehiculo = filter_input(INPUT_POST, 'IdVehiculo', FILTER_VALIDATE_INT);
        $fechaDesde = $_POST['FechaDesde'];
        $fechaHasta = $_POST['FechaHasta'];
        $correoUsuario = filter_input(INPUT_POST, 'Correo', FILTER_VALIDATE_EMAIL);
        $idConductor = filter_input(INPUT_POST, 'IdConductor', FILTER_VALIDATE_INT);

        // Verificar disponibilidad
        if (!verificarDisponibilidadVehiculo($idVehiculo, $fechaDesde, $fechaHasta)) {
            throw new Exception("El vehículo no está disponible para las fechas seleccionadas.");
        }

        // Calcular precio
        $precioCalculo = calcularPrecioAlquiler($idVehiculo, $fechaDesde, $fechaHasta);

        // Generar número de reserva
        $numeroReserva = time(); // Timestamp como número de reserva

        // Insertar reserva
        $stmt = $dbh->prepare("
            INSERT INTO tblbooking 
            (NumeroReserva, CorreoUsuario, IdVehiculo, IdConductor, 
             FechaDesde, FechaHasta, CostoTotal, Estado) 
            VALUES 
            (:numReserva, :correo, :idVehiculo, :idConductor, 
             :fechaDesde, :fechaHasta, :costoTotal, 1)
        ");

        $stmt->execute([
            'numReserva' => $numeroReserva,
            'correo' => $correoUsuario,
            'idVehiculo' => $idVehiculo,
            'idConductor' => $idConductor,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'costoTotal' => $precioCalculo['precioFinal']
        ]);

        // Actualizar estado del vehículo
        $stmt = $dbh->prepare("
            UPDATE tblvehicles 
            SET Estado = 'Reservado' 
            WHERE id = :idVehiculo
        ");
        $stmt->execute(['idVehiculo' => $idVehiculo]);

        // Generar contrato
        $contratoContenido = generarContrato($dbh->lastInsertId());

        // Redirigir con éxito
        header("Location: gestion_alquileres.php?status=success&numReserva=" . $numeroReserva);
        exit();

    } catch (Exception $e) {
        // Manejar errores
        header("Location: gestion_alquileres.php?status=error&message=" . urlencode($e->getMessage()));
        exit();
    }
}
?>