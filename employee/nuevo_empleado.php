<?php
// Archivo de inserción provisional de empleados
require_once '../includes/config.php';

// Función para insertar empleado con contraseña hasheada
function insertarEmpleado($nombreEmpleado, $contrasena, $correo) {
    global $dbh;
    
    try {
        // Hashear la contraseña
        $contrasenaHash = password_hash($contrasena, PASSWORD_DEFAULT);
        
        // Preparar consulta SQL
        $stmt = $dbh->prepare("INSERT INTO tblempleados (NombreEmpleado, Contrasena, Correo) VALUES (:nombre, :contrasena, :correo)");
        
        // Bindear parámetros
        $stmt->bindParam(':nombre', $nombreEmpleado, PDO::PARAM_STR);
        $stmt->bindParam(':contrasena', $contrasenaHash, PDO::PARAM_STR);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        
        // Ejecutar la inserción
        $stmt->execute();
        
        return true;
    } catch (PDOException $e) {
        // Manejar errores
        error_log("Error al insertar empleado: " . $e->getMessage());
        return false;
    }
}

// Ejemplo de uso (comentar/descomentar según sea necesario)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombreEmpleado = trim($_POST['nombreEmpleado']);
    $contrasena = trim($_POST['contrasena']);
    $correo = trim($_POST['correo']);
    
    // Validaciones básicas
    if (empty($nombreEmpleado) || empty($contrasena) || empty($correo)) {
        echo "Todos los campos son obligatorios.";
        exit;
    }
    
    // Validar formato de correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo "Formato de correo electrónico inválido.";
        exit;
    }
    
    // Intentar insertar empleado
    if (insertarEmpleado($nombreEmpleado, $contrasena, $correo)) {
        echo "Empleado insertado exitosamente.";
    } else {
        echo "Error al insertar el empleado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inserción de Empleados</title>
</head>
<body>
    <h2>Insertar Nuevo Empleado</h2>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div>
            <label for="nombreEmpleado">Nombre del Empleado:</label>
            <input type="text" id="nombreEmpleado" name="nombreEmpleado" required>
        </div>
        <div>
            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>
        </div>
        <div>
            <label for="correo">Correo Electrónico:</label>
            <input type="email" id="correo" name="correo" required>
        </div>
        <div>
            <button type="submit">Insertar Empleado</button>
        </div>
    </form>
</body>
</html>