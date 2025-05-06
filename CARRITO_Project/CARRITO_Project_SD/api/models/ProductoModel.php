<?php
class ProductoModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll()
    {
        $query = "SELECT p.*, pr.nombre as proveedor_nombre 
                  FROM productos p 
                  LEFT JOIN proveedores pr ON p.id_proveedor = pr.id_proveedor
                  WHERE p.stock > 0";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $query = "SELECT * FROM productos WHERE id_producto = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStock($id, $quantity)
    {
        $this->db->beginTransaction();

        try {
            // Bloquear el producto para evitar condiciones de carrera
            $query = "SELECT stock FROM productos WHERE id_producto = ? FOR UPDATE";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                throw new Exception('Producto no encontrado');
            }

            if ($product['stock'] < $quantity) {
                throw new Exception('Stock insuficiente');
            }

            // Actualizar stock
            $query = "UPDATE productos SET stock = stock - ? WHERE id_producto = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$quantity, $id]);

            // Verificar si quedó agotado
            if (($product['stock'] - $quantity) <= 0) {
                $query = "INSERT INTO productos_agotados (id_producto) VALUES (?)";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$id]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
