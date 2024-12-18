<?php 
// Verificar si las constantes ya están definidas
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}
if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', 'wilson123');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'rentauto');
}

// Establecer la conexión a la base de datos
global $dbh; // Variable global para la instancia de PDO
if (!isset($dbh)) { // Si aún no se ha creado la conexión
    try {
        $dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Manejo de errores
    } catch (PDOException $e) {
        exit("Error: " . $e->getMessage());
    }
}
?>