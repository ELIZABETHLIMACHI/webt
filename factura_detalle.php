<?php
require_once 'dompdf/autoload.inc.php'; // Asegúrate de que dompdf esté en esa carpeta
use Dompdf\Dompdf;

session_start();

if (!isset($_SESSION['usuario_id'])) {
    die("No se ha identificado el usuario. Por favor, inicia sesión.");
}

$usuario_id = $_SESSION['usuario_id'];
$usuario_rol = $_SESSION['usuario_rol'] ?? '';

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

if (!isset($_GET['id'])) {
    die("No se especificó la factura.");
}

$id = (int)$_GET['id'];

// Obtener datos de la reserva
if ($usuario_rol === 'admin') {
    $stmt = $pdo->prepare("SELECT * FROM reservas WHERE id = ?");
    $stmt->execute([$id]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM reservas WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id, $usuario_id]);
}
$reserva = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reserva) {
    die("Factura no encontrada o no tienes permiso para verla.");
}

// Obtener nombre del cliente desde tabla usuarios
$stmtCliente = $pdo->prepare("SELECT usuario FROM usuarios WHERE id = ?");
$stmtCliente->execute([$reserva['usuario_id']]);
$usuarioCliente = $stmtCliente->fetchColumn();
if (!$usuarioCliente) {
    $usuarioCliente = "Desconocido";
}

// Obtener servicios adicionales
$stmtServ = $pdo->prepare("
    SELECT s.nombre, s.costo
    FROM servicios_reservados sr
    JOIN servicios s ON sr.servicio_id = s.id
    WHERE sr.reserva_id = ?
");
$stmtServ->execute([$id]);
$servicios = $stmtServ->fetchAll(PDO::FETCH_ASSOC);

$totalServicios = 0;
foreach ($servicios as $s) {
    $totalServicios += $s['costo'];
}
$subtotal = $reserva['costo'] + $totalServicios;

// Descargar PDF
if (isset($_GET['pdf'])) {
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; }
            h1, h3 { color: #2c3e50; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #3498db; color: white; }
        </style>
    </head>
    <body>
        <h1>Factura Hotel Paraíso</h1>
        <p><strong>Cliente:</strong> <?= utf8_decode(htmlspecialchars($usuarioCliente)) ?></p>
        <p><strong>Fecha Entrada:</strong> <?= $reserva['fecha_entrada'] ?></p>
        <p><strong>Fecha Salida:</strong> <?= $reserva['fecha_salida'] ?></p>
        <p><strong>Tipo de Habitación:</strong> <?= utf8_decode($reserva['tipo_cuarto']) ?></p>
        <p><strong>Tipo de Pago:</strong> <?= utf8_decode($reserva['tipo_pago']) ?></p>
        <p><strong>Fecha de Reserva:</strong> <?= $reserva['fecha_reserva'] ?></p>
        <p><strong>Costo Habitación:</strong> Bs <?= number_format($reserva['costo'], 2, ',', '.') ?></p>

        <?php if ($servicios): ?>
            <h3>Servicios Adicionales</h3>
            <table>
                <thead>
                    <tr><th>Servicio</th><th>Costo (Bs)</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($servicios as $servicio): ?>
                        <tr>
                            <td><?= utf8_decode($servicio['nombre']) ?></td>
                            <td><?= number_format($servicio['costo'], 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td><strong>Total Servicios</strong></td>
                        <td><strong><?= number_format($totalServicios, 2, ',', '.') ?></strong></td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>

        <h3>Total a Pagar: Bs <?= number_format($subtotal, 2, ',', '.') ?></h3>
    </body>
    </html>
    <?php
    $html = ob_get_clean();

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("factura_{$id}.pdf", ["Attachment" => true]);
    exit;
}
?>
