<?php
class CarritoModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getActiveCart($id_cliente)
    {
        $query = "SELECT c.id_carrito, 
                  COALESCE(SUM(ci.cantidad * ci.precio_unitario), 0) as total,
                  COUNT(ci.id_item) as items_count
                  FROM carritos c
                  LEFT JOIN carrito_items ci ON c.id_carrito = ci.id_carrito
                  WHERE c.id_cliente = ? AND c.estado = 'activo'
                  GROUP BY c.id_carrito";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_cliente]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cart) {
            return $this->createCart($id_cliente);
        }

        $cart['items'] = $this->getCartItems($cart['id_carrito']);
        return $cart;
    }

    private function createCart($id_cliente)
    {
        $query = "INSERT INTO carritos (id_cliente) VALUES (?)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_cliente]);

        return [
            'id_carrito' => $this->db->lastInsertId(),
            'total' => 0,
            'items_count' => 0,
            'items' => []
        ];
    }

    public function getCartItems($id_carrito)
    {
        $query = "SELECT ci.*, p.nombre, p.imagen 
                  FROM carrito_items ci
                  JOIN productos p ON ci.id_producto = p.id_producto
                  WHERE ci.id_carrito = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_carrito]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addItem($id_cliente, $id_producto, $quantity, $price)
    {
        $this->db->beginTransaction();

        try {
            // Obtener o crear carrito
            $cart = $this->getActiveCart($id_cliente);

            // Verificar si el producto ya está en el carrito
            $query = "SELECT * FROM carrito_items 
                      WHERE id_carrito = ? AND id_producto = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$cart['id_carrito'], $id_producto]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($item) {
                // Actualizar cantidad
                $query = "UPDATE carrito_items 
                          SET cantidad = cantidad + ? 
                          WHERE id_item = ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$quantity, $item['id_item']]);
            } else {
                // Agregar nuevo item
                $query = "INSERT INTO carrito_items 
                          (id_carrito, id_producto, cantidad, precio_unitario) 
                          VALUES (?, ?, ?, ?)";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$cart['id_carrito'], $id_producto, $quantity, $price]);
            }

            $this->db->commit();
            return $this->getActiveCart($id_cliente);
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function removeItem($id_cliente, $id_producto)
    {
        $cart = $this->getActiveCart($id_cliente);

        $query = "DELETE FROM carrito_items 
                  WHERE id_carrito = ? AND id_producto = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$cart['id_carrito'], $id_producto]);

        return $this->getActiveCart($id_cliente);
    }

    public function processCheckout($id_cliente, $payment_data)
    {
        $this->db->beginTransaction();

        try {
            $cart = $this->getActiveCart($id_cliente);

            if ($cart['items_count'] === 0) {
                throw new Exception('El carrito está vacío');
            }

            // Registrar la venta
            $query = "INSERT INTO ventas 
                      (id_cliente, id_carrito, total, estado_pago, id_transaccion_paypal) 
                      VALUES (?, ?, ?, 'completado', ?)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $id_cliente,
                $cart['id_carrito'],
                $cart['total'],
                $payment_data['id_transaccion']
            ]);
            $id_venta = $this->db->lastInsertId();

            // Actualizar stock de productos
            foreach ($cart['items'] as $item) {
                $query = "UPDATE productos 
                          SET stock = stock - ? 
                          WHERE id_producto = ? AND stock >= ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$item['cantidad'], $item['id_producto'], $item['cantidad']]);

                if ($stmt->rowCount() === 0) {
                    throw new Exception("Stock insuficiente para {$item['nombre']}");
                }

                // Registrar productos agotados si es necesario
                $query = "SELECT stock FROM productos WHERE id_producto = ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$item['id_producto']]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($product['stock'] <= 0) {
                    $query = "INSERT INTO productos_agotados (id_producto) VALUES (?)";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute([$item['id_producto']]);
                }
            }

            // Marcar carrito como completado
            $query = "UPDATE carritos SET estado = 'completado' WHERE id_carrito = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$cart['id_carrito']]);

            $this->db->commit();

            return [
                'success' => true,
                'id_venta' => $id_venta,
                'id_transaccion' => $payment_data['id_transaccion']
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
