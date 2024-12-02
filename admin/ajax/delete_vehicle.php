// ajax/delete_vehicle.php
<?php
session_start();
require_once '../../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    try {
        // Primero obtenemos la información de la imagen
        $stmt = $dbh->prepare("SELECT Imagen1 FROM tblvehicles WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

        // Eliminamos la imagen si existe
        if ($vehicle && $vehicle['Imagen1']) {
            $imagePath = '../../img/cars/' . $vehicle['Imagen1'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Eliminamos el registro de la base de datos
        $stmt = $dbh->prepare("DELETE FROM tblvehicles WHERE id = ?");
        $stmt->execute([$_POST['id']]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>