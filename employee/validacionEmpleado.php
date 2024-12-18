<?php
// Incluimos el archivo de conexión a la base de datos
require_once '../includes/config.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreEmpleado = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    // Query to check if the user exists
    $sql = "SELECT * FROM tblempleados WHERE NombreEmpleado = :nombreEmpleado AND Contrasena = :contrasena";
    $query = $dbh->prepare($sql);
    $query->bindParam(':nombreEmpleado', $nombreEmpleado, PDO::PARAM_STR);
    $query->bindParam(':contrasena', $contrasena, PDO::PARAM_STR);
    $query->execute();

    // Check if a user was found
    if ($query->rowCount() > 0) {
        // Start a session and redirect to the employee panel
        session_start();
        $_SESSION['empleado_logged_in'] = true; // Cambié la clave de sesión a algo más descriptivo
        $_SESSION['NombreEmpleado'] = $nombreEmpleado;

        header("Location: dashboard.php"); // Redirige al panel de empleados
        exit();
    } else {
        $error_message = "Nombre de usuario o contraseña incorrectos.";
    }
}
?>