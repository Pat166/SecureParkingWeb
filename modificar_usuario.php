<?php
require_once 'config.php';

session_start();

// Verificar si se recibió el ID del usuario
if (!isset($_POST['usuario_id']) && !isset($_POST['submit'])) {
    header("Location: profile_Administrador.php");
    exit();
}

$usuario_id = isset($_POST['usuario_id']) ? $_POST['usuario_id'] : null;

// Si el formulario de modificación fue enviado
if (isset($_POST['submit'])) {
    $nombre = $_POST['nombre'];
    $sexo = $_POST['sexo'];
    $estatus = isset($_POST['estatus']) ? 1 : 0; // Checkbox: 1 si está marcado, 0 si no
    $estatusParking = isset($_POST['estatusParking']) ? 1 : 0;
    $carrera = $_POST['carrera'] ?: null; // Puede ser NULL
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];

    // Manejo de la fotografía (si se sube una nueva)
    $fotografia = null;
    if (isset($_FILES['fotografia']) && $_FILES['fotografia']['error'] == UPLOAD_ERR_OK) {
        $fotografia = file_get_contents($_FILES['fotografia']['tmp_name']);
    }

    try {
        if ($fotografia) {
            $stmt = $pdo->prepare("UPDATE Usuarios SET NombreCompleto = ?, Sexo = ?, Estatus = ?, EstatusParking = ?, Carrera = ?, Telefono = ?, Correo = ?, Fotografia = ? WHERE ID = ?");
            $stmt->execute([$nombre, $sexo, $estatus, $estatusParking, $carrera, $telefono, $correo, $fotografia, $usuario_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE Usuarios SET NombreCompleto = ?, Sexo = ?, Estatus = ?, EstatusParking = ?, Carrera = ?, Telefono = ?, Correo = ? WHERE ID = ?");
            $stmt->execute([$nombre, $sexo, $estatus, $estatusParking, $carrera, $telefono, $correo, $usuario_id]);
        }
        $_SESSION['message'] = "Usuario modificado exitosamente.";
        header("Location: profile_Administrador.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al modificar usuario: " . $e->getMessage();
    }
} else {
    // Obtener datos del usuario para mostrar en el formulario
    try {
        $stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE ID = ?");
        $stmt->execute([$usuario_id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            $_SESSION['error'] = "Usuario no encontrado.";
            header("Location: profile_Administrador.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al obtener usuario: " . $e->getMessage();
        header("Location: profile_Administrador.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Usuario</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .admin-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"], input[type="email"], select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }
        .myButton {
            padding: 10px 20px;
            margin-right: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .myButton:hover {
            background-color: #45a049;
        }
        a.myButton {
            background-color: #f44336;
            text-decoration: none;
            display: inline-block;
        }
        a.myButton:hover {
            background-color: #da190b;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h2>Modificar Usuario</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <p style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="post" action="modificar_usuario.php" enctype="multipart/form-data">
            <input type="hidden" name="usuario_id" value="<?php echo $usuario['ID']; ?>">
            
            <label>Nombre Completo:</label>
            <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario['NombreCompleto']); ?>" required>
            
            <label>Sexo:</label>
            <select name="sexo" required>
                <option value="M" <?php echo $usuario['Sexo'] === 'M' ? 'selected' : ''; ?>>Masculino</option>
                <option value="F" <?php echo $usuario['Sexo'] === 'F' ? 'selected' : ''; ?>>Femenino</option>
            </select>
            
            <label>Estatus:</label>
            <input type="checkbox" name="estatus" <?php echo $usuario['Estatus'] ? 'checked' : ''; ?>> Activo
            
            <label>Estatus Parking:</label>
            <input type="checkbox" name="estatusParking" <?php echo $usuario['EstatusParking'] ? 'checked' : ''; ?>> Permitido
            
            <label>Carrera (opcional):</label>
            <input type="text" name="carrera" value="<?php echo htmlspecialchars($usuario['Carrera'] ?? ''); ?>">
            
            <label>Teléfono:</label>
            <input type="text" name="telefono" value="<?php echo htmlspecialchars($usuario['Telefono']); ?>" required>
            
            <label>Correo:</label>
            <input type="email" name="correo" value="<?php echo htmlspecialchars($usuario['Correo']); ?>" required>
            
            <label>Fotografía (opcional):</label>
            <input type="file" name="fotografia" accept="image/*">
            <?php if ($usuario['Fotografia']): ?>
                <p>Fotografía actual: <img src="data:image/jpeg;base64,<?php echo base64_encode($usuario['Fotografia']); ?>" width="100" alt="Fotografía actual"></p>
            <?php endif; ?>
            
            <button type="submit" name="submit" class="myButton">Guardar Cambios</button>
            <a href="profile_Administrador.php" class="myButton">Cancelar</a>
        </form>
    </div>
</body>
</html>