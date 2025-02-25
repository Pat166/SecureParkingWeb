# Secure Parking Web

Este proyecto es una aplicación web para gestionar el estacionamiento seguro.

## Estructura del Proyecto

- `assets/`: Contiene recursos estáticos como imágenes y archivos CSS.
  - `images/`: Contiene las imágenes utilizadas en el proyecto.
  - `css/`: Contiene los archivos CSS para el estilo del proyecto.
- `config/`: Contiene archivos de configuración.
- `index.html`: Página de inicio.
- `login.html`: Página de inicio de sesión.
- `login.php`: Script para manejar el inicio de sesión.
- `profile.php`: Página de perfil del usuario.
- `logout.php`: Script para cerrar sesión.

## Configuración

1. Clona el repositorio en tu servidor o máquina local.
2. Configura la base de datos en `config/config.php` con tus credenciales de base de datos.
3. Asegúrate de que el servidor web tenga acceso a los archivos y permisos adecuados.

## Uso

1. Abre `index.html` en tu navegador.
2. Inicia sesión con tus credenciales en `login.html`.
3. Accede a tu perfil en `profile.php` y gestiona tu cuenta.
4. Cierra sesión utilizando `logout.php`.

## Ejemplo de Configuración de Base de Datos

Asegúrate de actualizar `config/config.php` con tus credenciales de base de datos:

```php
<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'secure_parking');

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>