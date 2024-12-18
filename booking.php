<?php
session_start();
require_once 'includes/config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit();
}

// Procesar datos de reserva
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $vehicle_id = intval($_POST['vehicle_id']);
        $pickup_location = $_POST['pickup_location'];
        $return_location = $_POST['return_location'];
        $pickup_date = $_POST['pickup_date'];
        $pickup_time = $_POST['pickup_time'];
        $persons = intval($_POST['persons']);

        // Aquí implementa la lógica de reserva
        // 1. Verificar disponibilidad del vehículo
        // 2. Calcular costo
        // 3. Insertar reserva en la base de datos

        // Ejemplo básico de inserción (ajusta según tus necesidades)
        $sql = "INSERT INTO tblbooking (
            IdVehiculo, 
            CorreoUsuario, 
            FechaDesde, 
            Estado
        ) VALUES (
            :vehicle_id, 
            :email, 
            :pickup_date, 
            1
        )";

        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':vehicle_id', $vehicle_id);
        $stmt->bindParam(':email', $_SESSION['login']);
        $stmt->bindParam(':pickup_date', $pickup_date);
        $stmt->execute();

        // Redirigir a confirmación de reserva
        $_SESSION['booking_success'] = "Reserva realizada con éxito";
        header("Location: confirmation.php");
        exit();

    } catch (PDOException $e) {
        // Manejar errores
        $_SESSION['booking_error'] = "Error al procesar la reserva: " . $e->getMessage();
        header("Location: detail.php?id=" . $vehicle_id);
        exit();
    }
} else {
    // Acceso directo no permitido
    header("Location: index.php");
    exit();
}
?>