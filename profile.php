<?php
session_start();
error_reporting(0);
require_once 'includes/config.php';

// Verificar si el usuario está logueado
if(strlen($_SESSION['login'])==0)
  { 
header('location:index.php');
exit;
}
else{
// Procesar la actualización del perfil
if (isset($_POST['updateprofile'])) {
    $nombre = $_POST['nombrecompleto'];
    $telefono = $_POST['numerocontacto'];
    $fechanac = $_POST['fechanacimiento'];
    $direccion = $_POST['direccion'];
    $ciudad = $_POST['ciudad'];
    $pais = $_POST['pais'];
    $email = $_SESSION['login'];

    try {
        $sql = "UPDATE tblusers SET NombreCompleto=:nombre, NumeroContacto=:telefono, 
                FechaNacimiento=:fechanac, Direccion=:direccion, Ciudad=:ciudad, 
                Pais=:pais WHERE CorreoElectronico=:email";
        $query = $dbh->prepare($sql);
        $query->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $query->bindParam(':telefono', $telefono, PDO::PARAM_STR);
        $query->bindParam(':fechanac', $fechanac, PDO::PARAM_STR);
        $query->bindParam(':direccion', $direccion, PDO::PARAM_STR);
        $query->bindParam(':ciudad', $ciudad, PDO::PARAM_STR);
        $query->bindParam(':pais', $pais, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();

        $msg = "¡Perfil actualizado exitosamente!";
        // Actualizar el nombre en la sesión
        $_SESSION['fname'] = $nombre;
    } catch (PDOException $e) {
        $error = "Error al actualizar el perfil. Por favor, intente más tarde.";
        error_log("Error en actualización de perfil: " . $e->getMessage());
    }
}
}
// Obtener datos del usuario
$useremail = $_SESSION['login'];
$sql = "SELECT * FROM tblusers WHERE CorreoElectronico=:useremail";
$query = $dbh->prepare($sql);
$query->bindParam(':useremail', $useremail, PDO::PARAM_STR);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>ECUA CARS - Perfil de Usuario</title>

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
</head>

<body>
    <!-- Header -->
    <?php include('includes/header.php'); ?>

    <!-- Page Header Start -->
    <div class="container-fluid page-header">
        <h1 class="display-3 text-uppercase text-white mb-3">Configuración de Perfil</h1>
    </div>
    <!-- Page Header End -->

    <!-- Profile Settings Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
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

                        <form method="post" class="row">
                            <div class="col-12 mb-3">
                                <label class="text-light mb-2">Fecha de Registro</label>
                                <p class="form-control bg-light">
                                    <?php echo date('d/m/Y', strtotime($result->FechaRegistro)); ?></p>
                            </div>

                            <?php if ($result->FechaActualizacion) { ?>
                                <div class="col-12 mb-3">
                                    <label class="text-light mb-2">Última Actualización</label>
                                    <p class="form-control bg-light">
                                        <?php echo date('d/m/Y', strtotime($result->FechaActualizacion)); ?></p>
                                </div>
                            <?php } ?>

                            <div class="col-md-6 mb-3">
                                <label class="text-light mb-2">Nombre Completo</label>
                                <input type="text" class="form-control bg-light" name="nombrecompleto"
                                    value="<?php echo htmlentities($result->NombreCompleto); ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="text-light mb-2">Correo Electrónico</label>
                                <input type="email" class="form-control bg-light"
                                    value="<?php echo htmlentities($result->CorreoElectronico); ?>" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="text-light mb-2">Número de Teléfono</label>
                                <input type="text" class="form-control bg-light" name="numerocontacto"
                                    value="<?php echo htmlentities($result->NumeroContacto); ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="text-light mb-2">Fecha de Nacimiento</label>
                                <input type="text" class="form-control bg-light" name="fechanacimiento"
                                    value="<?php echo htmlentities($result->FechaNacimiento); ?>"
                                    placeholder="dd/mm/yyyy">
                            </div>

                            <div class="col-12 mb-3">
                                <label class="text-light mb-2">Dirección</label>
                                <textarea class="form-control bg-light" name="direccion"
                                    rows="3"><?php echo htmlentities($result->Direccion); ?></textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="text-light mb-2">Ciudad</label>
                                <input type="text" class="form-control bg-light" name="ciudad"
                                    value="<?php echo htmlentities($result->Ciudad); ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="text-light mb-2">País</label>
                                <input type="text" class="form-control bg-light" name="pais"
                                    value="<?php echo htmlentities($result->Pais); ?>">
                            </div>

                            <div class="col-12">
                                <button type="submit" name="updateprofile" class="btn btn-primary py-2 px-4">
                                    Guardar Cambios
                                    <i class="fa fa-angle-right ml-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Profile Settings End -->

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
</body>

</html>