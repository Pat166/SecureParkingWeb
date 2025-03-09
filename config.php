
<?php
define('DB_SERVER', '162.241.62.202');
define('DB_USERNAME', 'securep6_AplicacionWeb');
define('DB_PASSWORD', 'SecureParking');
define('DB_NAME', 'securep6_EstacionamientoSeguro');
define('DB_PORT', 3306);

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";port=" . DB_PORT, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>