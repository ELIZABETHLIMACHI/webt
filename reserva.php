<?php
session_start();
$usuario_id = $_SESSION['usuario_id'] ?? null;
$error = '';

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

// Obtener servicios desde la base de datos
$stmt = $pdo->query("SELECT * FROM servicios");
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha_entrada = $_POST['fecha_entrada'] ?? '';
    $fecha_salida = $_POST['fecha_salida'] ?? '';
    $tipo_cuarto = $_POST['tipo_cuarto'] ?? '';
    $tipo_pago = $_POST['tipo_pago'] ?? '';
    $serviciosSeleccionados = $_POST['servicios'] ?? [];

    if ($usuario_id && $fecha_entrada && $fecha_salida && $tipo_cuarto && $tipo_pago) {
        $costos = [
            'Sencillo' => 100.00,
            'Doble' => 180.00,
            'Suite' => 250.00,
        ];

        // Cálculo de días
        $d1 = new DateTime($fecha_entrada);
        $d2 = new DateTime($fecha_salida);
        $dias = $d2->diff($d1)->days;
        if ($dias == 0) $dias = 1;

        $costo_base = ($costos[$tipo_cuarto] ?? 0) * $dias;

        // Sumar servicios seleccionados
        $total_servicios = 0;
        foreach ($serviciosSeleccionados as $id_servicio) {
            $stmt = $pdo->prepare("SELECT costo FROM servicios WHERE id = ?");
            $stmt->execute([$id_servicio]);
            $servicio = $stmt->fetch();
            if ($servicio) {
                $total_servicios += $servicio['costo'];
            }
        }

        $costo = $costo_base + $total_servicios;
        $fecha_reserva = date('Y-m-d H:i:s');

        $stmt = $pdo->prepare("INSERT INTO reservas (usuario_id, fecha_entrada, fecha_salida, tipo_cuarto, tipo_pago, estado, estado_pago, fecha_reserva, costo) 
                               VALUES (:usuario_id, :fecha_entrada, :fecha_salida, :tipo_cuarto, :tipo_pago, 'pendiente', 'Pagado', :fecha_reserva, :costo)");
        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':fecha_entrada' => $fecha_entrada,
            ':fecha_salida' => $fecha_salida,
            ':tipo_cuarto' => $tipo_cuarto,
            ':tipo_pago' => $tipo_pago,
            ':fecha_reserva' => $fecha_reserva,
            ':costo' => $costo,
        ]);

        $reserva_id = $pdo->lastInsertId();

        // Insertar servicios seleccionados
        foreach ($serviciosSeleccionados as $id_servicio) {
            $stmt = $pdo->prepare("INSERT INTO servicios_reservados (reserva_id, servicio_id) VALUES (?, ?)");
            $stmt->execute([$reserva_id, $id_servicio]);
        }

        $_SESSION['mensaje_reserva'] = "✅ ¡Reserva realizada exitosamente!";
        header('Location: index.php');
        exit;
    } else {
        $error = "Por favor completa todos los campos y asegúrate de haber iniciado sesión.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Reservar Habitación</title>
  <style>
    body { font-family: 'Montserrat', sans-serif; background: #f2f6fa; padding: 20px; }
    .container { background: white; padding: 30px; max-width: 600px; margin: auto; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
    label { font-weight: bold; margin-top: 10px; display: block; }
    input, select { width: 100%; padding: 10px; margin-top: 5px; margin-bottom: 15px; }
    .servicios-box { margin-top: 15px; }
    .servicio-item { display: flex; justify-content: space-between; margin-bottom: 8px; }
    .total-calculado { font-weight: bold; color: #27ae60; text-align: right; }
    .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; }
    button { width: 100%; padding: 12px; background: #3498db; color: white; border: none; font-size: 16px; border-radius: 8px; cursor: pointer; }
    button:hover { background: #2980b9; }
  </style>
</head>
<body>

<div class="container">
  <h2>Reservar una Habitación</h2>

  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" id="form-reserva">
    <label>Fecha Entrada:</label>
    <input type="date" name="fecha_entrada" id="fecha_entrada" required>

    <label>Fecha Salida:</label>
    <input type="date" name="fecha_salida" id="fecha_salida" required>

    <label>Tipo de Habitación:</label>
    <select name="tipo_cuarto" id="tipo_cuarto" required>
      <option value="">-- Seleccione --</option>
      <option value="Sencillo" data-precio="100">Sencillo - 100 Bs</option>
      <option value="Doble" data-precio="180">Doble - 180 Bs</option>
      <option value="Suite" data-precio="250">Suite - 250 Bs</option>
    </select>

    <label>Tipo de Pago:</label>
    <select name="tipo_pago" required>
      <option value="">-- Seleccione --</option>
      <option value="Efectivo">Efectivo</option>
      <option value="Pago móvil">Pago móvil QR</option>
    </select>

    <div class="servicios-box">
      <label>Servicios Adicionales:</label>
      <?php foreach ($servicios as $serv): ?>
        <div class="servicio-item">
          <label>
            <input type="checkbox" class="servicio-check" data-costo="<?= $serv['costo'] ?>" name="servicios[]" value="<?= $serv['id'] ?>"> <?= htmlspecialchars($serv['nombre']) ?>
          </label>
          <span>Bs <?= number_format($serv['costo'], 2, ',', '.') ?></span>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="total-calculado" id="total-calculado">Total aproximado: Bs 0,00</div>

    <button type="submit">Confirmar Reserva</button>
  </form>
</div>

<script>
  function calcularTotal() {
    const fechaEntrada = new Date(document.getElementById('fecha_entrada').value);
    const fechaSalida = new Date(document.getElementById('fecha_salida').value);
    const tipoCuarto = document.getElementById('tipo_cuarto');
    const precioCuarto = parseFloat(tipoCuarto.selectedOptions[0]?.dataset.precio || 0);

    let dias = Math.ceil((fechaSalida - fechaEntrada) / (1000 * 60 * 60 * 24));
    if (isNaN(dias) || dias < 1) dias = 1;

    let total = precioCuarto * dias;

    document.querySelectorAll('.servicio-check:checked').forEach(cb => {
      total += parseFloat(cb.dataset.costo);
    });

    document.getElementById('total-calculado').textContent = 
      'Total aproximado: Bs ' + total.toFixed(2).replace('.', ',');
  }

  document.getElementById('fecha_entrada').addEventListener('change', calcularTotal);
  document.getElementById('fecha_salida').addEventListener('change', calcularTotal);
  document.getElementById('tipo_cuarto').addEventListener('change', calcularTotal);
  document.querySelectorAll('.servicio-check').forEach(cb => {
    cb.addEventListener('change', calcularTotal);
  });
</script>

</body>
</html>
