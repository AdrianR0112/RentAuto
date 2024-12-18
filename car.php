<?php
session_start();
require_once 'includes/config.php';
error_reporting(0);
// Inicializar variables de filtro
$where_conditions = [];
$params = [];

// Procesar filtros
if ($_GET) {
    if (!empty($_GET['marca'])) {
        $where_conditions[] = "m.IdMarca = ?";
        $params[] = $_GET['marca'];
    }
    if (!empty($_GET['categoria'])) {
        $where_conditions[] = "v.Categoria = ?";
        $params[] = $_GET['categoria'];
    }
    if (!empty($_GET['precio_min'])) {
        $where_conditions[] = "v.PrecioPorDia >= ?";
        $params[] = $_GET['precio_min'];
    }
    if (!empty($_GET['precio_max'])) {
        $where_conditions[] = "v.PrecioPorDia <= ?";
        $params[] = $_GET['precio_max'];
    }
    if (!empty($_GET['transmision'])) {
        $where_conditions[] = "v.Transmision = ?";
        $params[] = $_GET['transmision'];
    }
    if (!empty($_GET['combustible'])) {
        $where_conditions[] = "v.TipoCombustible = ?";
        $params[] = $_GET['combustible'];
    }
    if (!empty($_GET['ano_min'])) {
        $where_conditions[] = "m.AnoModelo >= ?";
        $params[] = $_GET['ano_min'];
    }
    if (!empty($_GET['capacidad'])) {
        $where_conditions[] = "v.CapacidadAsientos >= ?";
        $params[] = $_GET['capacidad'];
    }
}

// Construir consulta SQL
$sql = "SELECT v.id, 
        CONCAT(b.NombreMarca, ' ', m.NombreModelo) AS TituloVehiculo, 
        v.CapacidadAsientos, v.TipoCombustible, 
        v.Transmision, v.PrecioPorDia, v.Imagen1, v.Categoria, 
        m.AnoModelo, b.NombreMarca 
        FROM tblvehicles v
        JOIN tblmodels m ON v.IdModelo = m.id
        JOIN tblbrands b ON m.IdMarca = b.id";

if (!empty($where_conditions)) {
    $sql .= " WHERE " . implode(" AND ", $where_conditions);
}

$stmt = $dbh->prepare($sql);
$stmt->execute($params);

// Consulta para obtener marcas
$marcas_stmt = $dbh->query("SELECT DISTINCT b.id, b.NombreMarca 
                           FROM tblbrands b 
                           INNER JOIN tblmodels m ON b.id = m.IdMarca 
                           ORDER BY b.NombreMarca");

$categorias_stmt = $dbh->query("SELECT DISTINCT Categoria FROM tblvehicles ORDER BY Categoria");
$transmisiones_stmt = $dbh->query("SELECT DISTINCT Transmision FROM tblvehicles");
$combustibles_stmt = $dbh->query("SELECT DISTINCT TipoCombustible FROM tblvehicles");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>ECUA CARS - Alquiler de Autos</title>

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
    <link href="css/style2.css" rel="stylesheet">
</head>

<body>
    <?php include('includes/header.php'); ?>

    <div class="container-fluid page-header">
        <h1 class="display-3 text-uppercase text-white mb-3">Lista de Autos</h1>
    </div>

    <div class="container-fluid py-5">
        <div class="container pt-5 pb-3">
            <div class="row">
                <!-- Sidebar de Filtros -->
                <div class="col-lg-3">
                    <div class="filter-sidebar">
                        <h4 class="mb-4">Filtrar Vehículos</h4>
                        <form action="" method="GET">
                            <!-- Marca -->
                            <div class="filter-section">
                                <div class="filter-title">Marca</div>
                                <select name="marca" class="form-control">
                                    <option value="">Todas las marcas</option>
                                    <?php while ($marca = $marcas_stmt->fetch()) : ?>
                                        <option value="<?php echo htmlspecialchars($marca['id']); ?>"
                                                <?php echo isset($_GET['marca']) && $_GET['marca'] == $marca['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($marca['NombreMarca']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <!-- Categoría -->
                            <div class="filter-section">
                                <div class="filter-title">Categoría</div>
                                <select name="categoria" class="form-control">
                                    <option value="">Todas las categorías</option>
                                    <?php while ($categoria = $categorias_stmt->fetch()) : ?>
                                        <option value="<?php echo htmlspecialchars($categoria['Categoria']); ?>"
                                                <?php echo isset($_GET['categoria']) && $_GET['categoria'] == $categoria['Categoria'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($categoria['Categoria']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <!-- Rango de Precio -->
                            <div class="filter-section">
                                <div class="filter-title">Rango de Precio</div>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="number" name="precio_min" class="form-control" placeholder="Mín" 
                                               value="<?php echo isset($_GET['precio_min']) ? htmlspecialchars($_GET['precio_min']) : ''; ?>">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" name="precio_max" class="form-control" placeholder="Máx"
                                               value="<?php echo isset($_GET['precio_max']) ? htmlspecialchars($_GET['precio_max']) : ''; ?>">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Aplicar Filtros</button>
                            <a href="?" class="btn btn-secondary w-100 mt-2">Limpiar Filtros</a>
                        </form>
                    </div>
                </div>

                <!-- Lista de Vehículos -->
                <div class="col-lg-9">
                    <div class="row">
                        <?php
                        if ($stmt->rowCount() > 0) {
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                <div class="col-lg-4 col-md-6 mb-2">
                                    <div class="rent-item mb-4">
                                        <img class="img-fluid mb-4" src="img/cars/<?php echo htmlspecialchars($row['Imagen1']); ?>" alt="">
                                        <h4 class="text-uppercase mb-2"><?php echo htmlspecialchars($row['TituloVehiculo']); ?></h4>
                                        <div class="px-2 mb-2">
                                            <span>$<?php echo htmlspecialchars($row['PrecioPorDia']); ?>/Día</span>
                                        </div>
                                        <div class="d-flex justify-content-center mb-4">
                                            <div class="px-2">
                                                <i class="fa fa-user text-primary mr-1"></i>
                                                <span><?php echo htmlspecialchars($row['CapacidadAsientos']); ?></span>
                                            </div>
                                            <div class="px-2 border-left border-right">
                                                <i class="fa fa-gas-pump text-primary mr-1"></i>
                                                <span><?php echo htmlspecialchars($row['TipoCombustible']); ?></span>
                                            </div>
                                            <div class="px-2">
                                                <i class="fa fa-cogs text-primary mr-1"></i>
                                                <span><?php echo htmlspecialchars($row['Transmision']); ?></span>
                                            </div>
                                        </div>
                                        <a class="btn btn-primary px-3" href="detail.php?id=<?php echo $row['id']; ?>">Ver detalles</a>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<div class="col-12"><p class="text-center">No se encontraron vehículos que coincidan con los filtros seleccionados.</p></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include('includes/footer.php'); ?>
    <!-- Fin Footer -->

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
</body>
</html>