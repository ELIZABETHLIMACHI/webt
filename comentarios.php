<?php
session_start(); // Iniciar sesión para acceder a $_SESSION
$usuario = $_SESSION['usuario'] ?? null;
$mensaje = '';

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$db = "hotel";

try {
  $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Error al conectar con la base de datos: " . $e->getMessage());
}

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = trim($_POST['nombre'] ?? '');
  $comentario = trim($_POST['comentario'] ?? '');
  $puntuacion = (int)($_POST['puntuacion'] ?? 5);

  if ($nombre !== '' && $comentario !== '' && $puntuacion >= 1 && $puntuacion <= 5) {
    $stmt = $pdo->prepare("INSERT INTO comentarios (usuario, comentario, fecha, puntuacion) VALUES (:usuario, :comentario, NOW(), :puntuacion)");
    $stmt->execute([
      ':usuario' => $nombre,
      ':comentario' => $comentario,
      ':puntuacion' => $puntuacion
    ]);
    header('Location: comentarios.php');
    exit;
  } else {
    $mensaje = "Por favor, completa todos los campos correctamente.";
  }
}

// Obtener los comentarios
$stmt = $pdo->query("SELECT usuario, comentario, fecha, puntuacion FROM comentarios ORDER BY fecha DESC");
$comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Comentarios - Hotel Paraíso</title>
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
    <?php if ($mensaje): ?>
      <p style="background-color:#f8d7da; color:#721c24; padding:10px; border-radius:5px;"><?= $mensaje ?></p>
    <?php endif; ?>

    <form method="POST" action="comentarios.php" style="max-width: 500px; margin-top: 20px;">
      <label for="nombre">Tu nombre:</label><br />
      <input type="text" id="nombre" name="nombre" required style="width:100%;" value="<?= htmlspecialchars($usuario ?? '') ?>"><br /><br />

      <label for="puntuacion">Puntuación:</label><br />
      <select id="puntuacion" name="puntuacion" required style="width:100%;">
        <option value="5">★★★★★ - Excelente</option>
        <option value="4">★★★★☆ - Muy bueno</option>
        <option value="3">★★★☆☆ - Bueno</option>
        <option value="2">★★☆☆☆ - Regular</option>
        <option value="1">★☆☆☆☆ - Malo</option>
      </select><br /><br />

      <label for="comentario">Tu comentario:</label><br />
      <textarea id="comentario" name="comentario" rows="4" required style="width:100%;"></textarea><br /><br />

      <button type="submit" class="btn-nav">Enviar comentario</button>
    </form>

    <h2 style="margin-top: 40px;">Últimos comentarios</h2>
    <ul class="servicios-lista">
      <?php if (count($comentarios) > 0): ?>
        <?php foreach ($comentarios as $c): ?>
          <li>
            <strong><?= htmlspecialchars($c['usuario']) ?></strong> (<?= $c['fecha'] ?>):
            <br />
            <?php
            $estrellas = intval($c['puntuacion']);
            for ($i = 1; $i <= 5; $i++) {
              echo $i <= $estrellas ? '★' : '☆';
            }
            ?>
            <br />
            <?= nl2br(htmlspecialchars($c['comentario'])) ?>
          </li>
        <?php endforeach; ?>
      <?php else: ?>
        <li>No hay comentarios aún. ¡Sé el primero en opinar!</li>
      <?php endif; ?>
    </ul>
  </main>

  <footer>
    &copy; 2025 Hotel Paraíso. Todos los derechos reservados.
  </footer>

</body>
</html>
