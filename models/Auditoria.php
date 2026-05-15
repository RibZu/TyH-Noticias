<?php
require_once __DIR__ . '/../config/database.php';

class Auditoria {
    private $conn;
    private $table = "auditoria";
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function registrar($noticia_id, $usuario_id, $accion, $estado_anterior, $estado_nuevo) {
        $query = "INSERT INTO " . $this->table . " 
                  (noticia_id, usuario_id, accion, estado_anterior, estado_nuevo) 
                  VALUES (:noticia_id, :usuario_id, :accion, :estado_anterior, :estado_nuevo)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":noticia_id", $noticia_id);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":accion", $accion);
        $stmt->bindParam(":estado_anterior", $estado_anterior);
        $stmt->bindParam(":estado_nuevo", $estado_nuevo);
        return $stmt->execute();
    }
    
    public function obtenerHistorial($noticia_id) {
        $query = "SELECT a.*, u.nombre as usuario_nombre 
                  FROM " . $this->table . " a
                  JOIN usuarios u ON a.usuario_id = u.id
                  WHERE a.noticia_id = :noticia_id
                  ORDER BY a.fecha_hora DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":noticia_id", $noticia_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>