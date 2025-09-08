<?php
session_start();
$usuario = $_SESSION['usuario'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Inicio - Hotel Paraíso</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
</head>
<body>

<header>
  <h1>Bienvenido a Hotel Paraíso</h1>
  <p>Tu descanso y eventos en un solo lugar</p>
  <?php if ($usuario): ?>
    <p>Hola, <strong><?= htmlspecialchars($usuario) ?></strong></p>
  <?php endif; ?>
</header>

<nav>
  <?php if (!$usuario): ?>
    <a href="login.php" class="btn-nav login-btn">Iniciar sesión</a>
    <a href="registro.php" class="btn-nav register-btn">Registrar usuario</a>
  <?php else: ?>
    <a href="logout.php" class="btn-nav logout-btn">Cerrar sesión</a>
  <?php endif; ?>

  <a href="index.php" class="active">Inicio</a>
  <a href="habitaciones.html">Habitaciones</a>
  <a href="servicios.php">Servicios</a>
  <a href="contacto.html">Contacto</a>
  <a href="conferencias.php">Registro y reserva</a>
  <a href="comentarios.php">Comentarios</a>
</nav>

<main class="section">
  <h1>Servicios que ofrecemos</h1>
  <form id="form-servicios">
    <ul class="servicios-lista">
      <li>
        <label>WiFi gratis</label><br>
        <img src="imagenes/servicios/wifi.webp" alt="WiFi Gratis" width="500">
      </li>
      <li>
        <label>Restaurante</label><br>
        <img src="imagenes/servicios/restaurante.jpg" alt="Restaurante" width="500">
      </li>
      <li>
        <label>Piscina</label><br>
        <img src="imagenes/servicios/piscina.jpg" alt="Piscina" width="500">
      </li>
      <li>
        <label>Parqueo</label><br>
        <img src="imagenes/servicios/parqueo.webp" alt="Parqueo" width="500">
      </li>
      <li>
        <label>Gimnasio</label><br>
        <img src="imagenes/servicios/gimnasio.jpg" alt="Gimnasio" width="500">
      </li>
      <li>
        <label>Servicio a la habitación</label><br>
        <img src="imagenes/servicios/servicio.webp" alt="Servicio a la habitación" width="500">
      </li>
      <li>
        <label>Recepción 24/7</label><br>
        <img src="imagenes/servicios/recepcion.webp" alt="Recepción 24/7" width="500">
      </li>
    </ul>
  </form>
  <a href="index.php" class="btn-nav login-btn">← Volver al inicio</a>
</main>


<footer>
  &copy; 2025 Hotel Paraíso. Todos los derechos reservados.
</footer>

</body>
</html>
