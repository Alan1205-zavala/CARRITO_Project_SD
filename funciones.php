<?php
session_start();

// Configuración
define('BASE_URL', 'http://localhost/CARRITO_Project/CARRITO_Project_SD-1/');
define('PAYPAL_CLIENT_ID', 'TU_CLIENT_ID_PAYPAL_AQUI');

// Autoloader mejorado
spl_autoload_register(function ($class) {
    $prefix = 'api\\';
    $base_dir = __DIR__ . '/api/';

    // Verifica si la clase usa el prefijo del namespace
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Obtiene el nombre relativo de la clase
    $relative_class = substr($class, $len);

    // Reemplaza los namespace separators con directory separators
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // Si el archivo existe, lo requiere
    if (file_exists($file)) {
        require $file;
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
