<?php
class AuthController
{
    private $db;
    private $clienteModel;

    public function __construct($db)
    {
        $this->db = $db;
        $this->clienteModel = new ClienteModel($db);
    }

    public function register($data)
    {
        $required = ['nombre', 'apellido', 'email', 'password'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("El campo $field es requerido");
            }
        }

        return $this->clienteModel->create(
            $data['nombre'],
            $data['apellido'],
            $data['email'],
            $data['password'],
            $data['direccion'] ?? null,
            $data['telefono'] ?? null
        );
    }

    public function login($email, $password)
    {
        if (empty($email) || empty($password)) {
            throw new Exception("Email y contraseña son requeridos");
        }

        $result = $this->clienteModel->login($email, $password);

        if ($result['success']) {
            session_start();
            $_SESSION['user'] = $result['cliente'];
        }

        return $result;
    }
}
