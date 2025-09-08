<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
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

$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Actualizar la última reserva de este usuario como 'Pagado'
    $sql = "UPDATE reservas SET estado_pago = 'Pagado' 
            WHERE usuario_id = ? 
            ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    if ($stmt->execute()) {
        echo "<script>
                alert('Pago realizado correctamente.');
                window.location.href = 'index.php?pago=ok';
              </script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago con QR</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
            padding-top: 50px;
        }

        .qr-container {
            background: #fff;
            display: inline-block;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        img {
            width: 250px;
            height: 250px;
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
        }

        form {
            margin-top: 20px;
        }

        button {
            padding: 12px 25px;
            font-size: 16px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="qr-container">
        <h2>Realice su pago</h2>
        <img src="./imagenes/qr/qr.png" alt="Código QR para pagar" />
        <p>Escanee el código QR con su aplicación bancaria para completar el pago.</p>

        <form method="POST">
            <button type="submit">Ya pagué</button>
        </form>
    </div>
</body>
</html>
