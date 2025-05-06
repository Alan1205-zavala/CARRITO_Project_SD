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
        try {
            $this->db->beginTransaction();

            // Validar datos de pago
            if (empty($payment_data['id_transaccion']) || empty($payment_data['amount'])) {
                throw new Exception('Datos de pago incompletos');
            }

            $cart = $this->model->getActiveCart($id_cliente);

            if ($cart['items_count'] === 0) {
                throw new Exception('El carrito está vacío');
            }

            // Verificar que el monto coincida
            if (abs($cart['total'] - $payment_data['amount']) > 0.01) {
                throw new Exception('El monto no coincide con el carrito');
            }

            // Registrar la venta
            $query = "INSERT INTO ventas 
                     (id_cliente, total, estado_pago, id_transaccion_paypal, fecha_venta) 
                     VALUES (?, ?, 'completado', ?, NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $id_cliente,
                $cart['total'],
                $payment_data['id_transaccion']
            ]);

            $this->db->commit();

            return [
                'success' => true,
                'id_transaccion' => $payment_data['id_transaccion']
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
