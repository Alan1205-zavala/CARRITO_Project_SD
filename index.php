<?php
require_once __DIR__ . '/funciones.php';

// Cargar explícitamente Database si es necesario
require_once __DIR__ . '/CARRITO/api/config/database.php';
require_once __DIR__ . '/CARRITO/api/models/ProductoModel.php';

if (!usuarioLogueado()) {
    header('Location: login.php');
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    $productoModel = new ProductoModel($db);
    $productos = $productoModel->getAll();
} catch (Exception $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
    <script src="https://www.paypal.com/sdk/js?client-id=Aa51CNnqtD9HMRe9qHOGXgXhUzu-rb-hiiYfkFrn9nFUOLYyBsDIm2uxnhQs89LK9PjfPbK8TwzNcpmY<?= getPayPalClientId() ?>&currency=MXN"></script>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container mt-4">
        <h2>Productos Disponibles</h2>
        <div class="row" id="productosContainer">
            <?php foreach ($productos as $producto): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($producto['nombre']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($producto['descripcion'] ?? 'Sin descripción') ?></p>
                            <p class="text-success">$<?= number_format($producto['precio'], 2) ?></p>
                            <button class="btn btn-primary add-to-cart"
                                data-id="<?= $producto['id_producto'] ?>">
                                Agregar al carrito
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php include 'modals.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>