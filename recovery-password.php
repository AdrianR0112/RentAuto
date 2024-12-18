<?php
session_start();
error_reporting(0);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>ECUA CARS - Recuperación de Contraseña</title>

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

    <style>
        .alert-custom {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            width: 80%;
            max-width: 500px;
        }
    </style>
</head>

<body>
    <?php
    session_start();
    ?>

    <!--Header-->
    <?php include('includes/header.php'); ?>
    <!-- /Header -->

    <!-- Page Header Start -->
    <div class="container-fluid page-header">
        <h1 class="display-3 text-uppercase text-white mb-3">Recuperación de Contraseña</h1>
    </div>
    <!-- Page Header Start -->

    <!-- Message Alert -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert <?php
        echo ($_SESSION['message'] == 'success') ? 'alert-success' :
            (($_SESSION['message'] == 'error') ? 'alert-danger' : 'alert-warning');
        ?> alert-dismissible fade show alert-custom" role="alert">
            <?php echo htmlspecialchars($_SESSION['message_text']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php
        // Clear the message after displaying
        unset($_SESSION['message']);
        unset($_SESSION['message_text']);
    endif;
    ?>

    <!-- Recovery Password Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div class="bg-secondary p-4 rounded">
                        <form action="config/recovery.php" method="POST" name="recoverypassword"
                            onSubmit="return validateEmail();">
                            <div class="mb-4">
                                <label class="text-light mb-2">Correo Electrónico</label>
                                <div class="input-group">
                                    <input type="email" class="form-control bg-light" name="email" id="emailInput"
                                        placeholder="Ingrese su correo electrónico" required>
                                </div>
                            </div>

                            <button type="submit" name="recovery" class="btn btn-primary py-2 px-4">
                                Recuperar Contraseña
                                <i class="fa fa-angle-right ml-2"></i>
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <p class="text-light">¿Recordaste tu contraseña? <a href="#loginModal" data-toggle="modal"
                                    data-target="#loginModal" class="text-primary">Iniciar Sesión</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Recovery Password End -->

    <!--Footer-->
    <?php include('includes/footer.php'); ?>
    <!-- /Footer -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="fa fa-angle-double-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <script src="js/main.js"></script>
    <script>
        function validateEmail() {
            var email = document.getElementById('emailInput').value;
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailRegex.test(email)) {
                alert('Por favor, ingrese un correo electrónico válido');
                return false;
            }
            return true;
        }
    </script>
</body>

</html>