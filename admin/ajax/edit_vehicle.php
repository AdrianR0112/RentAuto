<?php
session_start();
require_once '../../includes/config.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

// Verificar que se recibió un ID de vehículo
if (!isset($_POST['id']) || empty($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de vehículo no proporcionado']);
    exit();
}

try {
    // Preparar la consulta de actualización
    $stmt = $dbh->prepare("UPDATE tblvehicles 
        SET 
        MarcaVehiculo = :marca, 
        IdModelo = :modelo, 
        PrecioPorDia = :precio, 
        TipoCombustible = :combustible, 
        NumeroMotor = :numero_motor, 
        TipoMotor = :tipo_motor, 
        PotenciaMotor = :potencia_motor, 
        FabricanteMotor = :fabricante_motor,
        Categoria = :categoria,
        Transmision = :transmision,
        AnoModelo = :ano_modelo,
        CapacidadAsientos = :capacidad_asientos,
        NumeroChasis = :numero_chasis,
        TipoChasis = :tipo_chasis,
        MaterialChasis = :material_chasis,
        FabricanteChasis = :fabricante_chasis,
        DescripcionVehiculo = :descripcion,
        Estado = :estado,
        AireAcondicionado = :aire_acondicionado,
        GPS = :gps
        WHERE id = :id");

    // Generar título del vehículo
    $titulo = $_POST['MarcaVehiculo'] . ' ' . $_POST['IdModelo'];

    // Bindear parámetros
    $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
    $stmt->bindParam(':marca', $_POST['MarcaVehiculo'], PDO::PARAM_INT);
    $stmt->bindParam(':modelo', $_POST['IdModelo'], PDO::PARAM_INT);
    $stmt->bindParam(':precio', $_POST['PrecioPorDia'], PDO::PARAM_STR);
    $stmt->bindParam(':combustible', $_POST['TipoCombustible'], PDO::PARAM_STR);
    $stmt->bindParam(':numero_motor', $_POST['NumeroMotor'], PDO::PARAM_STR);
    $stmt->bindParam(':tipo_motor', $_POST['TipoMotor'], PDO::PARAM_STR);
    $stmt->bindParam(':potencia_motor', $_POST['PotenciaMotor'], PDO::PARAM_INT);
    $stmt->bindParam(':fabricante_motor', $_POST['FabricanteMotor'], PDO::PARAM_STR);
    $stmt->bindParam(':categoria', $_POST['Categoria'], PDO::PARAM_STR);
    $stmt->bindParam(':transmision', $_POST['Transmision'], PDO::PARAM_STR);
    $stmt->bindParam(':ano_modelo', $_POST['AnoModelo'], PDO::PARAM_INT);
    $stmt->bindParam(':capacidad_asientos', $_POST['CapacidadAsientos'], PDO::PARAM_INT);
    $stmt->bindParam(':numero_chasis', $_POST['NumeroChasis'], PDO::PARAM_STR);
    $stmt->bindParam(':tipo_chasis', $_POST['TipoChasis'], PDO::PARAM_STR);
    $stmt->bindParam(':material_chasis', $_POST['MaterialChasis'], PDO::PARAM_STR);
    $stmt->bindParam(':fabricante_chasis', $_POST['FabricanteChasis'], PDO::PARAM_STR);
    $stmt->bindParam(':descripcion', $_POST['DescripcionVehiculo'], PDO::PARAM_STR);
    $stmt->bindParam(':estado', $_POST['Estado'], PDO::PARAM_STR);
    
    // Manejar checkboxes
    $aireAcondicionado = isset($_POST['AireAcondicionado']) ? 1 : 0;
    $gps = isset($_POST['GPS']) ? 1 : 0;
    
    $stmt->bindParam(':aire_acondicionado', $aireAcondicionado, PDO::PARAM_INT);
    $stmt->bindParam(':gps', $gps, PDO::PARAM_INT);

    // Ejecutar actualización
    $resultado = $stmt->execute();

    // Manejar actualización de imagen si se proporciona
    if ($resultado && isset($_FILES['Imagen1']) && $_FILES['Imagen1']['error'] == 0) {
        $uploadDir = '../../uploads/vehicles/';
        $fileName = uniqid() . '_' . basename($_FILES['Imagen1']['name']);
        $uploadPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['Imagen1']['tmp_name'], $uploadPath)) {
            // Actualizar la ruta de la imagen en la base de datos
            $updateImageStmt = $dbh->prepare("UPDATE tblvehicles SET Imagen1 = :imagen WHERE id = :id");
            $updateImageStmt->bindParam(':imagen', $fileName, PDO::PARAM_STR);
            $updateImageStmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
            $updateImageStmt->execute();
        }
    }

    // Respuesta de éxito
    echo json_encode(['success' => true, 'message' => 'Vehículo actualizado correctamente']);

} catch (PDOException $e) {
    // Manejar errores de base de datos
    echo json_encode([
        'success' => false, 
        'message' => 'Error al actualizar: ' . $e->getMessage()
    ]);
}
?>