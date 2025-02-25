<!-- filepath: /c:/Users/adria/Documents/Python/Python/IAPark/SecureParkingWeb/config.php -->
<?php
define('DB_SERVER', 'bqstuhbox6cpmwqjsqpa-mysql.services.clever-cloud.com');
define('DB_USERNAME', 'uhg59mqvopfuphfo');
define('DB_PASSWORD', 'TUJm46YO4EL3gLO7ld4O');
define('DB_NAME', 'bqstuhbox6cpmwqjsqpa');
define('DB_PORT', 3306);

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";port=" . DB_PORT, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>