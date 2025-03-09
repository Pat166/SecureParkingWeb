<?php
require_once 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

// Obtener la imagen desde la base de datos
$stmt = $pdo->prepare("SELECT Fotografia FROM Usuarios WHERE ID = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($usuario && !empty($usuario['Fotografia'])) {
    $imagenBase64 = base64_encode($usuario['Fotografia']);
    echo '<img src="data:image/jpeg;base64,' . $imagenBase64 . '" width="10" height="10"/>';
} else {
    echo "No se encontró la imagen.";
}
} else {
    echo "No se recibió el ID del usuario.";
}
?>