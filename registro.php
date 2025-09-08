<?php
$mensaje = "";

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$db = "hotel";

// Crear conexión
$conn = new mysqli($host, $user, $pass, $db);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encriptar contraseña
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];

    // Preparar e insertar
    $sql = "INSERT INTO usuarios (usuario, password, email, telefono) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $usuario, $password, $email, $telefono);

    if ($stmt->execute()) {
        $mensaje = "Registro exitoso.";
    } else {
        $mensaje = "Error al registrar: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registro de Usuario - Hotel Paraíso</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #74ebd5, #ACB6E5);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: #ffffff;
            padding: 30px 25px;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            animation: fadeIn 1s ease-in-out;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"],
        input[type="tel"],
        button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
        }

        button {
            background-color: #3498db;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background-color: #2980b9;
        }

        .message {
            margin-top: 15px;
            font-size: 14px;
            text-align: center;
            padding: 10px;
            border-radius: 6px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .link-login {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #34495e;
            font-size: 14px;
        }

        .link-login:hover {
            color: #2980b9;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registro de Usuario</h2>
        <form id="register-form" method="POST" action="registro.php">
            <input type="text" name="usuario" placeholder="Usuario" required />
            <input type="password" name="password" placeholder="Contraseña" required />
            <input type="email" name="email" placeholder="Correo electrónico" required />
            <input type="tel" name="telefono" placeholder="Teléfono" />
            <button type="submit">Registrar</button>
        </form>

        <!-- Mostrar mensaje -->
        <?php if (!empty($mensaje)): ?>
            <div class="message <?= ($mensaje === 'Registro exitoso.' ? 'success' : 'error') ?>">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <a href="index.php" class="link-login">Volver a Iniciar Sesión</a>
    </div>
</body>
</html>
