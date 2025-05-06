<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="index.php">Tienda Online</a>
        <div class="d-flex">
            <?php if (usuarioLogueado()): ?>
                <span class="navbar-text me-3">
                    Hola, <?= htmlspecialchars($_SESSION['user']['nombre']) ?>
                </span>
                <a href="logout.php" class="btn btn-outline-danger me-2">Cerrar Sesión</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-primary me-2">Iniciar Sesión</a>
                <a href="register.php" class="btn btn-primary">Registrarse</a>
            <?php endif; ?>
            <a href="#" class="btn btn-outline-success" id="cartButton">
                Carrito <span id="cartCount" class="badge bg-secondary">0</span>
            </a>
        </div>
    </div>
</nav>