<?php
// logout.php
session_start(); // Iniciar sesión

// Destruir todas las variables de sesión
$_SESSION = [];

// Destruir la sesión
session_destroy();

// Redirigir al login con un mensaje
header("Location: login.php?status=loggedout");
exit();
?>