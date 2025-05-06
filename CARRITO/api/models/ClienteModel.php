<?php

namespace api\models;

use PDO;
use Exception;

class ClienteModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function create($nombre, $apellido, $email, $password, $direccion = null, $telefono = null)
    {
        if (empty($email) || empty($password)) {
            throw new Exception('Email y contraseña son requeridos');
        }

        $query = "SELECT id_cliente FROM clientes WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            throw new Exception('El email ya está registrado');
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $query = "INSERT INTO clientes 
                 (nombre, apellido, email, password, direccion, telefono, fecha_registro) 
                 VALUES (?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $this->db->prepare($query);
        $success = $stmt->execute([
            $nombre,
            $apellido,
            $email,
            $passwordHash,
            $direccion,
            $telefono
        ]);

        if (!$success) {
            throw new Exception('Error al registrar el cliente');
        }

        return [
            'success' => true,
            'id_cliente' => $this->db->lastInsertId(),
            'nombre' => $nombre,
            'apellido' => $apellido,
            'email' => $email
        ];
    }
    /**
     * Autentica un cliente
     * @param string $email
     * @param string $password
     * @return array
     * @throws Exception
     */
    public function login($email, $password)
    {
        if (empty($email) || empty($password)) {
            throw new Exception('Email y contraseña son requeridos');
        }

        $query = "SELECT id_cliente, nombre, apellido, email, password, direccion, telefono 
                 FROM clientes 
                 WHERE email = ?";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$email]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cliente || !password_verify($password, $cliente['password'])) {
            throw new Exception('Credenciales incorrectas');
        }

        // No devolver la contraseña en el resultado
        unset($cliente['password']);

        return [
            'success' => true,
            'cliente' => $cliente
        ];
    }

    /**
     * Obtiene información de un cliente por su ID
     * @param int $id_cliente
     * @return array|null
     */
    public function getById($id_cliente)
    {
        $query = "SELECT id_cliente, nombre, apellido, email, direccion, telefono 
                 FROM clientes 
                 WHERE id_cliente = ?";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_cliente]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza los datos de un cliente
     * @param int $id_cliente
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function update($id_cliente, $data)
    {
        $allowedFields = ['nombre', 'apellido', 'direccion', 'telefono'];
        $updateData = array_intersect_key($data, array_flip($allowedFields));

        if (empty($updateData)) {
            throw new Exception('No hay datos válidos para actualizar');
        }

        $setParts = [];
        $values = [];

        foreach ($updateData as $field => $value) {
            $setParts[] = "$field = ?";
            $values[] = $value;
        }

        $values[] = $id_cliente;

        $query = "UPDATE clientes SET " . implode(', ', $setParts) . " WHERE id_cliente = ?";
        $stmt = $this->db->prepare($query);
        $success = $stmt->execute($values);

        if (!$success) {
            throw new Exception('Error al actualizar los datos del cliente');
        }

        return $this->getById($id_cliente);
    }
}
