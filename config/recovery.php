<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

session_start(); // Start the session to store messages

require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';
require_once('../includes/config.php');

$email = $_POST['email'];
$query = "SELECT * FROM tblusers WHERE CorreoElectronico = :email";
$stmt = $dbh->prepare($query);
$stmt->execute(['email' => $email]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($stmt->rowCount() > 0) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'adrianestefanoromero@gmail.com';
        $mail->Password = 'viujacxjftpfxrdo';
        $mail->Port = 587;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        $mail->setFrom('rent.auto@outlook.com', 'RentAuto');
        $mail->addAddress($email, $row['NombreCompleto']);
        $mail->isHTML(true);
        $mail->Subject = 'Recuperacion de contrasena';
        $mail->Body = 'Hola ' . htmlspecialchars($row['NombreCompleto']) . ', 
        este es un correo generado para solicitar tu recuperación de contraseña, 
        por favor, visita la página de <a href="localhost/rentauto/change-password.php?id=' . $row['id'] . '">Recuperación de contraseña</a>';

        $mail->send();
        
        // Set success message
        $_SESSION['message'] = 'success';
        $_SESSION['message_text'] = 'Se ha enviado un correo de recuperación de contraseña. Por favor revisa tu bandeja de entrada.';
    } catch (Exception $e) {
        // Set error message
        $_SESSION['message'] = 'error';
        $_SESSION['message_text'] = 'No se pudo enviar el correo. Error: ' . $mail->ErrorInfo;
    }
} else {
    // Set not found message
    $_SESSION['message'] = 'not_found';
    $_SESSION['message_text'] = 'No se encontró ninguna cuenta asociada a este correo electrónico.';
}

// Redirect back to the recovery page
header("Location: ../recovery-password.php");
exit();
?>