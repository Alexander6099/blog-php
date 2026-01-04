<?php
// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);   
error_reporting(E_ALL);

// config/database.php
define('BD_HOST', 'localhost');
define('BD_USER', 'root');
define('BD_PASS', '');
define('BD_NAME', 'blog_db');

try {
    $conexion = new mysqli(BD_HOST, BD_USER, BD_PASS, BD_NAME);
    //VALIDAR CONEXIÓN
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // CONFIGURAR CHARSET A UTF8 `PARA EVITAR PROBLEMAS DE ACENTOS
    $conexion->set_charset("utf8MB4");

    // UTF-8MB4 es una versión mejorada de UTF-8 que incluye soporte para caracteres adicionales, como emojis y ciertos símbolos.
} catch (Exception $e) {
    echo "Error en la conexión: " . $e->getMessage();
}
?>
