<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "hotel";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Datos del nuevo administrador
$usuario = 'admin';
$password = password_hash('admin123', PASSWORD_DEFAULT); // Encripta la contraseña
$email = 'admin@hotel.com';
$telefono = '123456789';

// Inserción
$stmt = $conn->prepare("INSERT INTO administradores (usuario, password, email, telefono) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $usuario, $password, $email, $telefono);

if ($stmt->execute()) {
    echo "✅ Administrador creado correctamente.";
} else {
    echo "❌ Error: " . $stmt->error;
}
?>
