<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="Last-Modified" content="0">
    <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <title>Perfil del Administrador</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Estilos para la barra lateral (ahora a la derecha y azul) */
        .sidebar {
            height: 100%;
            width: 0;
            position: fixed;
            z-index: 100;
            top: 0;
            right: 0;
            background-color: #007BFF;
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 60px;
        }
        .sidebar a {
            padding: 8px 32px 8px 8px;
            text-decoration: none;
            font-size: 18px;
            color: #fff;
            display: block;
            transition: 0.3s;
        }
        .sidebar a:hover {
            color: #f1f1f1;
        }
        .sidebar .closebtn {
            position: absolute;
            top: 0;
            left: 25px;
            font-size: 36px;
            margin-right: 50px;
            color: #fff;
        }
        .headerProfile {
            padding: 10px;
            background-color: #f4f4f4;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .headerProfile .btn {
            font-size: 20px;
            cursor: pointer;
            background: none;
            border: none;
        }
        .admin-wrapper {
            margin-right: 0;
            transition: margin-right 0.5s;
            padding: 20px;
        }
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        .admin-table th {
            background-color: #4CAF50;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: bold;
        }
        .admin-table td {
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
        }
        .admin-table tr:hover {
            background-color: #f5f5f5;
            transition: background-color 0.3s;
        }
        .admin-table th, .admin-table td {
            min-width: 100px;
        }
        .admin-table .myButton {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .admin-table .myButton:nth-child(1) {
            background-color: #2196F3;
            color: white;
            margin-right: 5px;
        }
        .admin-table .myButton:nth-child(1):hover {
            background-color: #1976D2;
        }
        .admin-table .myButton:nth-child(2) {
            background-color: #f44336;
            color: white;
        }
        .admin-table .myButton:nth-child(2):hover {
            background-color: #d32f2f;
        }
    </style>
    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            var wrapper = document.querySelector(".admin-wrapper");
            if (sidebar.style.width === "250px") {
                sidebar.style.width = "0";
                wrapper.style.marginRight = "0";
            } else {
                sidebar.style.width = "250px";
                wrapper.style.marginRight = "250px";
            }
        }
        function showSection(sectionId) {
            var sections = document.querySelectorAll('.admin-section');
            sections.forEach(function(section) {
                section.style.display = 'none';
            });
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
        <button class="btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
    </div>

    <div id="sidebar" class="sidebar">
        <a href="javascript:void(0)" class="closebtn" onclick="toggleSidebar()">×</a>
        <a href="javascript:void(0)" onclick="showSection('users')">Usuarios</a>
        <a href="javascript:void(0)" onclick="showSection('vehicles')">Vehículos</a>
        <a href="javascript:void(0)" onclick="showSection('records')">Registros</a>
        <a href="logout.php">Cerrar sesión</a>
    </div>

    <div class="admin-wrapper">
        <?php if (isset($_GET['message'])): ?>
            <p style="color: green;"><?php echo htmlspecialchars($_GET['message']); ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
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
                        <td><?php echo htmlentities($usuario['NombreCompleto']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['Correo']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['Telefono']); ?></td>
                        <td>
                            <form method="post" action="modificar_usuario.php" style="display:inline;">
                                <input type="hidden" name="usuario_id" value="<?php echo $usuario['ID']; ?>">
                                <button type="submit" class="myButton">Modificar</button>
                            </form>
                            <form method="post" action="eliminar_usuario.php" style="display:inline;" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
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
                        <th>Matrícula Propietario</th>
                        <th>Acciones</th>
                    </tr>
                    <?php foreach ($vehiculos as $vehiculo): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($vehiculo['IDVehiculo']); ?></td>
                        <td><?php echo htmlspecialchars($vehiculo['Placa']); ?></td>
                        <td><?php echo htmlspecialchars($vehiculo['Marca']); ?></td>
                        <td><?php echo htmlspecialchars($vehiculo['Modelo']); ?></td>
                        <td><?php echo htmlspecialchars($vehiculo['Color']); ?></td>
                        <td><?php echo htmlspecialchars($vehiculo['Matricula']); ?></td>
                        <td>
                            <form method="post" action="modificar_vehiculo.php" style="display:inline;">
                                <input type="hidden" name="vehiculo_id" value="<?php echo $vehiculo['IDVehiculo']; ?>">
                                <button type="submit" class="myButton">Modificar</button>
                            </form>
                            <form method="post" action="eliminar_vehiculo_admin.php" style="display:inline;">
                                <input type="hidden" name="vehiculo_id" value="<?php echo $vehiculo['IDVehiculo']; ?>">
                                <button type="submit" class="myButton">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <!-- Sección de Registros -->
            <div id="records" class="admin-section" style="display: none;">
                <h2>Registros de Entradas y Salidas</h2>
                <table class="admin-table">
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Vehículo</th>
                        <th>Placa</th>
                        <th>Entrada</th>
                        <th>Salida</th>
                        <th>Estado</th>
                    </tr>
                    <?php foreach ($registros as $registro): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($registro['IDRegistro']); ?></td>
                        <td><?php echo htmlspecialchars($registro['NombreCompleto']); ?></td>
                        <td><?php echo htmlspecialchars($registro['Marca'] . ' ' . $registro['Modelo']); ?></td>
                        <td><?php echo htmlspecialchars($registro['PlacaVehiculo']); ?></td>
                        <td><?php echo htmlspecialchars($registro['Entrada']); ?></td>
                        <td><?php echo $registro['Salida'] ? htmlspecialchars($registro['Salida']) : 'En curso'; ?></td>
                        <td><?php echo $registro['Salida'] ? 'Completado' : 'Activo'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>