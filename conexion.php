<?php
$conexion = new mysqli("localhost", "root", "", "hotel");

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>
