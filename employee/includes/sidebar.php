<nav class="col-md-2 d-none d-md-block bg-dark sidebar">
    <div class="brand_logo_cont text-center py-3">
        <img src="assets/LogoRentAuto.png" class="brand_l" alt="Logo">
    </div>

    <div class="admin-info text-center text-white py-2">
        Panel del Empleado
    </div>

    <div class="sidebar-sticky">
        <ul class="nav flex-column">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" 
                    href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </li>

            <!-- Gestión de Alquileres -->
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#rentalsMenu" role="button" 
                    aria-expanded="false" aria-controls="rentalsMenu">
                    <i class="fas fa-car-side"></i>
                    Gestión de Alquileres
                </a>
                <div class="collapse" id="rentalsMenu">
                    <ul class="list-unstyled pl-3">
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_rentals.php' ? 'active' : ''; ?>" 
                                href="manage_rentals.php">
                                Gestionar Alquileres
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'register_rental.php' ? 'active' : ''; ?>" 
                                href="register_rental.php">
                                Registrar Alquiler
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Gestión de Devoluciones -->
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#returnsMenu" role="button" 
                    aria-expanded="false" aria-controls="returnsMenu">
                    <i class="fas fa-undo-alt"></i>
                    Gestión de Devoluciones
                </a>
                <div class="collapse" id="returnsMenu">
                    <ul class="list-unstyled pl-3">
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_returns.php' ? 'active' : ''; ?>" 
                                href="manage_returns.php">
                                Gestionar Devoluciones
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'register_return.php' ? 'active' : ''; ?>" 
                                href="register_return.php">
                                Registrar Devolución
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Consulta de Disponibilidad -->
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'check_availability.php' ? 'active' : ''; ?>" 
                    href="check_availability.php">
                    <i class="fas fa-search"></i>
                    Consulta de Disponibilidad
                </a>
            </li>

            <!-- Facturación y Pagos -->
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'billing.php' ? 'active' : ''; ?>" 
                    href="billing.php">
                    <i class="fas fa-dollar-sign"></i>
                    Facturación y Pagos
                </a>
            </li>

            <!-- Gestión de Clientes -->
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_clients.php' ? 'active' : ''; ?>" 
                    href="manage_clients.php">
                    <i class="fas fa-users"></i>
                    Gestión de Clientes
                </a>
            </li>

            <!-- Cerrar Sesión -->
            <li class="nav-item">
                <a class="nav-link" href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </a>
            </li>
        </ul>
    </div>
</nav>

