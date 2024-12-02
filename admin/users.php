<?php
session_start();
require_once '../includes/config.php';
error_reporting(0);

// Verificar si el usuario es administrador
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Si no está autenticado, redirigir al login
    header("Location: login.php");
    exit();
}

// Función para obtener todos los usuarios
function getUsers($dbh)
{
    try {
        $stmt = $dbh->query("SELECT * FROM tblusers ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return array('error' => $e->getMessage());
    }
}

// Función para obtener un usuario específico
function getUser($dbh, $userId)
{
    try {
        $stmt = $dbh->prepare("SELECT * FROM tblusers WHERE id = :userId");
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return array('error' => $e->getMessage());
    }
}

// Función para añadir un nuevo usuario
function addUser($dbh, $userData)
{
    try {
        $stmt = $dbh->prepare("
            INSERT INTO tblusers (
                NombreCompleto, 
                CorreoElectronico, 
                Contrasena, 
                NumeroContacto,
                FechaNacimiento,
                Direccion,
                Ciudad,
                Pais,
                FechaRegistro
            ) VALUES (
                :nombreCompleto,
                :correoElectronico,
                :contrasena,
                :numeroContacto,
                :fechaNacimiento,
                :direccion,
                :ciudad,
                :pais,
                NOW()
            )
        ");

        $hashedPassword = password_hash($userData['contrasena'], PASSWORD_DEFAULT);

        return $stmt->execute([
            'nombreCompleto' => $userData['nombreCompleto'],
            'correoElectronico' => $userData['correoElectronico'],
            'contrasena' => $hashedPassword,
            'numeroContacto' => $userData['numeroContacto'],
            'fechaNacimiento' => $userData['fechaNacimiento'],
            'direccion' => $userData['direccion'],
            'ciudad' => $userData['ciudad'],
            'pais' => $userData['pais']
        ]);
    } catch (PDOException $e) {
        return array('error' => $e->getMessage());
    }
}

// Función para actualizar un usuario
function updateUser($dbh, $userData)
{
    try {
        $sql = "UPDATE tblusers SET 
                NombreCompleto = :nombreCompleto,
                CorreoElectronico = :correoElectronico,
                NumeroContacto = :numeroContacto,
                FechaNacimiento = :fechaNacimiento,
                Direccion = :direccion,
                Ciudad = :ciudad,
                Pais = :pais";

        $params = [
            'id' => $userData['id'],
            'nombreCompleto' => $userData['nombreCompleto'],
            'correoElectronico' => $userData['correoElectronico'],
            'numeroContacto' => $userData['numeroContacto'],
            'fechaNacimiento' => $userData['fechaNacimiento'],
            'direccion' => $userData['direccion'],
            'ciudad' => $userData['ciudad'],
            'pais' => $userData['pais']
        ];

        // Solo actualizar la contraseña si se proporciona una nueva
        if (!empty($userData['contrasena'])) {
            $sql .= ", Contrasena = :contrasena";
            $params['contrasena'] = password_hash($userData['contrasena'], PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = :id";

        $stmt = $dbh->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        return array('error' => $e->getMessage());
    }
}

// Función para eliminar un usuario
function deleteUser($dbh, $userId)
{
    try {
        $stmt = $dbh->prepare("DELETE FROM tblusers WHERE id = :userId");
        return $stmt->execute(['userId' => $userId]);
    } catch (PDOException $e) {
        return array('error' => $e->getMessage());
    }
}

// Manejador de solicitudes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = array('success' => false, 'message' => '');

    $action = isset($_POST['action']) ? $_POST['action'] : '';

    switch ($action) {
        case 'add':
            if (addUser($dbh, $_POST)) {
                $response['success'] = true;
                $response['message'] = 'Usuario agregado exitosamente';
            } else {
                $response['message'] = 'Error al agregar usuario';
            }
            break;

        case 'update':
            if (updateUser($dbh, $_POST)) {
                $response['success'] = true;
                $response['message'] = 'Usuario actualizado exitosamente';
            } else {
                $response['message'] = 'Error al actualizar usuario';
            }
            break;

        case 'delete':
            if (deleteUser($dbh, $_POST['userId'])) {
                $response['success'] = true;
                $response['message'] = 'Usuario eliminado exitosamente';
            } else {
                $response['message'] = 'Error al eliminar usuario';
            }
            break;

        case 'get':
            $user = getUser($dbh, $_POST['userId']);
            if ($user) {
                $response['success'] = true;
                $response['user'] = $user;
            } else {
                $response['message'] = 'Usuario no encontrado';
            }
            break;

        default:
            $response['message'] = 'Acción no válida';
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>ECUA CARS - Gestión de Usuarios</title>
    <link href="img/favicon.ico" rel="icon">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Rubik&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="css/sidebar.css">
</head>

<body>
    <div class="container-fluid">
        <!-- Barra de navegación -->
        <?php include 'includes/sidebar.php'; ?>
        <main role="main" class="content-wrapper col-md-9 ml-sm-auto col-lg-10 px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb- mb-3 border-bottom">
                <h2 class="mb-4">Gestión de Usuarios</h2>

                <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addUserModal">
                    <i class="fas fa-user-plus"></i> Añadir Usuario
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Correo Electrónico</th>
                            <th>Número de Contacto</th>
                            <th>Ciudad</th>
                            <th>País</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $users = getUsers($dbh);
                        foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['NombreCompleto']); ?></td>
                                <td><?php echo htmlspecialchars($user['CorreoElectronico']); ?></td>
                                <td><?php echo htmlspecialchars($user['NumeroContacto']); ?></td>
                                <td><?php echo htmlspecialchars($user['Ciudad']); ?></td>
                                <td><?php echo htmlspecialchars($user['Pais']); ?></td>
                                <td><?php echo $user['FechaRegistro']; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="editUser(<?php echo $user['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $user['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal para añadir usuario -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Añadir Usuario</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                        <input type="hidden" name="action" value="add">
                        <div class="form-group">
                            <label>Nombre Completo</label>
                            <input type="text" class="form-control" name="nombreCompleto" required>
                        </div>
                        <div class="form-group">
                            <label>Correo Electrónico</label>
                            <input type="email" class="form-control" name="correoElectronico" required>
                        </div>
                        <div class="form-group">
                            <label>Contraseña</label>
                            <input type="password" class="form-control" name="contrasena" required>
                        </div>
                        <div class="form-group">
                            <label>Número de Contacto</label>
                            <input type="text" class="form-control" name="numeroContacto" required>
                        </div>
                        <div class="form-group">
                            <label>Fecha de Nacimiento</label>
                            <input type="date" class="form-control" name="fechaNacimiento" required>
                        </div>
                        <div class="form-group">
                            <label>Dirección</label>
                            <input type="text" class="form-control" name="direccion" required>
                        </div>
                        <div class="form-group">
                            <label>Ciudad</label>
                            <input type="text" class="form-control" name="ciudad" required>
                        </div>
                        <div class="form-group">
                            <label>País</label>
                            <input type="text" class="form-control" name="pais" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="saveUser()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar usuario -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Usuario</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="editUserId">
                        <div class="form-group">
                            <label>Nombre Completo</label>
                            <input type="text" class="form-control" name="nombreCompleto" id="editNombreCompleto"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Correo Electrónico</label>
                            <input type="email" class="form-control" name="correoElectronico" id="editCorreoElectronico"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Nueva Contraseña (dejar en blanco para mantener la actual)</label>
                            <input type="password" class="form-control" name="contrasena">
                        </div>
                        <div class="form-group">
                            <label>Número de Contacto</label>
                            <input type="text" class="form-control" name="numeroContacto" id="editNumeroContacto"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Fecha de Nacimiento</label>
                            <input type="date" class="form-control" name="fechaNacimiento" id="editFechaNacimiento"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Dirección</label>
                            <input type="text" class="form-control" name="direccion" id="editDireccion" required>
                        </div>
                        <div class="form-group">
                            <label>Ciudad</label>
                            <input type="text" class="form-control" name="ciudad" id="editCiudad" required>
                        </div>
                        <div class="form-group">
                            <label>País</label>
                            <input type="text" class="form-control" name="pais" id="editPais" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="updateUser()">Actualizar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>

    <script>
        function saveUser() {
            const formData = new FormData(document.getElementById('addUserForm'));

            $.ajax({
                url: 'users.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        $('#addUserModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    alert('Error al guardar usuario: ' + error);
                }
            });
        }

        function editUser(userId) {
            $.ajax({
                url: 'users.php',
                method: 'POST',
                data: {
                    action: 'get',
                    userId: userId
                },
                success: function (response) {
                    if (response.success) {
                        $('#editUserId').val(response.user.id);
                        $('#editUserName').val(response.user.name);
                        $('#editUserEmail').val(response.user.email);
                        $('#editUserRole').val(response.user.role);
                        $('#editUserModal').modal('show');
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    alert('Error al obtener datos del usuario: ' + error);
                }
            });
        }

        function updateUser() {
            const formData = new FormData(document.getElementById('editUserForm'));

            $.ajax({
                url: 'users.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        $('#editUserModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    alert('Error al actualizar usuario: ' + error);
                }
            });
        }

        function deleteUser(userId) {
            if (confirm('¿Está seguro de que desea eliminar este usuario?')) {
                $.ajax({
                    url: 'users.php',
                    method: 'POST',
                    data: {
                        action: 'delete',
                        userId: userId
                    },
                    success: function (response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        alert('Error al eliminar usuario: ' + error);
                    }
                });
            }
        }
    </script>
</body>

</html>