<?php
session_start();
$usuario = $_SESSION['usuario'] ?? null;

// Mostrar mensaje de reserva si existe
$mensaje_reserva = '';
if (isset($_SESSION['mensaje_reserva'])) {
    $mensaje_reserva = $_SESSION['mensaje_reserva'];
    unset($_SESSION['mensaje_reserva']); // Limpiar mensaje después de mostrarlo
}
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
  <h1>Hotel Paraíso</h1>
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
  <?php if ($mensaje_reserva): ?>
    <div style="background-color:#d4edda; color:#155724; padding:10px; border-radius:5px; margin:15px auto; max-width:600px; text-align:center;">
      <?= htmlspecialchars($mensaje_reserva) ?>
    </div>
  <?php endif; ?>

  <h2>Descubre nuestro hotel</h2>
  <p>Bienvenido al Hotel Paraíso. Ofrecemos habitaciones confortables, servicios de primer nivel y espacios para eventos profesionales.</p>
  <img src="./imagenes/habitaciones/deluxe.jpg" alt="Vista del hotel" style="max-width:100%; border-radius: 12px; margin-top: 20px;" />
</main>

<footer>
  &copy; 2025 Hotel Paraíso. Todos los derechos reservados.
</footer>

</body>
</html>
