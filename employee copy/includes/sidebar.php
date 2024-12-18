<nav class="col-md-2 d-none d-md-block bg-dark sidebar">
    <div class="brand_logo_cont text-center py-3">
        <img src="assets/LogoRentAuto.png" class="brand_l" alt="Logo">
    </div>

    <div class="admin-info text-center text-white py-2">
        Panel del Empleado
    </div>

    <div class="sidebar-sticky">
        <ul class="nav flex-column">
            <!-- Inicio -->
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" 
                    href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </li>

            <!-- Gestión de Alquileres -->
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#alquileresMenu" role="button" 
                    aria-expanded="false" aria-controls="alquileresMenu">
                    <i class="fas fa-car-side"></i>
                    Alquileres
                </a>
                <ul class="collapse list-unstyled" id="alquileresMenu">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'gestionar_alquileres.php' ? 'active' : ''; ?>" 
                            href="gestionar_alquileres.php">
                            Gestión de Alquileres
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'registrar_alquiler.php' ? 'active' : ''; ?>" 
                            href="registrar_alquiler.php">
                            Registrar Alquiler
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Gestión de Devoluciones -->
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#devolucionesMenu" role="button" 
                    aria-expanded="false" aria-controls="devolucionesMenu">
                    <i class="fas fa-undo-alt"></i>
                    Devoluciones
                </a>
                <ul class="collapse list-unstyled" id="devolucionesMenu">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'gestionar_devoluciones.php' ? 'active' : ''; ?>" 
                            href="gestionar_devoluciones.php">
                            Gestión de Devoluciones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'registrar_devolucion.php' ? 'active' : ''; ?>" 
                            href="registrar_devolucion.php">
                            Registrar Devolución
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Consulta de Disponibilidad -->
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'consulta_disponibilidad.php' ? 'active' : ''; ?>" 
                    href="consulta_disponibilidad.php">
                    <i class="fas fa-search"></i>
                    Consulta de Disponibilidad
                </a>
            </li>

            <!-- Facturación -->
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'facturacion.php' ? 'active' : ''; ?>" 
                    href="facturacion.php">
                    <i class="fas fa-dollar-sign"></i>
                    Facturación y Pagos
                </a>
            </li>

            <!-- Gestión de Clientes -->
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'gestionar_clientes.php' ? 'active' : ''; ?>" 
                    href="gestionar_clientes.php">
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

<!-- Agregar el script de Bootstrap para que funcione el collapse -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
