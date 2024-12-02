<?php
session_start();
require_once 'includes/config.php';

// Función para manejar el login
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password']; // Obtenemos la contraseña sin hashear

    try {
        // Primero buscamos al usuario por email
        $sql = "SELECT * FROM tblusers WHERE CorreoElectronico = :email";
        $query = $dbh->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();
        $results = $query->fetch(PDO::FETCH_OBJ);

        // Verificamos si el usuario existe y la contraseña es correcta
        if ($query->rowCount() > 0 && password_verify($password, $results->Contrasena)) {
            // Credenciales válidas, iniciamos sesión
            $_SESSION['login'] = $email;
            $_SESSION['fname'] = $results->NombreCompleto;
            $_SESSION['id'] = $results->id;

            // Opcional: Actualizar hash si se necesita
            if (password_needs_rehash($results->Contrasena, PASSWORD_DEFAULT)) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $updateSql = "UPDATE tblusers SET Contrasena = :newHash WHERE id = :id";
                $updateQuery = $dbh->prepare($updateSql);
                $updateQuery->execute([
                    ':newHash' => $newHash,
                    ':id' => $results->id
                ]);
            }

            header("location: index.php");
            exit();
        } else {
            echo "<script>alert('Email o contraseña incorrectos');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error en el sistema. Por favor intente más tarde.');</script>";
        error_log("Error en login: " . $e->getMessage());
    }
}

// Función para verificar si el usuario está logueado
function isLoggedIn()
{
    return isset($_SESSION['login']) && !empty($_SESSION['login']);
}
?>
<!-- Inicio de la Barra Superior -->
<div class="container-fluid bg-dark py-3 px-lg-5 d-none d-lg-block">
    <div class="row">
        <div class="col-md-6 text-center text-lg-left mb-2 mb-lg-0">
            <div class="d-inline-flex align-items-center">
                <a class="text-body pr-3" href=""><i class="fa fa-phone-alt mr-2"></i>+593 967 252 447</a>
                <span class="text-body">|</span>
                <a class="text-body px-3" href=""><i class="fa fa-envelope mr-2"></i>contacto@ecuacars.com</a>
            </div>
        </div>
        <div class="col-md-6 text-center text-lg-right">
            <div class="d-inline-flex align-items-center">
                <a class="text-body px-3" href="https://facebook.com/freewebsitecode/">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a class="text-body px-3" href="https://freewebsitecode.com/">
                    <i class="fab fa-twitter"></i>
                </a>

                <a class="text-body px-3" href="https://freewebsitecode.com/">
                    <i class="fab fa-instagram"></i>
                </a>

            </div>
        </div>
    </div>
</div>
<!-- Fin de la Barra Superior -->

<!-- Inicio de la Barra de Navegación -->
<div class="container-fluid position-relative nav-bar p-0">
    <div class="position-relative px-lg-5" style="z-index: 9;">
        <nav class="navbar navbar-expand-lg bg-secondary navbar-dark py-3 py-lg-0 pl-3 pl-lg-5">
            <a href="index.php" class="navbar-brand">
                <h1 class="text-uppercase text-primary mb-1">Ecua Cars</h1>
            </a>
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Actualización de la barra de navegación -->
            <div class="collapse navbar-collapse justify-content-between px-3" id="navbarCollapse">
                <div class="navbar-nav ml-auto py-0">
                    <a href="index.php" class="nav-item nav-link">Inicio</a>
                    <a href="car.php" class="nav-item nav-link">Autos</a>
                    <a href="about.php" class="nav-item nav-link">Nosotros</a>
                    <a href="contact.php" class="nav-item nav-link">Contacto</a>
                    <!-- <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Páginas</a>
                        <div class="dropdown-menu rounded-0 m-0">
                            <a href="contact.php" class="dropdown-item">Contacto</a>
                            <a href="team.php" class="dropdown-item">Nuestro Equipo</a>
                        </div>
                    </div> -->
                    <?php if (!isLoggedIn()) { ?>
                        <a href="#loginModal" class="nav-item nav-link" data-toggle="modal" data-target="#loginModal">
                            <i class="fa fa-user mr-2"></i>Login/Registro
                        </a>
                    <?php } else { ?>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-user mr-2"></i><?php echo htmlspecialchars($_SESSION['fname']); ?>
                            </a>
                            <div class="dropdown-menu rounded-0 m-0">
                                <a href="profile.php" class="dropdown-item">
                                    <i class="fas fa-user-cog mr-2"></i>Configuración de perfil
                                </a>
                                <a href="update-password.php" class="dropdown-item">
                                    <i class="fas fa-key mr-2"></i>Actualiza contraseña
                                </a>
                                <a href="my-booking.php" class="dropdown-item">
                                    <i class="fas fa-calendar-check mr-2"></i>Mi reserva
                                </a>
                                <a href="post-testimonial.php" class="dropdown-item">
                                    <i class="fas fa-comment-dots mr-2"></i>Publicar un testimonio
                                </a>
                                <a href="my-testimonials.php" class="dropdown-item">
                                    <i class="fas fa-comments mr-2"></i>Mi Testimonio
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="logout.php" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Salir
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </nav>
    </div>
</div>
<!-- Fin de la Barra de Navegación -->

<!-- Modal de Login -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: #F4F5F8; border-radius: 8px;">
            <div class="modal-header border-0">
                <h3 class="modal-title">Iniciar Sesión</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <button class="btn btn-outline-dark btn-block mb-3"
                    style="display: flex; align-items: center; justify-content: center;">
                    <i class="fab fa-google mr-2"></i>Iniciar Sesión con Google
                </button>
                <p class="text-muted">O</p>
                <form method="post" action="">
                    <div class="form-group">
                        <input type="email" name="email" class="form-control" placeholder="Correo Electrónico" required>
                    </div>
                    <div class="form-group position-relative">
                        <input type="password" id="loginPassword" name="password" class="form-control"
                            placeholder="Contraseña" required>
                        <span class="password-toggle" onclick="toggleLoginPasswordVisibility()">
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>
                    <a href="recovery-password.php" class="d-block text-muted mb-3">¿Olvidaste tu contraseña?</a>
                    <button type="submit" name="login" class="btn btn-primary btn-block"
                        style="background-color: #F77D0A; border: none;">
                        Iniciar Sesión
                    </button>
                </form>
            </div>
            <div class="modal-footer justify-content-center border-0">
                <p class="mb-0">¿No tienes una cuenta? <a href="#" class="text-primary" data-dismiss="modal"
                        data-toggle="modal" data-target="#registerModal">Regístrate ahora</a></p>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Registro -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: #F4F5F8; border-radius: 8px;">
            <div class="modal-header border-0">
                <h3 class="modal-title mx-auto text-secondary">Crear Cuenta</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <form>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Nombre Completo" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Número de teléfono móvil" required>
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" placeholder="Correo Electrónico" required>
                    </div>
                    <div class="form-group position-relative">
                        <input type="password" id="registerPassword" class="form-control" placeholder="Contraseña"
                            required>
                        <span class="password-toggle" onclick="toggleRegisterPasswordVisibility()">
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block"
                        style="background-color: #F77D0A; border: none;">Registrarse</button>
                </form>
            </div>
            <div class="modal-footer justify-content-center border-0">
                <p class="mb-0">¿Ya tienes una cuenta? <a href="#" class="text-primary" data-dismiss="modal"
                        data-toggle="modal" data-target="#loginModal">Inicia sesión aquí</a></p>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleLoginPasswordVisibility() {
        const passwordInput = document.getElementById("loginPassword");
        const passwordToggle = document.querySelector("#loginModal .password-toggle i");

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

    function toggleRegisterPasswordVisibility() {
        const passwordInput = document.getElementById("registerPassword");
        const passwordToggle = document.querySelector("#registerModal .password-toggle i");

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
</script>