<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

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
    $mail->Host = 'smtp-mail.outlook.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'rent.auto@outlook.com';
    $mail->Password = 'Rent.2024';
    $mail->Port = 587;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

    $mail->setFrom('rent.auto@outlook.com', 'RentAuto');
    $mail->addAddress($email, $row['NombreCompleto']);
    $mail->isHTML(true);
    $mail->Subject = 'Recuperación de contraseña';
    $mail->Body = 'Hola ' . htmlspecialchars($row['NombreCompleto']) . ', 
    este es un correo generado para solicitar tu recuperación de contraseña, 
    por favor, visita la página de <a href="localhost/rentauto/config/change_password.php?id=' . $row['id'] . '">Recuperación de contraseña</a>';

    $mail->send();
    header("Location: ../index.php?message=ok");
  } catch (Exception $e) {
    header("Location: ../index.php?message=error");
  }

} else {
  header("Location: ../index.php?message=not_found");
}
?>