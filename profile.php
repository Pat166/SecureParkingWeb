<?php
$seccionActiva = isset($_GET['seccion']) ? $_GET['seccion'] : 'perfil';
ob_start();
session_start();
if ($_SESSION['usuario']) {
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
    
    // Obtener los registros de entradas y salidas del usuario
    $stmt = $pdo->prepare("SELECT PlacaVehiculo, Entrada, Salida FROM Registro WHERE UsuarioID = :usuario_id ORDER BY Entrada DESC");
    $stmt->bindParam(':usuario_id', $usuario['ID'], PDO::PARAM_INT);
    $stmt->execute();
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $imagenBase64 = '';
    if ($usuarioImagen && !empty($usuarioImagen['Fotografia'])) {
        $imagenBase64 = base64_encode($usuarioImagen['Fotografia']);
    }

    // Manejo de actualización de configuración con validaciones
    if ($seccionActiva === 'settings' && isset($_POST['submit_settings'])) {
        $contrasena_actual = $_POST['contrasena_actual'] ?? '';
        $nueva_contrasena = $_POST['nueva_contrasena'] ?? '';
        $nuevo_correo = trim($_POST['nuevo_correo'] ?? '');
        $nuevo_telefono = trim($_POST['nuevo_telefono'] ?? '');

        $errors = [];

        // Validar contraseña si se intenta cambiar
        if (!empty($nueva_contrasena)) {
            if (empty($contrasena_actual)) {
                $errors[] = "Por favor, ingresa tu contraseña actual para cambiarla.";
            } else {
                $stmt = $pdo->prepare("SELECT ContraseñaHash FROM Usuarios WHERE ID = ?");
                $stmt->execute([$usuario['ID']]);
                $hash_actual = $stmt->fetchColumn();

                if (!password_verify($contrasena_actual, $hash_actual)) {
                    $errors[] = "La contraseña actual es incorrecta.";
                } else {
                    // Validar nueva contraseña
                    if (strlen($nueva_contrasena) < 8 || !preg_match("/[A-Z]/", $nueva_contrasena) || 
                        !preg_match("/[a-z]/", $nueva_contrasena) || !preg_match("/[0-9]/", $nueva_contrasena)) {
                        $errors[] = "La nueva contraseña debe tener al menos 8 caracteres, incluyendo una mayúscula, una minúscula y un número.";
                    }
                }
            }
        }

        // Validar correo
        if (!empty($nuevo_correo)) {
            if (!filter_var($nuevo_correo, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "El correo electrónico no es válido.";
            } elseif (strlen($nuevo_correo) > 100) {
                $errors[] = "El correo no puede exceder los 100 caracteres.";
            }
        }

        // Validar teléfono
        if (!empty($nuevo_telefono)) {
            if (!preg_match("/^[0-9]{10,15}$/", $nuevo_telefono)) {
                $errors[] = "El teléfono debe contener solo números y tener entre 10 y 15 dígitos.";
            }
        }

        // Si no hay errores, proceder con las actualizaciones
        if (empty($errors)) {
            try {
                if (!empty($nueva_contrasena)) {
                    $nueva_contrasena_hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE Usuarios SET ContraseñaHash = ? WHERE ID = ?");
                    $stmt->execute([$nueva_contrasena_hash, $usuario['ID']]);
                    $_SESSION['message'] = "Contraseña actualizada exitosamente.";
                }

                if (!empty($nuevo_correo) && $nuevo_correo !== $usuario['Correo']) {
                    $stmt = $pdo->prepare("UPDATE Usuarios SET Correo = ? WHERE ID = ?");
                    $stmt->execute([$nuevo_correo, $usuario['ID']]);
                    $_SESSION['message'] = isset($_SESSION['message']) ? $_SESSION['message'] . " Correo actualizado exitosamente." : "Correo actualizado exitosamente.";
                    $_SESSION['usuario']['Correo'] = $nuevo_correo;
                }

                if (!empty($nuevo_telefono) && $nuevo_telefono !== $usuario['Telefono']) {
                    $stmt = $pdo->prepare("UPDATE Usuarios SET Telefono = ? WHERE ID = ?");
                    $stmt->execute([$nuevo_telefono, $usuario['ID']]);
                    $_SESSION['message'] = isset($_SESSION['message']) ? $_SESSION['message'] . " Teléfono actualizado exitosamente." : "Teléfono actualizado exitosamente.";
                    $_SESSION['usuario']['Telefono'] = $nuevo_telefono;
                }

                if (!isset($_SESSION['message'])) {
                    $_SESSION['message'] = "No se realizaron cambios.";
                }
            } catch (PDOException $e) {
                $errors[] = "Error al actualizar la configuración: " . $e->getMessage();
            }
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode("<br>", $errors);
        }
        
    }
} else {
    header("Location: login.html");
    exit();
}
?>
<?php if (isset($_GET['error']) && $_GET['error'] === 'placa_existente'): ?>
    <div class="error-message">
        <p>La placa ingresada ya está registrada. Por favor, verifica los datos.</p>
    </div>
<?php endif; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="Last-Modified" content="0">
    <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Usuario</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            display: flex;
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
        .settings-form {
            max-width: 400px;
            margin: 20px auto;
        }
        .settings-form label {
            display: block;
            margin: 15px 0 5px;
            font-weight: bold;
            color: #333;
        }
        .settings-form input[type="password"],
        .settings-form input[type="email"],
        .settings-form input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }
        .settings-form input[type="password"]:focus,
        .settings-form input[type="email"]:focus,
        .settings-form input[type="text"]:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }
        .settings-form button {
            margin-top: 20px;
            width: 100%;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            text-align: center;
        }
        @media (max-width: 600px) {
            .profile{
                display: block;
            }
        }
    </style>
</head>
<body>
    <div class="headerProfile">
        <div class="LogoEscrito" style="margin-left: 30px;">
            <h1 class="title">Secure Parking </h1>
            <h1 class="title2">WEB</h1>
            <div class="raya"></div>
        </div>
        <button class="btn" onclick="toggleSidebar()"><i class="fa fa-bars"></i></button>
    </div>

    <div id="sidebar" class="sidebar">
        <a href="javascript:void(0)" class="closebtn" onclick="toggleSidebar()">×</a>
        <a href="profile.php?seccion=perfil">Perfil</a>
        <a href="profile.php?seccion=vehicles">Vehículos</a>
        <a href="profile.php?seccion=registros">Registros</a>
        <a href="profile.php?seccion=settings">Configuración</a>
        <a href="logout.php">Cerrar sesión</a>
    </div>

    <div class="profile-wrapper">
        <div class="profile-container">
            <h1>Bienvenido, <?php echo htmlspecialchars($usuario['NombreCompleto']);?></h1>
            <div id="profile" class="profile-section" style="display: <?php echo ($seccionActiva === 'perfil') ? 'flex' : 'none'; ?>;">
                <table class="profile-table">
                    <tr><th>Tipo de Usuario</th><td><?php echo htmlspecialchars($usuario['TipoUsuario']); ?></td></tr>
                    <tr><th>Matrícula</th><td><?php echo htmlspecialchars($usuario['Matricula']); ?></td></tr>
                    <tr><th>Correo</th><td><?php echo htmlspecialchars($usuario['Correo']); ?></td></tr>
                    <tr><th>Teléfono</th><td><?php echo htmlspecialchars($usuario['Telefono']); ?></td></tr>
                    <?php if ($usuario['TipoUsuario'] === 'Estudiante'): ?>
                        <tr><th>Carrera</th><td><?php echo htmlspecialchars($usuario['Carrera'] ?? 'No especificado'); ?></td></tr>
                        <tr><th>Estatus</th><td><?php echo $usuario['Estatus'] ? 'Activo' : 'Inactivo'; ?></td></tr>
                    <?php endif; ?>
                </table>
                <div class="profile-image-container">
                    <?php if (!empty($imagenBase64)): ?>
                        <img src="data:image/jpeg;base64,<?php echo $imagenBase64; ?>" alt="Fotografía del Usuario" class="profile-image">
                    <?php else: ?>
                        <img src="assets/images/default.png" alt="Fotografía del Usuario" class="profile-image">
                    <?php endif; ?>
                </div>
            </div>

            <div id="vehicles" class="profile-section" style="display: <?php echo ($seccionActiva === 'vehicles') ? 'block' : 'none'; ?>;">
                <h2>Vehículos Registrados</h2>
                <form method="post" action="agregar_vehiculo.php">
                    <label for="placa">Placa:</label>
                    <input type="text" id="placa" name="placa" required>
                    <label for="marca">Marca:</label>
                    <input type="text" id="marca" name="marca" required>
                    <label for="modelo">Modelo:</label>
                    <input type="text" id="modelo" name="modelo" required>
                    <label for="color">Color:</label>
                    <input type="text" id="color" name="color" required>
                    <label for="tipo">Tipo:</label>
                    <input type="text" id="tipo" name="tipo" required>
                    <button type="submit" class="myButton">Agregar Vehículo</button>
                </form>
                <?php if (count($vehiculos) > 0): ?>
                    <table class="profile-table">
                        <tr><th>Placa</th><th>Marca</th><th>Modelo</th><th>Color</th><th>Tipo</th><th>Acciones</th></tr>
                        <?php foreach ($vehiculos as $vehiculo): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($vehiculo['Placa']); ?></td>
                                <td><?php echo htmlspecialchars($vehiculo['Marca']); ?></td>
                                <td><?php echo htmlspecialchars($vehiculo['Modelo']); ?></td>
                                <td><?php echo htmlspecialchars($vehiculo['Color']); ?></td>
                                <td><?php echo htmlspecialchars($vehiculo['Tipo']); ?></td>
                                <td>
                                    <form method="post" action="eliminar_vehiculo.php" style="display:inline;">
                                        <input type="hidden" name="vehiculo_id" value="<?php echo $vehiculo['IDVehiculo']; ?>">
                                        <button type="submit" class="myButton">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?><p>No tienes vehículos registrados.</p><?php endif; ?>
            </div>

            <div id="registros" class="profile-section" style="display: <?php echo ($seccionActiva === 'registros') ? 'block' : 'none'; ?>;">
                <h2>Registros de Entradas y Salidas</h2>
                <?php if (count($registros) > 0): ?>
                    <table class="profile-table">
                        <tr><th>Placa</th><th>Entrada</th><th>Salida</th></tr>
                        <?php foreach ($registros as $registro): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($registro['PlacaVehiculo']); ?></td>
                                <td><?php echo htmlspecialchars($registro['Entrada']); ?></td>
                                <td><?php echo $registro['Salida'] ? htmlspecialchars($registro['Salida']) : 'Aún en el estacionamiento'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <p>No tienes registros de entradas o salidas.</p>
                <?php endif; ?>
            </div>

            <div id="settings" class="profile-section" style="display: <?php echo ($seccionActiva === 'settings') ? 'block' : 'none'; ?>;">
                <h2>Configuración</h2>
                <?php if (isset($_SESSION['error'])): ?>
                    <p style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
                <?php endif; ?>
                <?php if (isset($_SESSION['message'])): ?>
                    <p style="color: green;"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
                <?php endif; ?>
                <form method="post" action="profile.php?seccion=settings" class="settings-form" onsubmit="return validateForm()">
                    <h3>Cambiar Contraseña</h3>
                    <label for="contrasena_actual">Contraseña Actual:</label>
                    <input type="password" id="contrasena_actual" name="contrasena_actual" placeholder="Ingresa tu contraseña actual">
                    <label for="nueva_contrasena">Nueva Contraseña:</label>
                    <input type="password" id="nueva_contrasena" name="nueva_contrasena" placeholder="Ingresa tu nueva contraseña">

                    <h3>Actualizar Contacto</h3>
                    <label for="nuevo_correo">Correo Electrónico:</label>
                    <input type="email" id="nuevo_correo" name="nuevo_correo" value="<?php echo htmlspecialchars($usuario['Correo']); ?>" placeholder="Nuevo correo" required maxlength="100">
                    <label for="nuevo_telefono">Teléfono:</label>
                    <input type="text" id="nuevo_telefono" name="nuevo_telefono" value="<?php echo htmlspecialchars($usuario['Telefono']); ?>" placeholder="Nuevo teléfono" required pattern="[0-9]{10,15}" maxlength="15">

                    <button type="submit" name="submit_settings" class="myButton">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            if (sidebar.style.right === "0px") {
                sidebar.style.right = "-250px";
            } else {
                sidebar.style.right = "0px";
            }
        }

        function showSection(sectionId) {
            var sections = document.querySelectorAll('.profile-section');
            sections.forEach(function(section) {
                section.style.display = 'none';
            });
            document.getElementById(sectionId).style.display = 'block';
            toggleSidebar();
        }

        function validateForm() {
            let errors = [];
            const contrasenaActual = document.getElementById('contrasena_actual').value;
            const nuevaContrasena = document.getElementById('nueva_contrasena').value;
            const correo = document.getElementById('nuevo_correo').value;
            const telefono = document.getElementById('nuevo_telefono').value;

            // Validar contraseña si se ingresa una nueva
            if (nuevaContrasena) {
                if (!contrasenaActual) {
                    errors.push("Ingresa tu contraseña actual para cambiarla.");
                } else {
                    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
                    if (!passwordRegex.test(nuevaContrasena)) {
                        errors.push("La nueva contraseña debe tener al menos 8 caracteres, incluyendo una mayúscula, una minúscula y un número.");
                    }
                }
            }

            // Validar correo
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (correo && !emailRegex.test(correo)) {
                errors.push("El correo electrónico no es válido.");
            }
            if (correo.length > 100) {
                errors.push("El correo no puede exceder los 100 caracteres.");
            }

            // Validar teléfono
            const phoneRegex = /^[0-9]{10,15}$/;
            if (telefono && !phoneRegex.test(telefono)) {
                errors.push("El teléfono debe contener solo números y tener entre 10 y 15 dígitos.");
            }

            if (errors.length > 0) {
                alert(errors.join("\n"));
                return false;
            }
            return true;
        }
    </script>
</body>
</html>