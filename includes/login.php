<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Consulta para obtener el usuario y su hash de contraseña
    $query = $conn->prepare("SELECT * FROM tblusers WHERE CorreoElectronico = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verifica si la contraseña coincide con el hash almacenado
        if (password_verify($password, $user['Contrasena'])) {
            // Usuario válido
            $_SESSION['login'] = $email;
            header("Location: index.php");
            exit();
        } else {
            $error = "Correo o contraseña incorrectos";
        }
    } else {
        $error = "Correo o contraseña incorrectos";
    }

    if (isset($error)) {
        echo "<script>alert('$error');</script>";
        echo "<script>window.location.href='index.php';</script>";
    }
}
?>