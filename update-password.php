<?php
session_start();
error_reporting(0);
require_once 'includes/config.php';

// Verificar si el usuario está logueado
if (strlen($_SESSION['login']) == 0) {
    header('location:index.php');
    exit;
} else {
    // Procesar el cambio de contraseña
    if (isset($_POST['updatepassword'])) {
        $email = $_SESSION['login'];
        $currentpassword = $_POST['currentpassword'];
        $newpassword = $_POST['newpassword'];
        $confirmpassword = $_POST['confirmpassword'];

        try {
            // Primero, obtener el hash actual de la contraseña
            $sql = "SELECT Contrasena FROM tblusers WHERE CorreoElectronico=:email";
            $query = $dbh->prepare($sql);
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);

            if ($query->rowCount() > 0) {
                // Verificar la contraseña actual
                if (password_verify($currentpassword, $result->Contrasena)) {
                    // Verificar que las nuevas contraseñas coincidan
                    if ($newpassword == $confirmpassword) {
                        // Crear nuevo hash para la nueva contraseña
                        $newpasswordHash = password_hash($newpassword, PASSWORD_DEFAULT);

                        // Actualizar la contraseña en la base de datos
                        $sql = "UPDATE tblusers SET Contrasena=:newpassword WHERE CorreoElectronico=:email";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':newpassword', $newpasswordHash, PDO::PARAM_STR);
                        $query->bindParam(':email', $email, PDO::PARAM_STR);
                        $query->execute();

                        $msg = "¡Contraseña actualizada exitosamente!";
                    } else {
                        $error = "Las nuevas contraseñas no coinciden.";
                    }
                } else {
                    $error = "La contraseña actual es incorrecta.";
                }
            } else {
                $error = "Usuario no encontrado.";
            }
        } catch (PDOException $e) {
            $error = "Error al actualizar la contraseña. Por favor, intente más tarde.";
            error_log("Error en actualización de contraseña: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>ECUA CARS - Cambiar Contraseña</title>

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Rubik&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">

    <!-- Librerías de Estilo -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Estilos Personalizados de Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilo de Plantilla -->
    <link href="css/style.css" rel="stylesheet">

    <!-- JavaScript para validación de contraseña -->
    <script type="text/javascript">
        function validarContrasena() {
            if (document.changepassword.newpassword.value != document.changepassword.confirmpassword.value) {
                alert('Las nuevas contraseñas no coinciden');
                document.changepassword.confirmpassword.focus();
                return false;
            }
            return true;
        }
    </script>
    <style>
        .password-field {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }

        .password-toggle:hover {
            color: #333;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <?php include('includes/header.php'); ?>

    <!-- Page Header Start -->
    <div class="container-fluid page-header">
        <h1 class="display-3 text-uppercase text-white mb-3">Cambiar Contraseña</h1>
    </div>
    <!-- Page Header End -->

    <!-- Change Password Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div class="bg-secondary p-5 rounded">
                        <?php if (isset($msg)) { ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?php echo $msg; ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php } ?>
                        <?php if (isset($error)) { ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $error; ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php } ?>

                        <form method="post" name="changepassword" onSubmit="return validarContrasena();">
                            <div class="mb-4">
                                <label class="text-light mb-2">Contraseña Actual</label>
                                <div class="password-field">
                                    <input type="password" class="form-control bg-light" name="currentpassword"
                                        id="currentPassword" required>
                                    <span class="password-toggle" onclick="togglePasswordVisibility('currentPassword')">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="text-light mb-2">Nueva Contraseña</label>
                                <div class="password-field">
                                    <input type="password" class="form-control bg-light" name="newpassword"
                                        id="newPassword" required>
                                    <span class="password-toggle" onclick="togglePasswordVisibility('newPassword')">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="text-light mb-2">Confirmar Nueva Contraseña</label>
                                <div class="password-field">
                                    <input type="password" class="form-control bg-light" name="confirmpassword"
                                        id="confirmPassword" required>
                                    <span class="password-toggle" onclick="togglePasswordVisibility('confirmPassword')">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>

                            <button type="submit" name="updatepassword" class="btn btn-primary py-2 px-4">
                                Actualizar Contraseña
                                <i class="fa fa-angle-right ml-2"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Change Password End -->

    <!-- Footer -->
    <?php include('includes/footer.php'); ?>

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top">
        <i class="fa fa-angle-double-up"></i>
    </a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    <script>
        function togglePasswordVisibility(inputId) {
            const passwordInput = document.getElementById(inputId);
            const passwordToggle = passwordInput.parentElement.querySelector('.password-toggle i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.classList.remove('fa-eye');
                passwordToggle.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordToggle.classList.remove('fa-eye-slash');
                passwordToggle.classList.add('fa-eye');
            }
        }

        function validarContrasena() {
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (newPassword !== confirmPassword) {
                alert('Las nuevas contraseñas no coinciden');
                document.getElementById('confirmPassword').focus();
                return false;
            }
            return true;
        }
    </script>
</body>

</html>