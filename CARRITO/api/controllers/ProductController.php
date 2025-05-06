<?php
class ProductoController
{
    private $db;
    private $model;

    public function __construct($db)
    {
        $this->db = $db;
        $this->model = new ProductoModel($db);
    }

    public function getAllProducts()
    {
        return $this->model->getAll();
    }

    public function getProduct($id)
    {
        return $this->model->getById($id);
    }

    public function updateStock($id, $quantity)
    {
        return $this->model->updateStock($id, $quantity);
    }
}
