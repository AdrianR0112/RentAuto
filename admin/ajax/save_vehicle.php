<?php
// ajax/save_vehicle.php
require_once '../../includes/config.php';

header('Content-Type: application/json');

try {
    // Procesar la imagen
    $targetDir = "../../img/cars/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $fileName = basename($_FILES["Imagen1"]["name"]);
    $targetFile = $targetDir . time() . '_' . $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Verificar si es una imagen real
    if(getimagesize($_FILES["Imagen1"]["tmp_name"]) === false) {
        throw new Exception("El archivo no es una imagen válida.");
    }
    
    // Verificar tamaño del archivo (max 5MB)
    if ($_FILES["Imagen1"]["size"] > 5000000) {
        throw new Exception("El archivo es demasiado grande.");
    }
    
    // Permitir ciertos formatos de archivo
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        throw new Exception("Solo se permiten archivos JPG, JPEG & PNG.");
    }
    
    if(move_uploaded_file($_FILES["Imagen1"]["tmp_name"], $targetFile)) {
        $stmt = $dbh->prepare("
            INSERT INTO tblvehicles (
                TituloVehiculo, MarcaVehiculo, DescripcionVehiculo, PrecioPorDia,
                TipoCombustible, Categoria, Transmision, AnoModelo,
                CapacidadAsientos, Imagen1, AireAcondicionado, GPS
            ) VALUES (
                :titulo, :marca, :descripcion, :precio,
                :combustible, :categoria, :transmision, :modelo,
                :asientos, :imagen, :ac, :gps
            )
        ");
        
        $params = [
            ':titulo' => $_POST['TituloVehiculo'],
            ':marca' => $_POST['MarcaVehiculo'],
            ':descripcion' => $_POST['DescripcionVehiculo'],
            ':precio' => $_POST['PrecioPorDia'],
            ':combustible' => $_POST['TipoCombustible'],
            ':categoria' => $_POST['Categoria'],
            ':transmision' => $_POST['Transmision'],
            ':modelo' => $_POST['AnoModelo'],
            ':asientos' => $_POST['CapacidadAsientos'],
            ':imagen' => time() . '_' . $fileName,
            ':ac' => isset($_POST['AireAcondicionado']) ? 1 : 0,
            ':gps' => isset($_POST['GPS']) ? 1 : 0
        ];
        
        $stmt->execute($params);
        
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Error al subir la imagen.");
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>