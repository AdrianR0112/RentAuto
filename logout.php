<?php
session_start();
require_once 'includes/config.php';


// Destruir todas las variables de sesión
$_SESSION = array();

// Destruir la cookie de sesión si existe
if(isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-400, '/');
}

// Destruir la sesión
session_destroy();

// Redirigir al inicio con un mensaje de éxito
echo "<script>alert('Has cerrado sesión exitosamente.');</script>";
echo "<script type='text/javascript'> document.location = 'index.php'; </script>";
exit();
?>