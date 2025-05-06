<?php
class ProveedorModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll()
    {
        $query = "SELECT * FROM proveedores";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id_proveedor)
    {
        $query = "SELECT * FROM proveedores WHERE id_proveedor = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_proveedor]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
