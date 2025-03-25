<?php
require_once 'config.php';

session_start();

// Verificar si se recibió el ID del vehículo
if (!isset($_POST['vehiculo_id']) && !isset($_POST['submit'])) {
    header("Location: profile_Administrador.php?error=No se especificó un vehículo para modificar");
    exit();
}

$vehiculo_id = isset($_POST['vehiculo_id']) ? $_POST['vehiculo_id'] : null;

// Si el formulario de modificación fue enviado
if (isset($_POST['submit'])) {
    $placa = trim($_POST['placa']);
    $marca = trim($_POST['marca']);
    $modelo = trim($_POST['modelo']);
    $color = trim($_POST['color']);

    // Validación básica
    if (empty($placa) || empty($marca) || empty($modelo) || empty($color)) {
        $error = "Todos los campos son obligatorios.";
    } elseif (strlen($placa) > 10) { // Ajusta según tus reglas
        $error = "La placa no puede tener más de 10 caracteres.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE Vehiculo SET Placa = ?, Marca = ?, Modelo = ?, Color = ? WHERE IDVehiculo = ?");
            $stmt->execute([$placa, $marca, $modelo, $color, $vehiculo_id]);
            header("Location: profile_Administrador.php?message=Vehículo modificado exitosamente");
            exit();
        } catch (PDOException $e) {
            $error = "Error al modificar vehículo: " . $e->getMessage();
        }
    }
} else {
    // Obtener datos del vehículo para mostrar en el formulario
    try {
        $stmt = $pdo->prepare("SELECT * FROM Vehiculo WHERE IDVehiculo = ?");
        $stmt->execute([$vehiculo_id]);
        $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$vehiculo) {
            header("Location: profile_Administrador.php?error=Vehículo no encontrado");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: profile_Administrador.php?error=Error al obtener vehículo: " . urlencode($e->getMessage()));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="Last-Modified" content="0">
    <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <title>Modificar Vehículo</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .admin-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .admin-container h2 {
            margin-bottom: 20px;
        }
        .admin-container label {
            display: block;
            margin: 10px 0 5px;
        }
        .admin-container input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .myButton {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
        }
        .myButton[type="submit"] {
            background-color: #2196F3;
            color: white;
        }
        .myButton[type="submit"]:hover {
            background-color: #1976D2;
        }
        .myButton[href] {
            background-color: #f44336;
            color: white;
        }
        .myButton[href]:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h2>Modificar Vehículo</h2>
        <?php if (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="post" action="modificar_vehiculo.php">
            <input type="hidden" name="vehiculo_id" value="<?php echo htmlspecialchars($vehiculo['IDVehiculo']); ?>">
            <label>Placa:</label>
            <input type="text" name="placa" value="<?php echo htmlspecialchars($vehiculo['Placa']); ?>" required>
            <label>Marca:</label>
            <input type="text" name="marca" value="<?php echo htmlspecialchars($vehiculo['Marca']); ?>" required>
            <label>Modelo:</label>
            <input type="text" name="modelo" value="<?php echo htmlspecialchars($vehiculo['Modelo']); ?>" required>
            <label>Color:</label>
            <input type="text" name="color" value="<?php echo htmlspecialchars($vehiculo['Color']); ?>" required>
            <button type="submit" name="submit" class="myButton">Guardar Cambios</button>
            <a href="profile_Administrador.php" class="myButton">Cancelar</a>
        </form>
    </div>
</body>
</html>