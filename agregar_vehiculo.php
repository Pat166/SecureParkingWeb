<!-- filepath: /c:/Users/adria/Documents/Python/Python/IAPark/SecureParkingWeb/agregar_vehiculo.php -->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.html");
    exit();
}

require_once 'config.php';

$usuario = $_SESSION['usuario'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $placa = $_POST['placa'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $color = $_POST['color'];
    $tipo = $_POST['tipo'];

    $stmt = $pdo->prepare("INSERT INTO Vehiculo (Placa, Marca, Modelo, Color, Tipo, PropietarioID) VALUES (:placa, :marca, :modelo, :color, :tipo, :propietario_id)");
    $stmt->bindParam(':placa', $placa);
    $stmt->bindParam(':marca', $marca);
    $stmt->bindParam(':modelo', $modelo);
    $stmt->bindParam(':color', $color);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':propietario_id', $usuario['ID']);
    $stmt->execute();

    header("Location: profile.php?seccion=vehicles");
    exit();
}
?>