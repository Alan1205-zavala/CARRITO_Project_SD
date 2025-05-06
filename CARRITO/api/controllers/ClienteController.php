<?php
class ClienteController
{
    private $db;
    private $model;

    public function __construct($db)
    {
        $this->db = $db;
        $this->model = new ClienteModel($db);
    }

    public function getProfile($id_cliente)
    {
        return $this->model->getById($id_cliente);
    }

    public function updateProfile($id_cliente, $data)
    {
        $allowed = ['nombre', 'apellido', 'direccion', 'telefono'];
        $updateData = array_intersect_key($data, array_flip($allowed));

        if (empty($updateData)) {
            throw new Exception('No hay datos válidos para actualizar');
        }

        return $this->model->update($id_cliente, $updateData);
    }
}
