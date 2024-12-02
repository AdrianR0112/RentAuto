<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>ECUA CARS - EMPLEADO</title>

  <!-- Favicon -->
  <link href="img/favicon.ico" rel="icon">

  <!-- Google Web Fonts -->
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Rubik&display=swap"
    rel="stylesheet">

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">

  <!-- Estilos Personalizados de Bootstrap -->
  <link href="../css/bootstrap.min.css" rel="stylesheet">

  <!-- Estilo de Plantilla -->
  <link href="css/style.css" rel="stylesheet">

</head>

<body>
  <div class="container h-100">
    <div class="d-flex justify-content-center h-100">
      <div class="user_card">
        <div class="d-flex justify-content-center">
          <div class="brand_logo_container">
            <img src="assets\LogoRentAuto.png"
              class="brand_logo" alt="Logo">
          </div>
        </div>
        <div class="d-flex justify-content-center">
            <h3 class="text-admin-login">Panel de Empleados</h1>
          </div>
        <div class="d-flex justify-content-center form_container">
          <form>
            <div class="input-group mb-3">
              <div class="input-group-append">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
              </div>
              <input type="text" name="" class="form-control input_user" value="" placeholder="Usuario">
            </div>
            <div class="input-group mb-2">
              <div class="input-group-append">
                <span class="input-group-text"><i class="fas fa-key"></i></span>
              </div>
              <input type="password" name="" class="form-control input_pass" value="" placeholder="Contraseña">
            </div>
            <div class="d-flex justify-content-center mt-3 login_container">
              <button type="button" name="button" class="btn login_btn">Iniciar sesión</button>
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

</body>

</html>