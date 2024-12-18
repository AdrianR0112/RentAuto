<?php
session_start();
require_once 'includes/config.php';
require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function generateVerificationCode($length = 6)
{
    return sprintf("%0{$length}d", mt_rand(1, 999999));
}

function sendVerificationEmail($email, $verificationCode)
{
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
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Código de Verificación';
        $mail->Body = "Su código de verificación es: <strong>$verificationCode</strong>. 
                       Este código expirará en 10 minutos.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error enviando correo de verificación: " . $e->getMessage());
        return false;
    }
}

// Manejar la verificación de correo
if (isset($_POST['verify_email'])) {
    $inputCode = $_POST['verification_code'];
    $email = $_SESSION['email_to_verify'];

    // Verificar vigencia del código
    if (
        isset($_SESSION['verification_code']) &&
        $inputCode === $_SESSION['verification_code'] &&
        isset($_SESSION['verification_timestamp']) &&
        (time() - $_SESSION['verification_timestamp']) <= 600 // 10 minutos
    ) {
        try {
            // Actualizar usuario como verificado
            $updateSql = "UPDATE tblusers SET email_verificado = 1 WHERE CorreoElectronico = :email";
            $updateQuery = $dbh->prepare($updateSql);
            $updateQuery->execute([':email' => $email]);

            // Limpiar sesiones
            unset($_SESSION['verification_code']);
            unset($_SESSION['verification_timestamp']);
            unset($_SESSION['email_to_verify']);

            // Redirigir a página de éxito
            header("Location: index.php?verificacion=exitosa");
            exit();
        } catch (PDOException $e) {
            error_log("Error verificando email: " . $e->getMessage());
            $verificationError = "Hubo un problema verificando su correo. Intente nuevamente.";
        }
    } else {
        $verificationError = "Código de verificación inválido o expirado.";
    }
}

// Cuando se complete el registro, añadir esto al script de registro
if (isset($_POST['register'])) {
    // ... código de registro existente ...

    // Si el registro es exitoso
    if ($query->rowCount() > 0) {
        // Generar código de verificación
        $verificationCode = generateVerificationCode();

        // Enviar correo de verificación
        if (sendVerificationEmail($correoElectronico, $verificationCode)) {
            // Guardar información en sesión
            $_SESSION['verification_code'] = $verificationCode;
            $_SESSION['verification_timestamp'] = time();
            $_SESSION['email_to_verify'] = $correoElectronico;

            // Ir a modal de verificación
            header("Location: verify-email.php");
            exit();
        } else {
            // Manejar error de envío de correo
            $registroError = "No se pudo enviar el correo de verificación. Intente nuevamente.";
        }
    }
}
?>

<!-- Modal de Verificación de Correo -->
<div class="modal show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="background-color: #F4F5F8; border-radius: 8px;">
            <div class="modal-header border-0">
                <h3 class="modal-title mx-auto text-secondary">Verificación de Correo</h3>
            </div>
            <div class="modal-body text-center">
                <?php if (isset($verificationError)): ?>
                    <div class="alert alert-danger"><?php echo $verificationError; ?></div>
                <?php endif; ?>

                <p>Hemos enviado un código de verificación a <?php echo $_SESSION['email_to_verify']; ?></p>

                <form method="post" action="">
                    <div class="form-group">
                        <input type="text" name="verification_code" class="form-control text-center"
                            placeholder="Ingrese el código de 6 dígitos" maxlength="6" required>
                    </div>

                    <div class="form-group">
                        <p class="text-muted">
                            El código expirará en 10 minutos.
                            Si no lo recibe, puede solicitar un nuevo código.
                        </p>
                    </div>

                    <button type="submit" name="verify_email" class="btn btn-primary btn-block"
                        style="background-color: #F77D0A; border: none;">
                        Verificar Código
                    </button>
                </form>

                <div class="mt-3">
                    <a href="#" class="text-muted">Reenviar código</a>
                </div>
            </div>
        </div>
    </div>
</div>