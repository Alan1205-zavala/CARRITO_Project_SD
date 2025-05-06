<?php
require_once __DIR__ . '/funciones.php';

// Cargar explícitamente el modelo si es necesario
require_once __DIR__ . '/CARRITO/api/models/ClienteModel.php';

if (usuarioLogueado()) {
    header('Location: index.php');
    exit;
}

// Procesar el formulario de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = conectarDB();
        $clienteModel = new api\models\ClienteModel($db); // Usar namespace completo

        $result = $clienteModel->create(
            $_POST['nombre'],
            $_POST['apellido'],
            $_POST['email'],
            $_POST['password'],
            $_POST['direccion'] ?? null,
            $_POST['telefono'] ?? null
        );

        // Registro exitoso - autenticar al usuario
        $_SESSION['user'] = [
            'id_cliente' => $result['id_cliente'],
            'nombre' => $_POST['nombre'],
            'apellido' => $_POST['apellido'],
            'email' => $_POST['email']
        ];

        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Tienda Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>assets/css/styles.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Registro de Usuario</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="apellido" class="form-label">Apellido</label>
                                    <input type="text" class="form-control" id="apellido" name="apellido" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="mb-3">
                                <label for="direccion" class="form-label">Dirección</label>
                                <textarea class="form-control" id="direccion" name="direccion" rows="2"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono">
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Registrarse</button>
                        </form>

                        <div class="mt-3 text-center">
                            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>