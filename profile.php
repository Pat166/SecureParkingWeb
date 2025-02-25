<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.html");
    exit();
}

$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Usuario</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="profile-wrapper">
        <div class="profile-container">
            <div class="profile-header">
                <h1>Bienvenido, <?php echo htmlspecialchars($usuario['NombreCompleto']);?></h1>
            </div>
            <div class="profile-details">
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
            <a href="logout.php" class="myButton">Cerrar sesión</a>
        </div>
    </div>
</body>
</html>
