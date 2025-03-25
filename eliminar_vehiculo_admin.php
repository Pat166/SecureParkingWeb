<?php
require_once 'config.php';

session_start();

// Verificar si se recibió el ID del vehículo
if (!isset($_POST['vehiculo_id'])) {
    $_SESSION['error'] = "No se proporcionó un ID de vehículo.";
    header("Location: profile_Administrador.php");
    exit();
}

$vehiculo_id = $_POST['vehiculo_id'];

try {
    $stmt = $pdo->prepare("DELETE FROM Vehiculo WHERE IDVehiculo = ?");
    $stmt->execute([$vehiculo_id]);
    $_SESSION['message'] = "Vehículo eliminado exitosamente.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al eliminar vehículo: " . $e->getMessage();
}

header("Location: profile_Administrador.php");
exit();
?>