<?php
require_once '../../includes/config.php';
require_once 'funciones.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $idReserva = filter_input(INPUT_POST, 'IdReserva', FILTER_VALIDATE_INT);
        $estadoDanios = $_POST['EstadoDanios'];

        // Registrar devolución
        $stmt = $dbh->prepare("
            INSERT INTO tbldevoluciones 
            (IdReserva, EstadoDanios, RetrasoHoras, CargoRetraso, CargoDanios) 
            VALUES 
            (:idReserva, :estadoDanios, :retrasoHoras, :cargoRetraso, :cargoDanios)
        ");

        // Calcular multas
        $multaRetraso = calcularMultaRetraso($idReserva);

        // Calcular daños (por implementar según necesidades específicas)
        $cargoDanios = 0;
        if (!empty($estadoDanios)) {
            $cargoDanios = 500; // Ejemplo de cargo por daños
        }

        $stmt->execute([
            'idReserva' => $idReserva,
            'estadoDanios' => $estadoDanios,
            'retrasoHoras' => $multaRetraso['horasRetraso'],
            'cargoRetraso' => $multaRetraso['multaRetraso'],
            'cargoDanios' => $cargoDanios
        ]);

        // Actualizar estado del vehículo
        $stmt = $dbh->prepare("
            UPDATE tblvehicles v
            JOIN tblbooking b ON b.IdVehiculo = v.id
            SET v.Estado = 'Disponible'
            WHERE b.id = :idReserva
        ");
        $stmt->execute(['idReserva' => $idReserva]);

        header("Location: gestion_alquileres.php?status=devolucion_success");
        exit();

    } catch (Exception $e) {
        header("Location: gestion_alquileres.php?status=devolucion_error&message=" . urlencode($e->getMessage()));
        exit();
    }
}
?>