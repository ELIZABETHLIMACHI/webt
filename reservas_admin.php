<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== "admin") {
    header("Location: login.php");
    exit();
}

$host = "localhost";
$user = "root";
$pass = "";
$db = "hotel";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$sql = "SELECT r.id, u.usuario, r.fecha_entrada, r.fecha_salida, r.tipo_cuarto, r.tipo_pago, r.costo, r.estado_pago 
        FROM reservas r
        JOIN usuarios u ON r.usuario_id = u.id
        ORDER BY r.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservas Registradas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .logout {
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        .logout a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>Listado de Reservas</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Fecha Entrada</th>
                <th>Fecha Salida</th>
                <th>Tipo de Cuarto</th>
                <th>Tipo de Pago</th>
                <th>Costo (Bs)</th>
                <th>Estado de Pago</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['usuario']) ?></td>
                    <td><?= $row['fecha_entrada'] ?></td>
                    <td><?= $row['fecha_salida'] ?></td>
                    <td><?= $row['tipo_cuarto'] ?></td>
                    <td><?= $row['tipo_pago'] ?></td>
                    <td><?= number_format($row['costo'], 2) ?></td>
                    <td><?= $row['estado_pago'] ?: 'Pendiente' ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="logout">
        <a href="logout.php">Cerrar sesión</a>
    </div>
</body>
</html>
