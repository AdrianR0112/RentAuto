<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>ECUA CARS - Recuperación de Contraseña</title>

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Rubik&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">

    <!-- Librerías de Estilo -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Estilos Personalizados de Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilo de Plantilla -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php 
    session_start();
    require_once 'includes/config.php';
    error_reporting(0);

    if(isset($_POST['update'])) {
        $email = $_POST['email'];
        $mobile = $_POST['mobile'];
        $newpassword = password_hash($_POST['newpassword'], PASSWORD_BCRYPT);
        
        $sql = "SELECT id FROM tblusers WHERE CorreoElectronico=:email AND NumeroContacto=:mobile";
        $query = $dbh->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);
        
        if($query->rowCount() > 0) {
            $con = "UPDATE tblusers SET Contrasena=:newpassword WHERE CorreoElectronico=:email AND NumeroContacto=:mobile";
            $chngpwd1 = $dbh->prepare($con);
            $chngpwd1->bindParam(':email', $email, PDO::PARAM_STR);
            $chngpwd1->bindParam(':mobile', $mobile, PDO::PARAM_STR);
            $chngpwd1->bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
            $chngpwd1->execute();
            $successMsg = "Su contraseña cambió exitosamente";
        } else {
            $errorMsg = "La identificación de correo electrónico o el número de móvil no son válidos";
        }
    }
    ?>

    <!--Header-->
    <?php include('includes/header.php'); ?>
    <!-- /Header -->

    <!-- Page Header Start -->
    <div class="container-fluid page-header">
        <h1 class="display-3 text-uppercase text-white mb-3">Recuperación de Contraseña</h1>
    </div>
    <!-- Page Header Start -->

    <!-- Recovery Password Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div class="bg-secondary p-4 rounded">
                        <?php 
                        if(isset($successMsg)) {
                            echo "<div class='alert alert-success'>" . htmlspecialchars($successMsg) . "</div>";
                        }
                        if(isset($errorMsg)) {
                            echo "<div class='alert alert-danger'>" . htmlspecialchars($errorMsg) . "</div>";
                        }
                        ?>
                        <form action="" method="POST" name="chngpwd" onSubmit="return valid();">
                            <div class="mb-4">
                                <label class="text-light mb-2">Correo Electrónico</label>
                                <input type="email" name="email" class="form-control bg-light" placeholder="Su dirección de correo electrónico*" required>
                            </div>

                            <div class="mb-4">
                                <label class="text-light mb-2">Número de Móvil</label>
                                <input type="text" name="mobile" class="form-control bg-light" placeholder="Su reg. Móvil*" required>
                            </div>

                            <div class="mb-4">
                                <label class="text-light mb-2">Nueva Contraseña</label>
                                <input type="password" name="newpassword" class="form-control bg-light" placeholder="Contraseña nueva*" required>
                            </div>

                            <div class="mb-4">
                                <label class="text-light mb-2">Confirmar Contraseña</label>
                                <input type="password" name="confirmpassword" class="form-control bg-light" placeholder="Confirmar contraseña*" required>
                            </div>

                            <button type="submit" name="update" class="btn btn-primary py-2 px-4">
                                Restablecer Contraseña
                                <i class="fa fa-angle-right ml-2"></i>
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <p class="text-light">¿Recordaste tu contraseña? <a href="#loginModal" data-toggle="modal" data-target="#loginModal" class="text-primary">Iniciar Sesión</a></p>
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
        function valid() {
            var newPassword = document.getElementsByName('newpassword')[0].value;
            var confirmPassword = document.getElementsByName('confirmpassword')[0].value;

            if(newPassword !== confirmPassword) {
                alert("Los campos Nueva contraseña y Confirmar contraseña no coinciden !!");
                return false;
            }
            return true;
        }

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