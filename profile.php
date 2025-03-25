<?php
$seccionActiva = isset($_GET['seccion']) ? $_GET['seccion'] : 'perfil';
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
}
else {
    header("Location: login.html");
    exit();
}
?>
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
    </script>
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
            <div id="profile" class="profile-section" style="display: <?php echo ($seccionActiva === 'perfil') ? 'block' : 'none'; ?>;">
                <h1>Bienvenido, <?php echo htmlspecialchars($usuario['NombreCompleto']);?></h1>
                <table class="profile-table">
                    <tr><th>Matricula</th><td><?php echo htmlspecialchars($usuario['Matricula']); ?></td></tr>
                    <tr><th>Correo</th><td><?php echo htmlspecialchars($usuario['Correo']); ?></td></tr>
                    <tr><th>Teléfono</th><td><?php echo htmlspecialchars($usuario['Telefono']); ?></td></tr>
                    <tr><th>Carrera</th><td><?php echo htmlspecialchars($usuario['Carrera']); ?></td></tr>
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
        </div>
    </div>
</body>
</html>