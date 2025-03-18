<!-- filepath: /c:/Users/adria/Documents/Python/Python/IAPark/SecureParkingWeb/eliminar_vehiculo.php -->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.html");
    exit();
}

require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vehiculo_id = $_POST['vehiculo_id'];

    $stmt = $pdo->prepare("DELETE FROM Vehiculo WHERE IDVehiculo = :vehiculo_id");
    $stmt->bindParam(':vehiculo_id', $vehiculo_id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: profile.php?seccion=vehicles");
    exit();
}
?>