<?php
session_start();

// Configuración
define('BASE_URL', 'http://localhost/proyecto/');
define('PAYPAL_CLIENT_ID', 'TU_CLIENT_ID_PAYPAL_AQUI');

// Autoload para modelos
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/api/models/' . $class . '.php';
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
