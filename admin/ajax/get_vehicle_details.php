<?php
session_start();
require_once '../../includes/config.php';

header('Content-Type: application/json');

// Verificar si el usuario es administrador
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

// Verificar que se recibió un ID de vehículo
if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de vehículo no proporcionado']);
    exit();
}

try {
    // Preparar la consulta para obtener los detalles del vehículo
    $stmt = $dbh->prepare("SELECT * FROM tblvehicles WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($vehicle) {
        // Convertir valores booleanos a enteros para checkboxes
        $vehicle['AireAcondicionado'] = (int)$vehicle['AireAcondicionado'];
        $vehicle['GPS'] = (int)$vehicle['GPS'];
        
        echo json_encode(['success' => true, 'vehicle' => $vehicle]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Vehículo no encontrado']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al recuperar los datos: ' . $e->getMessage()]);
}
?>