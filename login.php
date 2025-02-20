<?php
session_start();

// Datos de conexión
$host = 'bqstuhbox6cpmwqjsqpa-mysql.services.clever-cloud.com';
$dbname = 'bqstuhbox6cpmwqjsqpa';
$user = 'uhg59mqvopfuphfo';
$password = 'TUJm46YO4EL3gLO7ld4O';
$port = 3306;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;port=$port", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!isset($_POST['matricula']) || !isset($_POST['password'])) {
            die("Error: No se recibieron los datos del formulario.");
        }

        $matricula = $_POST['matricula'];
        $password = $_POST['password'];

        // Buscar usuario en la base de datos
        $stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE Matricula = :matricula");
        $stmt->bindParam(':matricula', $matricula, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            echo "<script>alert('Matrícula o contraseña incorrectas'); window.location.href='login.html';</script>";
            exit();
        }

        // Verificar contraseña
        if (password_verify($password, $usuario['ContraseñaHash'])) {
            $_SESSION['usuario'] = $usuario;
            header("Location: profile.php");
            exit();
        } else {
            echo "<script>alert('Matrícula o contraseña incorrectas'); window.location.href='login.html';</script>";
        }
    }
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
