<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "hotel";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Datos del administrador
$usuario = 'admin';
$password = 'admin123';
$email = 'admin@hotel.com';
$telefono = '123456789';

// Hashear la contraseña
$hash = password_hash($password, PASSWORD_DEFAULT);

// Eliminar si ya existe
$conn->query("DELETE FROM administradores WHERE usuario = '$usuario'");

// Insertar nuevo administrador
$stmt = $conn->prepare("INSERT INTO administradores (usuario, password, email, telefono) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $usuario, $hash, $email, $telefono);

if ($stmt->execute()) {
    echo "✅ Administrador creado correctamente.<br>Usuario: <strong>admin</strong><br>Contraseña: <strong>admin123</strong>";
} else {
    echo "❌ Error al crear administrador: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
