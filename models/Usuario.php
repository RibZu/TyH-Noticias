<?php
require_once __DIR__ . '/../config/database.php';

class Usuario {
    private $conn;
    private $table = "usuarios";
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function registrar($nombre, $email, $password, $rol_editor = 0, $rol_validador = 0) {
        // Verificar si el email ya existe
        if($this->obtenerPorEmail($email)) {
            return false;
        }
        
        $query = "INSERT INTO " . $this->table . " (nombre, email, password, rol_editor, rol_validador) 
                  VALUES (:nombre, :email, :password, :rol_editor, :rol_validador)";
        $stmt = $this->conn->prepare($query);
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":rol_editor", $rol_editor);
        $stmt->bindParam(":rol_validador", $rol_validador);
        
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    public function login($email, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($password, $usuario['password'])) {
                return $usuario;
            }
        }
        return false;
    }
    
    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function obtenerPorEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }
    
    public function listarTodos() {
        $query = "SELECT id, nombre, email, rol_editor, rol_validador, fecha_registro 
                  FROM " . $this->table . " ORDER BY nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function actualizarRoles($id, $rol_editor, $rol_validador) {
        $query = "UPDATE " . $this->table . " SET rol_editor = :rol_editor, rol_validador = :rol_validador 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":rol_editor", $rol_editor);
        $stmt->bindParam(":rol_validador", $rol_validador);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
    
    public function esEditor($usuario_id) {
        $usuario = $this->obtenerPorId($usuario_id);
        return $usuario && $usuario['rol_editor'] == 1;
    }
    
    public function esValidador($usuario_id) {
        $usuario = $this->obtenerPorId($usuario_id);
        return $usuario && $usuario['rol_validador'] == 1;
    }
    
    public function cambiarPassword($id, $nueva_password) {
        $query = "UPDATE " . $this->table . " SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $hashed_password = password_hash($nueva_password, PASSWORD_DEFAULT);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>