<?php
require_once 'config.php';

// Verificar si se recibió el ID del usuario
if (!isset($_POST['usuario_id']) || empty($_POST['usuario_id'])) {
    header('Location: profile_Administrador.php?error=No se especificó un usuario para eliminar');
    exit();
}

$usuario_id = $_POST['usuario_id'];

try {
    // Iniciar transacción
    $pdo->beginTransaction();

    // Eliminar registros dependientes en Vehiculo
    $stmt = $pdo->prepare("DELETE FROM Vehiculo WHERE PropietarioID = ?");
    $stmt->execute([$usuario_id]);

    // Eliminar el usuario (los registros en Registro se eliminan por ON DELETE CASCADE)
    $stmt = $pdo->prepare("DELETE FROM Usuarios WHERE ID = ?");
    $stmt->execute([$usuario_id]);

    // Confirmar transacción
    $pdo->commit();

    // Redirigir con mensaje de éxito
    header('Location: profile_Administrador.php?message=Usuario eliminado correctamente');
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $pdo->rollBack();
    // Redirigir con mensaje de error detallado
    header('Location: profile_Administrador.php?error=Error al eliminar usuario: ' . urlencode($e->getMessage()));
}

exit();
?>