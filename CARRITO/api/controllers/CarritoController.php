<?php
class CarritoController
{
    private $db;
    private $model;
    private $productoModel;

    public function __construct($db)
    {
        $this->db = $db;
        $this->model = new CarritoModel($db);
        $this->productoModel = new ProductoModel($db);
    }

    public function getCart($id_cliente)
    {
        return $this->model->getActiveCart($id_cliente);
    }

    public function addToCart($id_cliente, $id_producto, $quantity = 1)
    {
        // Validar existencia del producto y stock
        $product = $this->productoModel->getById($id_producto);
        if (!$product || $product['stock'] < $quantity) {
            throw new Exception('Producto no disponible o stock insuficiente');
        }

        return $this->model->addItem($id_cliente, $id_producto, $quantity, $product['precio']);
    }

    public function removeFromCart($id_cliente, $id_producto)
    {
        return $this->model->removeItem($id_cliente, $id_producto);
    }

    public function checkout($id_cliente, $payment_data)
    {
        // Validar datos de pago
        if (empty($payment_data['id_transaccion'])) {
            throw new Exception('Datos de pago incompletos');
        }

        $result = $this->model->processCheckout($id_cliente, $payment_data);

        if ($result['success']) {
            // Aquí podrías enviar un correo de confirmación, etc.
        }

        return $result;
    }
}
