<?php
header("Content-Type: application/json");
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/ProductoController.php';
require_once __DIR__ . '/controllers/CarritoController.php';
require_once __DIR__ . '/controllers/ClienteController.php';

try {
    $db = (new Database())->getConnection();

    $request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];
    $params = $_REQUEST;
    $data = json_decode(file_get_contents('php://input'), true) ?? [];

    // Routing
    switch (true) {
        // Autenticación
        case preg_match('/\/api\/auth\/register$/', $request) && $method === 'POST':
            $controller = new AuthController($db);
            echo json_encode($controller->register($data));
            break;

        case preg_match('/\/api\/auth\/login$/', $request) && $method === 'POST':
            $controller = new AuthController($db);
            echo json_encode($controller->login($data['email'] ?? '', $data['password'] ?? ''));
            break;

        // Productos
        case preg_match('/\/api\/productos$/', $request) && $method === 'GET':
            $controller = new ProductoController($db);
            echo json_encode($controller->getAllProducts());
            break;

        // Carrito
        case preg_match('/\/api\/carrito$/', $request) && $method === 'GET':
            if (!isset($params['id_cliente'])) {
                throw new Exception('Se requiere id_cliente');
            }
            $controller = new CarritoController($db);
            echo json_encode($controller->getCart($params['id_cliente']));
            break;

        case preg_match('/\/api\/carrito\/add$/', $request) && $method === 'POST':
            $controller = new CarritoController($db);
            echo json_encode($controller->addToCart(
                $data['id_cliente'] ?? null,
                $data['id_producto'] ?? null,
                $data['cantidad'] ?? 1
            ));
            break;

        case preg_match('/\/api\/carrito\/remove$/', $request) && $method === 'POST':
            $controller = new CarritoController($db);
            echo json_encode($controller->removeFromCart(
                $data['id_cliente'] ?? null,
                $data['id_producto'] ?? null
            ));
            break;

        case preg_match('/\/api\/carrito\/checkout$/', $request) && $method === 'POST':
            $controller = new CarritoController($db);
            echo json_encode($controller->checkout(
                $data['id_cliente'] ?? null,
                $data['payment_data'] ?? []
            ));
            break;

        // Clientes
        case preg_match('/\/api\/clientes\/me$/', $request) && $method === 'GET':
            if (!isset($params['id_cliente'])) {
                throw new Exception('Se requiere id_cliente');
            }
            $controller = new ClienteController($db);
            echo json_encode($controller->getProfile($params['id_cliente']));
            break;

        default:
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Endpoint no encontrado']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
