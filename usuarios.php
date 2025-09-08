<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "hotel";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Mostrar reservas con info de usuario
$sql = "SELECT r.id AS reserva_id, u.usuario, u.email, u.telefono, r.fecha_entrada, r.fecha_salida, r.tipo_cuarto, r.tipo_pago, r.estado_pago 
        FROM reservas r
        JOIN usuarios u ON r.usuario_id = u.id
        ORDER BY r.id DESC";
$resultado = $conn->query($sql);

// Manejo de actualización
$mensaje = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualizar'])) {
    $reserva_id = $_POST['reserva_id'];
    $fecha_entrada = $_POST['fecha_entrada'];
    $fecha_salida = $_POST['fecha_salida'];
    $tipo_cuarto = $_POST['tipo_cuarto'];
    $tipo_pago = $_POST['tipo_pago'];
    $estado_pago = $_POST['estado_pago'];

    $sqlUpdate = "UPDATE reservas SET fecha_entrada=?, fecha_salida=?, tipo_cuarto=?, tipo_pago=?, estado_pago=? WHERE id=?";
    $stmt = $conn->prepare($sqlUpdate);
    $stmt->bind_param("sssssi", $fecha_entrada, $fecha_salida, $tipo_cuarto, $tipo_pago, $estado_pago, $reserva_id);

    if ($stmt->execute()) {
        $mensaje = "Reserva actualizada correctamente.";
    } else {
        $mensaje = "Error al actualizar la reserva.";
    }
}

// Obtener datos para edición si se pasó un ID
$reservaEditar = null;
if (isset($_GET['editar'])) {
    $idEditar = $_GET['editar'];
    $sqlEditar = "SELECT * FROM reservas WHERE id=?";
    $stmt = $conn->prepare($sqlEditar);
    $stmt->bind_param("i", $idEditar);
    $stmt->execute();
    $reservaEditar = $stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios y Reservas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef2f3;
            padding: 20px;
        }

        h2, h3 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th {
            background-color: #2c3e50;
            color: white;
            padding: 10px;
        }

        td {
            padding: 10px;
            text-align: center;
        }

        a {
            color: #2980b9;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        form {
            background-color: #ffffff;
            padding: 20px;
            margin-top: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 500px;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="date"], select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #27ae60;
            color: white;
            padding: 10px 20px;
            border: none;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #219150;
        }

        .mensaje {
            color: green;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h2>Reservas Registradas</h2>
            <a href="index.php" class="link-login">Volver a Iniciar Sesión</a><br>
 <a href="facturacion.php" class="link-login">facturacion</a>

    <?php if ($mensaje): ?>
        <p class="mensaje"><?= $mensaje ?></p>
    <?php endif; ?>

    <table>
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Entrada</th>
            <th>Salida</th>
            <th>Tipo Cuarto</th>
            <th>Tipo Pago</th>
            <th>Estado Pago</th>
            <th>Acción</th>
        </tr>
        <?php while ($fila = $resultado->fetch_assoc()): ?>
            <tr>
                <td><?= $fila['reserva_id'] ?></td>
                <td><?= $fila['usuario'] ?></td>
                <td><?= $fila['email'] ?></td>
                <td><?= $fila['telefono'] ?></td>
                <td><?= $fila['fecha_entrada'] ?></td>
                <td><?= $fila['fecha_salida'] ?></td>
                <td><?= $fila['tipo_cuarto'] ?></td>
                <td><?= $fila['tipo_pago'] ?></td>
                <td><?= $fila['estado_pago'] ?></td>
                <td><a href="usuarios.php?editar=<?= $fila['reserva_id'] ?>">Editar</a></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <?php if ($reservaEditar): ?>
        <h3>Editar Reserva ID <?= $reservaEditar['id'] ?></h3>
        <form method="POST">
            <input type="hidden" name="reserva_id" value="<?= $reservaEditar['id'] ?>">

            <label>Fecha Entrada:</label>
            <input type="date" name="fecha_entrada" value="<?= $reservaEditar['fecha_entrada'] ?>" required>

            <label>Fecha Salida:</label>
            <input type="date" name="fecha_salida" value="<?= $reservaEditar['fecha_salida'] ?>" required>

            <label>Tipo de Cuarto:</label>
            <select name="tipo_cuarto" required>
                <option <?= $reservaEditar['tipo_cuarto'] == 'Sencillo' ? 'selected' : '' ?>>Sencillo</option>
                <option <?= $reservaEditar['tipo_cuarto'] == 'Doble' ? 'selected' : '' ?>>Doble</option>
                <option <?= $reservaEditar['tipo_cuarto'] == 'Suite' ? 'selected' : '' ?>>Suite</option>
            </select>

            <label>Tipo de Pago:</label>
            <select name="tipo_pago" required>
                <option <?= $reservaEditar['tipo_pago'] == 'Efectivo' ? 'selected' : '' ?>>Efectivo</option>
                <option <?= $reservaEditar['tipo_pago'] == 'Pago móvil' ? 'selected' : '' ?>>Pago móvil</option>
            </select>

            <label>Estado de Pago:</label>
            <select name="estado_pago" required>
                <option <?= $reservaEditar['estado_pago'] == 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                <option <?= $reservaEditar['estado_pago'] == 'Pagado' ? 'selected' : '' ?>>Pagado</option>
            </select>

            <button type="submit" name="actualizar">Actualizar</button>
        </form>
    <?php endif; ?>
</body>
</html>

<?php $conn->close(); ?>
