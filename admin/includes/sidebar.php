<nav class="col-md-2 d-none d-md-block bg-dark sidebar">

    <div class="brand_logo_cont">
        <img src="assets/LogoRentAuto.png" class="brand_l" alt="Logo">
    </div>

    <div class="admin-info">
        Panel Administrativo
    </div>

    <div class="sidebar-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>"
                    href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>"
                    href="users.php">
                    <i class="fas fa-users"></i>
                    Usuarios
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'vehicles.php' ? 'active' : ''; ?>"
                    href="vehicles.php">
                    <i class="fas fa-car"></i>
                    Vehículos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reservas.php' ? 'active' : ''; ?>"
                    href="reservas.php">
                    <i class="fas fa-calendar-alt"></i>
                    Reservas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>"
                    href="reports.php">
                    <i class="fas fa-chart-bar"></i>
                    Informes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'tarifas.php' ? 'active' : ''; ?>"
                    href="tarifas.php">
                    <i class="fas fa-dollar-sign"></i>
                    Tarifas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </a>
            </li>
        </ul>
    </div>
</nav>