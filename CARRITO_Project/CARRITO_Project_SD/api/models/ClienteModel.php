<?php
class ClienteModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function create($nombre, $apellido, $email, $password, $direccion = null, $telefono = null)
    {
        // Verificar email único
        $query = "SELECT id_cliente FROM clientes WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            throw new Exception('El email ya está registrado');
        }

        // Hash de la contraseña
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Insertar cliente
        $query = "INSERT INTO clientes 
                  (nombre, apellido, email, password, direccion, telefono) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$nombre, $apellido, $email, $passwordHash, $direccion, $telefono]);

        return [
            'success' => true,
            'id_cliente' => $this->db->lastInsertId(),
            'nombre' => $nombre,
            'apellido' => $apellido,
            'email' => $email
        ];
    }

    public function login($email, $password)
    {
        $query = "SELECT id_cliente, nombre, apellido, email, password, direccion, telefono 
                  FROM clientes 
                  WHERE email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$email]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cliente || !password_verify($password, $cliente['password'])) {
            throw new Exception('Credenciales incorrectas');
        }

        // No devolver la contraseña
        unset($cliente['password']);

        return [
            'success' => true,
            'cliente' => $cliente
        ];
    }

    public function getById($id_cliente)
    {
        $query = "SELECT id_cliente, nombre, apellido, email, direccion, telefono 
                  FROM clientes 
                  WHERE id_cliente = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_cliente]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id_cliente, $data)
    {
        $allowed = ['nombre', 'apellido', 'direccion', 'telefono'];
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowed)) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }

        if (empty($fields)) {
            throw new Exception('No hay campos válidos para actualizar');
        }

        $values[] = $id_cliente;

        $query = "UPDATE clientes SET " . implode(', ', $fields) . " WHERE id_cliente = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute($values);

        return $this->getById($id_cliente);
    }
}
