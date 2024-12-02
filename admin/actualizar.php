<?php
session_start();
require_once '../includes/config.php';

// Check if admin is logged in
// if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
//     header("Location: login.php");
//     exit();
// }

// Initialize variables
$id = $username = $password = $confirm_password = '';
$error_message = $success_message = '';
$edit_mode = false;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validate inputs
    if (empty($username)) {
        $error_message = "El nombre de usuario es obligatorio.";
    }

    // Check if editing existing admin or creating new
    if (!empty($_POST['id'])) {
        $id = $_POST['id'];
        $edit_mode = true;
    }

    // Password validation (only for new admins or when password is changed)
    if (!$edit_mode || !empty($password)) {
        if (empty($password)) {
            $error_message = "La contraseña es obligatoria.";
        } elseif (strlen($password) < 8) {
            $error_message = "La contraseña debe tener al menos 8 caracteres.";
        } elseif ($password !== $confirm_password) {
            $error_message = "Las contraseñas no coinciden.";
        }
    }

    // If no errors, proceed with database operation
    if (empty($error_message)) {
        try {
            if ($edit_mode) {
                // Update existing admin
                if (empty($password)) {
                    // If no new password, update only username
                    $stmt = $dbh->prepare("UPDATE admin SET NombreUsuario = :username WHERE id = :id");
                    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                } else {
                    // Update username and password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $dbh->prepare("UPDATE admin SET NombreUsuario = :username, Contrasena = :password WHERE id = :id");
                    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                    $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                }
            } else {
                // Create new admin
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $dbh->prepare("INSERT INTO admin (NombreUsuario, Contrasena) VALUES (:username, :password)");
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
            }

            // Execute the statement
            $stmt->execute();

            $success_message = $edit_mode ? "Administrador actualizado exitosamente." : "Administrador creado exitosamente.";
            
            // Clear form fields
            $id = $username = $password = $confirm_password = '';
        } catch(PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}

// Handle edit request
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    try {
        $stmt = $dbh->prepare("SELECT id, NombreUsuario FROM admin WHERE id = :id");
        $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            $id = $admin['id'];
            $username = $admin['NombreUsuario'];
            $edit_mode = true;
        }
    } catch(PDOException $e) {
        $error_message = "Error al cargar los datos del administrador.";
    }
}

// Fetch all admins
try {
    $stmt = $dbh->query("SELECT id, NombreUsuario, FechaActualizacion FROM admin ORDER BY FechaActualizacion DESC");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error_message = "Error al recuperar la lista de administradores.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>ECUA CARS - Gestión de Administradores</title>

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
    <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>
    <div class="container-fluid">
        <?php include 'includes/sidebar.php'; ?>

        <main role="main" class="content-wrapper col-md-9 ml-sm-auto col-lg-10 px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2"><?php echo $edit_mode ? 'Editar Administrador' : 'Crear Administrador'; ?></h1>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <?php echo $edit_mode ? 'Editar Administrador' : 'Nuevo Administrador'; ?>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <?php if ($edit_mode): ?>
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                                <?php endif; ?>

                                <div class="form-group">
                                    <label for="username">Nombre de Usuario</label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?php echo htmlspecialchars($username); ?>" 
                                           required>
                                </div>

                                <div class="form-group">
                                    <label for="password"><?php echo $edit_mode ? 'Nueva Contraseña (dejar en blanco si no desea cambiar)' : 'Contraseña'; ?></label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           <?php echo $edit_mode ? '' : 'required'; ?>>
                                </div>

                                <div class="form-group">
                                    <label for="confirm_password"><?php echo $edit_mode ? 'Confirmar Nueva Contraseña' : 'Confirmar Contraseña'; ?></label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                           <?php echo $edit_mode ? '' : 'required'; ?>>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <?php echo $edit_mode ? 'Actualizar Administrador' : 'Crear Administrador'; ?>
                                </button>
                                <?php if ($edit_mode): ?>
                                    <a href="admin_management.php" class="btn btn-secondary ml-2">Cancelar</a>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            Lista de Administradores
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre de Usuario</th>
                                            <th>Última Actualización</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($admins as $admin): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($admin['id']); ?></td>
                                                <td><?php echo htmlspecialchars($admin['NombreUsuario']); ?></td>
                                                <td><?php echo htmlspecialchars($admin['FechaActualizacion']); ?></td>
                                                <td>
                                                    <a href="?action=edit&id=<?php echo $admin['id']; ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button onclick="confirmDelete(<?php echo $admin['id']; ?>)" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Confirmación de Eliminación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ¿Está seguro que desea eliminar este administrador?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="deleteAdmin()">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script>
        let adminToDelete = null;

        function confirmDelete(id) {
            adminToDelete = id;
            $('#deleteModal').modal('show');
        }

        function deleteAdmin() {
            if (adminToDelete) {
                window.location.href = `?action=delete&id=${adminToDelete}`;
            }
        }
    </script>
</body>
</html>