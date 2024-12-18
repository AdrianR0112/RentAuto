<?php
session_start();
require_once '../includes/config.php';
error_reporting(E_ALL);

// Initialize error message variable
$error_message = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check if username or password is empty
    if (empty($username) || empty($password)) {
        $error_message = "Por favor, ingrese usuario y contraseña.";
    } else {
        try {
            // Prepare SQL statement to prevent SQL injection
            $stmt = $dbh->prepare("SELECT id, NombreEmpleado, Contrasena, Correo FROM tblempleados WHERE NombreEmpleado = :username");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();

            // Fetch the employee record
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify password
            if ($employee && password_verify($password, $employee['Contrasena'])) {
                // Password is correct, start a new session
                $_SESSION['employee_logged_in'] = true;
                $_SESSION['employee_id'] = $employee['id'];
                $_SESSION['employee_username'] = $employee['NombreEmpleado'];
                $_SESSION['employee_email'] = $employee['Correo'];

                // Redirect to dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                // Invalid credentials
                $error_message = "Usuario o contraseña incorrectos.";
            }
        } catch (PDOException $e) {
            // Log error (in a production environment, log to file instead of displaying)
            error_log("Login error: " . $e->getMessage());
            $error_message = "Error de conexión. Por favor, intente nuevamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>ECUA CARS - Inicio de Sesión Empleado</title>

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Rubik&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container h-100">
        <div class="d-flex justify-content-center h-100">
            <div class="user_card">
                <div class="d-flex justify-content-center">
                    <div class="brand_logo_container">
                        <img src="assets/LogoRentAuto.png" class="brand_logo" alt="Logo">
                    </div>
                </div>
                <div class="d-flex justify-content-center">
                    <h3 class="text-admin-login">Panel del Empleado</h3>
                </div>

                <?php
                // Display error message if exists
                if (!empty($error_message)): ?>
                    <div class="alert alert-danger text-center" role="alert">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-center form_container">
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="input-group mb-3">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                            <input type="text" name="username" class="form-control input_user"
                                value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>"
                                placeholder="Usuario" required>
                        </div>
                        <div class="input-group mb-2">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                            </div>
                            <input type="password" name="password" class="form-control input_pass"
                                placeholder="Contraseña" required>
                        </div>
                        <div class="d-flex justify-content-center mt-3 login_container">
                            <button type="submit" class="btn login_btn">Iniciar sesión</button>
                        </div>
                    </form>
                </div>

                <div class="mt-4">
                    <div class="d-flex justify-content-center links">
                        <a href="#" class="text-secondary-link">¿Olvidaste tu contraseña?</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional: Add scripts if needed -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
</body>
</html>