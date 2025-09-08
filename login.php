<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db = "hotel";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // Buscar en la tabla de administradores
    $sqlAdmin = "SELECT id, password FROM administradores WHERE usuario = ?";
    $stmtAdmin = $conn->prepare($sqlAdmin);
    $stmtAdmin->bind_param("s", $usuario);
    $stmtAdmin->execute();
    $stmtAdmin->store_result();

    if ($stmtAdmin->num_rows > 0) {
        $stmtAdmin->bind_result($id, $hash);
        $stmtAdmin->fetch();

        if (password_verify($password, $hash)) {
            $_SESSION['usuario_id'] = $id;
            $_SESSION['usuario_nombre'] = $usuario;
            $_SESSION['usuario_rol'] = "admin";
            header("Location: usuarios.php");
            exit();
        } else {
            $error = "Contraseña incorrecta.";
        }

    } else {
        // Si no es admin, buscar en usuarios normales
        $sqlUser = "SELECT id, password, rol FROM usuarios WHERE usuario = ?";
        $stmtUser = $conn->prepare($sqlUser);
        $stmtUser->bind_param("s", $usuario);
        $stmtUser->execute();
        $stmtUser->store_result();

        if ($stmtUser->num_rows > 0) {
            $stmtUser->bind_result($id, $hash, $rol);
            $stmtUser->fetch();

            if (password_verify($password, $hash)) {
                $_SESSION['usuario_id'] = $id;
                $_SESSION['usuario_nombre'] = $usuario;
                $_SESSION['usuario_rol'] = $rol;
                header("Location: reserva.php");
                exit();
            } else {
                $error = "Contraseña incorrecta.";
            }
        } else {
            $error = "Usuario no encontrado.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-box {
            background: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            margin-bottom: 25px;
            text-align: center;
            color: #333;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Iniciar Sesión</h2>
        <form method="POST">
            <input type="text" name="usuario" placeholder="Usuario" required />
            <input type="password" name="password" placeholder="Contraseña" required />
            <button type="submit">Entrar</button>
        </form>
        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
    </div>
</body>
</html>
