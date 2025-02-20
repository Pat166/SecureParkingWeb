<?php
// Datos de conexión
$host = 'bqstuhbox6cpmwqjsqpa-mysql.services.clever-cloud.com';
$dbname = 'bqstuhbox6cpmwqjsqpa';
$user = 'uhg59mqvopfuphfo';
$password = 'TUJm46YO4EL3gLO7ld4O';
$port = 3306;

try {
    // Conectar a MySQL con PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;port=$port", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para obtener los usuarios
    $stmt = $pdo->query('SELECT * FROM Usuarios');

    // Obtener los resultados
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mostrar los datos en una tabla HTML
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Matrícula</th>
                <th>Nombre</th>
                <th>Correo</th>
            </tr>";

    foreach ($usuarios as $usuario) {
        echo "<tr>
                <td>{$usuario['ID']}</td>
                <td>{$usuario['Matricula']}</td>
                <td>{$usuario['NombreCompleto']}</td>
                <td>{$usuario['Correo']}</td>
              </tr>";
    }

    echo "</table>";

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
