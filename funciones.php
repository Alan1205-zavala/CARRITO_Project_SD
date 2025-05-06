<?php
session_start();

// Configuración
define('BASE_PATH', __DIR__);
define('BASE_URL', 'http://localhost/proyecto/');
define('PAYPAL_CLIENT_ID', 'TU_CLIENT_ID_PAYPAL_AQUI');
define('PAYPAL_SANDBOX', true); // Cambiar a false para producción
define('PAYPAL_SECRET', 'TU_SECRET_SANDBOX_AQUI');

// Autoloader mejorado
spl_autoload_register(function ($class) {
    $prefixes = [
        'api\\' => __DIR__ . '/api/',
        '' => __DIR__ . '/api/' // Para clases en el root como Database
    ];

    foreach ($prefixes as $prefix => $base_dir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

function getPayPalClientId()
{
    return PAYPAL_CLIENT_ID;
}

function usuarioLogueado()
{
    return isset($_SESSION['user']);
}

function conectarDB()
{
    try {
        $db = new PDO('mysql:host=localhost;dbname=tienda_online;charset=utf8', 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}

function redirect($url)
{
    header("Location: $url");
    exit;
}

function jsonResponse($success, $message = '', $data = [])
{
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}
