<?php
ob_start();
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Pragma: no-cache");

// Verificar si el administrador ha iniciado sesión
if ($_SESSION['admin_id']) {

    // Incluir configuración de la base de datos
    require_once 'config.php';

    // Obtener todos los usuarios
    $stmt = $pdo->prepare("SELECT * FROM Usuarios");
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener todos los vehículos con la matrícula del propietario
    $stmt = $pdo->prepare("SELECT v.*, u.Matricula 
        FROM Vehiculo v 
        LEFT JOIN Usuarios u ON v.PropietarioID = u.ID");
    $stmt->execute();
    $vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener todos los registros
    $stmt = $pdo->prepare("SELECT r.*, u.NombreCompleto, v.Marca, v.Modelo 
        FROM Registro r 
        LEFT JOIN Usuarios u ON r.UsuarioID = u.ID
        LEFT JOIN Vehiculo v ON r.PlacaVehiculo = v.Placa
        ORDER BY r.Entrada DESC");
    $stmt->execute();
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Incluir la vista y pasar los datos
    require 'admin_view.php';
    
}
else {
    header("Location: login_admin.html");
    exit();
}
?>