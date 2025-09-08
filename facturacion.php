<?php
session_start();
$usuario = $_SESSION['usuario'] ?? null;

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

// Eliminar factura (reserva + servicios) si se envía el ID por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
    $id = $_POST['eliminar_id'];
    $pdo->prepare("DELETE FROM servicios_reservados WHERE reserva_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM reservas WHERE id = ?")->execute([$id]);
}

// Obtener todas las reservas pagadas
$stmt = $pdo->prepare("SELECT * FROM reservas WHERE estado_pago = 'Pagado'");
$stmt->execute();
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular total general
$totalGeneral = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Facturación y Total</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: #f4f4f4;
            padding: 40px;
        }
        h1, h2 {
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #3498db;
            color: #fff;
        }
        .total, .subserv {
            font-size: 18px;
            font-weight: bold;
            color: green;
        }
        .btn-volver, .btn-eliminar, .btn-ver, .btn-pdf {
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 10px;
            margin-right: 10px;
            cursor: pointer;
            border: none;
        }
        .btn-volver:hover, .btn-eliminar:hover, .btn-ver:hover, .btn-pdf:hover {
            background: #2980b9;
        }
        .btn-eliminar {
            background: #e74c3c;
        }
        .btn-pdf {
            background: #27ae60;
        }
    </style>
</head>
<body>

<h1>Facturación del Hotel Paraíso</h1>

<?php if ($reservas): ?>
    <?php foreach ($reservas as $reserva): ?>
        <?php
            // Obtener servicios adicionales para esta reserva
            $stmtServ = $pdo->prepare("
                SELECT s.nombre, s.costo
                FROM servicios_reservados sr
                JOIN servicios s ON sr.servicio_id = s.id
                WHERE sr.reserva_id = ?
            ");
            $stmtServ->execute([$reserva['id']]);
            $servicios = $stmtServ->fetchAll(PDO::FETCH_ASSOC);

            // Calcular totales
            $totalServicios = 0;
            foreach ($servicios as $s) {
                $totalServicios += $s['costo'];
            }
            $subtotal = $reserva['costo'] + $totalServicios;
            $totalGeneral += $subtotal;
        ?>
        <table>
            <thead>
                <tr>
                    <th colspan="2">Factura - Cliente ID: <?= htmlspecialchars($reserva['usuario_id']) ?></th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Fecha Entrada</td><td><?= htmlspecialchars($reserva['fecha_entrada']) ?></td></tr>
                <tr><td>Fecha Salida</td><td><?= htmlspecialchars($reserva['fecha_salida']) ?></td></tr>
                <tr><td>Tipo de Habitación</td><td><?= htmlspecialchars($reserva['tipo_cuarto']) ?></td></tr>
                <tr><td>Tipo de Pago</td><td><?= htmlspecialchars($reserva['tipo_pago']) ?></td></tr>
                <tr><td>Fecha de Reserva</td><td><?= htmlspecialchars($reserva['fecha_reserva']) ?></td></tr>
                <tr><td>Costo Habitación</td><td>Bs <?= number_format($reserva['costo'], 2, ',', '.') ?></td></tr>

                <?php if ($servicios): ?>
                    <tr><th colspan="2">Servicios Adicionales</th></tr>
                    <?php foreach ($servicios as $servicio): ?>
                        <tr>
                            <td><?= htmlspecialchars($servicio['nombre']) ?></td>
                            <td>Bs <?= number_format($servicio['costo'], 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="subserv"><td>Total Servicios</td><td>Bs <?= number_format($totalServicios, 2, ',', '.') ?></td></tr>
                <?php endif; ?>

                <tr><td><strong>Subtotal Reserva</strong></td><td><strong>Bs <?= number_format($subtotal, 2, ',', '.') ?></strong></td></tr>
            </tbody>
        </table>

        <!-- Botones para ver y descargar PDF -->
       
        <a href="factura_detalle.php?id=<?= $reserva['id'] ?>&pdf=1" target="_blank" class="btn-pdf">Descargar PDF</a>

        <form method="POST" onsubmit="return confirm('¿Estás seguro de cancelar esta factura? Se eliminarán los datos.');" style="display:inline;">
            <input type="hidden" name="eliminar_id" value="<?= $reserva['id'] ?>">
            <button type="submit" class="btn-eliminar">Cancelar Factura</button>
        </form>
    <?php endforeach; ?>

    <p class="total">Total de ingresos: <strong>Bs <?= number_format($totalGeneral, 2, ',', '.') ?></strong></p>
<?php else: ?>
    <p>No hay reservas pagadas registradas.</p>
<?php endif; ?>

<a href="index.php" class="btn-volver">← Volver al inicio</a>

</body>
</html>