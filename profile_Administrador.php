<!-- filepath: c:\Users\adria\Documents\Python\Python\IAPark\SecureParkingWeb\profile_Administrador.php -->
<?php
require_once 'config.php';

// Obtener todos los usuarios
$stmt = $pdo->prepare("SELECT * FROM Usuarios");
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener todos los vehículos
$stmt = $pdo->prepare("SELECT * FROM Vehiculo");
$stmt->execute();
$vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Administrador</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <script>
        function showSection(sectionId) {
            // Ocultar todas las secciones
            var sections = document.querySelectorAll('.admin-section');
            sections.forEach(function(section) {
                section.style.display = 'none';
            });

            // Mostrar la sección seleccionada
            var sectionToShow = document.getElementById(sectionId);
            if (sectionToShow) {
                sectionToShow.style.display = 'block';
            }
        }
    </script>
</head>
<body>
    <div class="headerProfile">
        <div class="LogoEscrito" style="margin-left: 30px;">
            <h1 class="title">Secure Parking </h1>
            <h1 class="title2">ADMIN</h1>
            <div class="raya"></div>
        </div>
        <button class="btn" onclick="toggleSidebar()"><i class="fa fa-bars"></i></button>
    </div>

    <!-- Barra lateral -->
    <div id="sidebar" class="sidebar">
        <a href="javascript:void(0)" class="closebtn" onclick="toggleSidebar()">&times;</a>
        <a href="javascript:void(0)" onclick="showSection('users')">Usuarios</a>
        <a href="javascript:void(0)" onclick="showSection('vehicles')">Vehículos</a>
        <a href="logout.php">Cerrar sesión</a>
    </div>

    <div class="admin-wrapper">
        <div class="admin-container">
            <!-- Sección de Usuarios -->
            <div id="users" class="admin-section" style="display: block;">
                <h2>Usuarios Registrados</h2>
                <table class="admin-table">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Acciones</th>
                    </tr>
                    <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($usuario['ID']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['NombreCompleto']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['Correo']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['Telefono']); ?></td>
                        <td>
                            <form method="post" action="modificar_usuario.php" style="display:inline;">
                                <input type="hidden" name="usuario_id" value="<?php echo $usuario['ID']; ?>">
                                <button type="submit" class="myButton">Modificar</button>
                            </form>
                            <form method="post" action="eliminar_usuario.php" style="display:inline;">
                                <input type="hidden" name="usuario_id" value="<?php echo $usuario['ID']; ?>">
                                <button type="submit" class="myButton">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <!-- Sección de Vehículos -->
            <div id="vehicles" class="admin-section" style="display: none;">
                <h2>Vehículos Registrados</h2>
                <table class="admin-table">
                    <tr>
                        <th>ID</th>
                        <th>Placa</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Color</th>
                        <th>Propietario</th>
                        <th>Acciones</th>
                    </tr>
                    <?php foreach ($vehiculos as $vehiculo): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($vehiculo['IDVehiculo']); ?></td>
                        <td><?php echo htmlspecialchars($vehiculo['Placa']); ?></td>
                        <td><?php echo htmlspecialchars($vehiculo['Marca']); ?></td>
                        <td><?php echo htmlspecialchars($vehiculo['Modelo']); ?></td>
                        <td><?php echo htmlspecialchars($vehiculo['Color']); ?></td>
                        <td><?php echo htmlspecialchars($vehiculo['PropietarioID']); ?></td>
                        <td>
                            <form method="post" action="modificar_vehiculo.php" style="display:inline;">
                                <input type="hidden" name="vehiculo_id" value="<?php echo $vehiculo['IDVehiculo']; ?>">
                                <button type="submit" class="myButton">Modificar</button>
                            </form>
                            <form method="post" action="eliminar_vehiculo.php" style="display:inline;">
                                <input type="hidden" name="vehiculo_id" value="<?php echo $vehiculo['IDVehiculo']; ?>">
                                <button type="submit" class="myButton">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>