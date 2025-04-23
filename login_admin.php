<?php
ob_start();
session_start();
require_once 'config.php';

// Procesar el formulario solo si se envió por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si se enviaron los datos del formulario
    if (!isset($_POST['matricula']) || !isset($_POST['contrasena'])) {
        header('Location: login_admin.php?error=Datos incompletos');
        exit();
    }

    $matricula = trim($_POST['matricula']);
    $contrasena = trim($_POST['contrasena']);

    // Validar que los campos no estén vacíos
    if (empty($matricula) || empty($contrasena)) {
        header('Location: login_admin.php?error=La matrícula y la contraseña son obligatorias');
        exit();
    }

    try {
        // Buscar al administrador por matrícula
        $stmt = $pdo->prepare("SELECT * FROM Administradores WHERE MatriculaAdmin = ?");
        $stmt->execute([$matricula]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si el administrador existe y la contraseña coincide
        if ($admin && $admin['ContraseñaAdmin'] === $contrasena) {
            // Guardar datos en la sesión
            $_SESSION['admin_id'] = $admin['IDAdmin'];
            $_SESSION['admin_matricula'] = $admin['MatriculaAdmin'];
            $_SESSION['admin_nombre'] = $admin['NombreAdmin'];
            
            // Redirigir al panel de administrador
            header('Location: admin.php');
            exit();
        } else {
            // Error de autenticación
            header('Location: login_admin.php?error=Matrícula o contraseña incorrectas');
            exit();
        }
    } catch (PDOException $e) {
        // Error de base de datos
        header('Location: login_admin.php?error=Error en la base de datos: ' . urlencode($e->getMessage()));
        exit();
    }
}
// Si no es POST, simplemente muestra el formulario
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Administrador</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="left">
        <img src="assets/images/logo.png" alt="Logo">
    </div>

    <div class="right">
        <div>
            <h1 class="title">Secure Parking </h1>
            <h1 class="title2">ADMIN</h1>
            <div class="raya"></div>
        </div>
        <div class="login-box">
            <h2>Iniciar Sesión - Administrador</h2>
            <?php if (isset($_GET['error'])): ?>
                <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
            <?php endif; ?>
            <form action="login_admin.php" method="post">
                <input type="text" name="matricula" placeholder="Matrícula" required>
                <input type="password" name="contrasena" placeholder="Contraseña" required>
                <button type="submit" class="myButton">Iniciar Sesión</button>
            </form>
            <a href="index.html" class="myButton">Volver</a>
        </div>
    </div>
</body>
</html>