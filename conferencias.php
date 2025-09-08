<?php
session_start();
$usuario = $_SESSION['usuario'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Conferencias - Hotel Paraíso</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
  <style>
    /* Estilos para las pestañas */
    .tabs-container {
      margin: 20px 0;
    }
    
    .tabs-nav {
      display: flex;
      border-bottom: 2px solid #ddd;
      margin-bottom: 20px;
    }
    
    .tab-button {
      padding: 12px 24px;
      background: none;
      border: none;
      cursor: pointer;
      font-size: 16px;
      font-family: 'Montserrat', sans-serif;
      color: #666;
      border-bottom: 3px solid transparent;
      transition: all 0.3s ease;
    }
    
    .tab-button:hover {
      color: #333;
      background-color: #f5f5f5;
    }
    
    .tab-button.active {
      color: #2c3e50;
      border-bottom-color: #3498db;
      background-color: #fff;
    }
    
    .tab-content {
      display: none;
      animation: fadeIn 0.3s ease-in;
    }
    
    .tab-content.active {
      display: block;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    /* Estilos para el formulario de reservaciones */
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
      color: #333;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-family: 'Montserrat', sans-serif;
      font-size: 14px;
    }
    
    .form-group textarea {
      resize: vertical;
      height: 100px;
    }
    
    .form-row {
      display: flex;
      gap: 20px;
    }
    
    .form-row .form-group {
      flex: 1;
    }
    
    .btn-submit {
      background-color: #3498db;
      color: white;
      padding: 12px 30px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
      font-family: 'Montserrat', sans-serif;
      transition: background-color 0.3s ease;
    }
    
    .btn-submit:hover {
      background-color: #2980b9;
    }
    
    .reservacion-info {
      background-color: #f8f9fa;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 30px;
    }
  </style>
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
  <h2>Servicios de Conferencias</h2>
  
  <div class="tabs-container">
    <div class="tabs-nav">
      <button class="tab-button active" onclick="switchTab('conferencias')">salones</button>
      <button class="tab-button" onclick="switchTab('reservaciones')">Reservaciones</button>
    </div>

    <!-- Contenido de Conferencias -->
    <div id="conferencias" class="tab-content active">
      <h3>Salones de eventos</h3>
      <p>En Hotel Paraíso contamos con modernos espacios para conferencias, reuniones y eventos corporativos:</p>

      <ul class="servicios-lista">
        <li><strong>Salón principal:</strong> Capacidad para 150 personas, equipado con proyector y sonido.</li>
        <li><strong>Salas ejecutivas:</strong> Espacios privados para juntas pequeñas.</li>
        <li><strong>Internet de alta velocidad:</strong> Conexión segura para videoconferencias.</li>
        <li><strong>Catering disponible:</strong> Opciones de refrigerios y comidas para tus eventos.</li>
      </ul>

      <img src="./imagenes/conferencia/salon.jpg" alt="Salón de conferencias" style="max-width:100%; border-radius: 12px; margin-top: 20px;"/>

      <div style="margin-top: 40px;">
        <h4>Información de contacto</h4>
        <p>Para más información sobre disponibilidad y precios, contacta con nuestro equipo de eventos:</p>
        <ul class="servicios-lista">
          <li><strong>Teléfono:</strong> +1 (555) 123-4567</li>
          <li><strong>Email:</strong> eventos@hotelparaiso.com</li>
          <li><strong>Horario de atención:</strong> Lunes a viernes, 9:00 AM - 6:00 PM</li>
        </ul>
      </div>
    </div>

    <!-- Contenido de Reservaciones -->
    <div id="reservaciones" class="tab-content">
      <h3>Reserva tu espacio para conferencias</h3>
      
      <?php if (!$usuario): ?>
        <div class="reservacion-info" style="background-color: #fff3cd; border: 1px solid #ffeaa7;">
          <h4>⚠️ Inicio de sesión requerido</h4>
          <p>Para realizar una reservación necesitas <a href="login.php">iniciar sesión</a> o <a href="registro.php">crear una cuenta</a>.</p>
        </div>
      <?php else: ?>
        <div class="reservacion-info">
          <h4>Información importante:</h4>
          <ul class="servicios-lista">
            <li>Las reservaciones deben realizarse con mínimo 48 horas de anticipación</li>
            <li>Se requiere confirmación del equipo de eventos</li>
            <li>Incluye servicio de café y agua durante el evento</li>
            <li>Descuentos disponibles para reservaciones de múltiples días</li>
          </ul>
        </div>
      <?php endif; ?>

      <?php if ($usuario): ?>
      <form action="procesar_reservacion.php" method="POST">
        <div class="form-row">
          <div class="form-group">
            <label for="nombre_contacto">Nombre del contacto *</label>
            <input type="text" id="nombre_contacto" name="nombre_contacto" required>
          </div>
          <div class="form-group">
            <label for="empresa">Empresa/Organización</label>
            <input type="text" id="empresa" name="empresa">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" required>
          </div>
          <div class="form-group">
            <label for="telefono">Teléfono *</label>
            <input type="tel" id="telefono" name="telefono" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="tipo_sala">Tipo de sala *</label>
            <select id="tipo_sala" name="tipo_sala" required>
              <option value="">Selecciona una opción</option>
              <option value="salon_principal">Salón Principal (150 personas)</option>
              <option value="sala_ejecutiva_1">Sala Ejecutiva 1 (20 personas)</option>
              <option value="sala_ejecutiva_2">Sala Ejecutiva 2 (15 personas)</option>
              <option value="sala_juntas">Sala de Juntas (8 personas)</option>
            </select>
          </div>
          <div class="form-group">
            <label for="num_asistentes">Número de asistentes *</label>
            <input type="number" id="num_asistentes" name="num_asistentes" min="1" max="150" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="fecha_evento">Fecha del evento *</label>
            <input type="date" id="fecha_evento" name="fecha_evento" required>
          </div>
          <div class="form-group">
            <label for="hora_inicio">Hora de inicio *</label>
            <input type="time" id="hora_inicio" name="hora_inicio" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="hora_fin">Hora de fin *</label>
            <input type="time" id="hora_fin" name="hora_fin" required>
          </div>
          <div class="form-group">
            <label for="catering">Servicio de catering</label>
            <select id="catering" name="catering">
              <option value="ninguno">Sin catering</option>
              <option value="coffee_break">Coffee Break</option>
              <option value="almuerzo_ligero">Almuerzo ligero</option>
              <option value="almuerzo_completo">Almuerzo completo</option>
              <option value="cena">Cena</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="requerimientos">Requerimientos especiales</label>
          <textarea id="requerimientos" name="requerimientos" placeholder="Describe cualquier equipo adicional, disposición especial de mesas, o servicios extras que necesites..."></textarea>
        </div>

        <div class="form-group">
          <button type="submit" class="btn-submit">Enviar solicitud de reservación</button>
        </div>
      </form>
      <?php else: ?>
        <p style="text-align: center; color: #666; font-style: italic;">
          Inicia sesión para acceder al formulario de reservaciones.
        </p>
      <?php endif; ?>

      <div style="margin-top: 30px;">
        <h4>¿Necesitas ayuda?</h4>
        <p>Nuestro equipo de eventos está disponible para asesorarte:</p>
        <ul class="servicios-lista">
          <li><strong>WhatsApp:</strong> +1 (555) 123-4567</li>
          <li><strong>Email directo:</strong> reservas@hotelparaiso.com</li>
          <li><strong>Atención inmediata:</strong> Recepción del hotel</li>
        </ul>
      </div>
    </div>
  </div>

</main>

<footer>
  &copy; 2025 Hotel Paraíso. Todos los derechos reservados.
</footer>

<script>
function switchTab(tabName) {
  // Ocultar todos los contenidos de pestañas
  const allTabs = document.querySelectorAll('.tab-content');
  allTabs.forEach(tab => {
    tab.classList.remove('active');
  });
  
  // Quitar clase active de todos los botones
  const allButtons = document.querySelectorAll('.tab-button');
  allButtons.forEach(button => {
    button.classList.remove('active');
  });
  
  // Mostrar el contenido de la pestaña seleccionada
  document.getElementById(tabName).classList.add('active');
  
  // Agregar clase active al botón correspondiente
  event.target.classList.add('active');
}

// Establecer fecha mínima como hoy
document.addEventListener('DOMContentLoaded', function() {
  const fechaInput = document.getElementById('fecha_evento');
  const today = new Date().toISOString().split('T')[0];
  fechaInput.setAttribute('min', today);
});
</script>

</body>
</html>