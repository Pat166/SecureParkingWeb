<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
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
    <link rel="stylesheet" href="css.css">
</head>
<body>
    <h1>Bienvenido, <?php echo htmlspecialchars($usuario['NombreCompleto']); ?></h1>
    <table border="1">
        <tr><th>ID</th><td><?php echo $usuario['ID']; ?></td></tr>
        <tr><th>Matrícula</th><td><?php echo $usuario['Matricula']; ?></td></tr>
        <tr><th>Nombre</th><td><?php echo $usuario['NombreCompleto']; ?></td></tr>
        <tr><th>Correo</th><td><?php echo $usuario['Correo']; ?></td></tr>
    </table>
    <br>
    <a href="logout.php">Cerrar sesión</a>
</body>
</html>
