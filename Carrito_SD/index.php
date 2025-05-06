<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Online</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" 
    rel="stylesheet" 
    integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" 
    crossorigin="anonymous">
</head>
<body>
<header>
  <div class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
      <a href="#" class="navbar-brand">
        <strong>Tienda Online SD</strong>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
              data-bs-target="#navbarHeader" aria-controls="navbarHeader"
              aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

        <div class="collapse navbar-collapse" id="navbarHeader">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
                <a href="#" class="nav-link active">Catalogo</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">Contacto</a>
            </li>
            </ul>
            <a href="carrito.php" class="btn btn-primary">Iniciar Sesion</a>
    </div>
  </div>
</header>

<main>
    <div class="container">
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
        <div class="col">
          <div class="card w-100 shadow-sm">
            <img src="" class="img-thumbnail" style="max-height: 300px">
            <div class="card-body">
              <h5 class="card-title">Zapatos color cafe</h5>
              <p class="card-text">$ 599.00</p>
              <div class="d-flex justify-content-between align-items-center">
                <div class="btn-group">
                  <a href="#" class="btn btn-primary">Detalles</a>
                </div>
                <a href="#" class="btn btn-success">Agregar</a>
              </div>
            </div>
          </div>
        </div>

        <div class="col">
          <div class="card w-100 shadow-sm">
            <img src="https://i.ibb.co/hBWkrDH/principal.jpg" class="img-thumbnail" style="max-height: 300px">
            <div class="card-body">
              <h5 class="card-title">Laptop 15.6" con Windows 11</h5>
              <p class="card-text">$ 12,000.00</p>
              <div class="d-flex justify-content-between align-items-center">
                <div class="btn-group">
                  <a href="#" class="btn btn-primary">Detalles</a>
                </div>
                <a href="#" class="btn btn-success">Agregar</a>
              </div>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card w-100 shadow-sm">
            <img src="https://i.ibb.co/0KShhpG/principal.jpg" class="img-thumbnail" style="max-height: 300px">
            <div class="card-body">
              <h5 class="card-title">Smartphone Negro 32gb Dual Sim 3gb Ram</h5>
              <p class="card-text">$ 2,899.00</p>
              <div class="d-flex justify-content-between align-items-center">
                <div class="btn-group">
                  <a href="#" class="btn btn-primary">Detalles</a>
                </div>
                <a href="#" class="btn btn-success">Agregar</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <footer class="footer text-lg-start bg-primary bg-gradient mt-auto">
    <div class="container text-md-start pt-2 pb-1">
        <!-- Grid row -->
        <div class="row mt-3">
            <!-- Grid column -->
            <div class="col-12 col-lg-3 col-sm-12 mb-2">
                <!-- Content -->
                <p class="text-white h3">
                    Tienda Online CDP
                </p>
                <p class="mt-1 text-white">
                    &copy; 2021 - 2025 Copyright: <a href="https://github.com/mroblesdev" target="_blank" class="text-white">MRoblesDev</a>
                </p>
            </div>
            <!-- Grid column -->
        </div>
        <!-- Grid row -->
    </div>
</footer>
    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" 
integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" 
crossorigin="anonymous"></script>

</body>
</html>