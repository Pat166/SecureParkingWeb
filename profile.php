<!-- filepath: /c:/Users/adria/Documents/Python/Python/IAPark/SecureParkingWeb/profile.php -->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.html");
    exit();
}

require_once 'config.php';

$usuario = $_SESSION['usuario'];

// Obtener los vehículos del usuario
$stmt = $pdo->prepare("SELECT * FROM Vehiculo WHERE PropietarioID = :propietario_id");
$stmt->bindParam(':propietario_id', $usuario['ID'], PDO::PARAM_INT);
$stmt->execute();
$vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener la imagen del usuario
$stmt = $pdo->prepare("SELECT Fotografia FROM Usuarios WHERE ID = :id");
$stmt->bindParam(':id', $usuario['ID'], PDO::PARAM_INT);
$stmt->execute();
$usuarioImagen = $stmt->fetch(PDO::FETCH_ASSOC);

$imagenBase64 = '';
if ($usuarioImagen && !empty($usuarioImagen['Fotografia'])) {
    $imagenBase64 = base64_encode($usuarioImagen['Fotografia']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Usuario</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>body { flex-direction: column; }</style>
</head>
<body>
    <div class="headerProfile">
            <h1 class="title">Secure Parking </h1>
            <h1 class="title2">WEB</h2>
            <div class="raya"></div>
    </div>
    <div class="profile-wrapper">
        <div class="profile-container">
            <div class="profile-header">
                <h1>Bienvenido, <?php echo htmlspecialchars($usuario['NombreCompleto']);?></h1>
            </div>
            <div class="profile-details">
                <div>
                    <table class="profile-table">
                        <tr>
                            <th>Matricula</th>
                            <td><?php echo htmlspecialchars($usuario['Matricula']); ?></td>
                        </tr>
                        <tr>
                            <th>Correo</th>
                            <td><?php echo htmlspecialchars($usuario['Correo']); ?></td>
                        </tr>
                        <tr>
                            <th>Teléfono</th>
                            <td><?php echo htmlspecialchars($usuario['Telefono']); ?></td>
                        </tr>
                        <tr>
                            <th>Carrera</th>
                            <td><?php echo htmlspecialchars($usuario['Carrera']); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="profile-image-container">
                    <?php if ($imagenBase64): ?>
                        <img src="data:image/jpeg;base64,<?php echo $imagenBase64; ?>" alt="Fotografía del Usuario" class="profile-image">
                    <?php else: ?>
                        <img src="assets/images/default.png" alt="Fotografía del Usuario" class="profile-image">
                    <?php endif; ?>
                </div>
            </div>
            <div class="profile-vehicles">
                <h2>Vehículos Registrados</h2>
                <?php if (count($vehiculos) > 0): ?>
                <table class="profile-table">
                    <tr>
                        <th>Placa</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Color</th>
                        <th>Tipo</th>
                    </tr>
                    <?php foreach ($vehiculos as $vehiculo): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($vehiculo['Placa']); ?></td>
                        <td><?php echo htmlspecialchars($vehiculo['Marca']); ?></td>
                        <td><?php echo htmlspecialchars($vehiculo['Modelo']); ?></td>
                        <td><?php echo htmlspecialchars($vehiculo['Color']); ?></td>
                        <td><?php echo htmlspecialchars($vehiculo['Tipo']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <?php else: ?>
                <p>No tienes vehículos registrados.</p>
                <?php endif; ?>
            </div>
            <a href="logout.php" class="myButton">Cerrar sesión</a>
        </div>
    </div>
</body>
</html>