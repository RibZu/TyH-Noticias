<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Auditoria.php';

class Noticia {
    private $conn;
    private $table = "noticias";
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    private function obtenerParametro($clave) {
        $query = "SELECT valor FROM parametros WHERE clave = :clave";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":clave", $clave);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado['valor'] : null;
    }
    
    public function crear($titulo, $descripcion, $autor_id, $imagen = null) {
        if(strlen($titulo) < 10 || strlen($titulo) > 100) {
            return ["error" => "El título debe tener entre 10 y 100 caracteres"];
        }
        
        if(strlen($descripcion) < 50) {
            return ["error" => "La descripción debe tener al menos 50 caracteres"];
        }
        
        $query = "SELECT id FROM " . $this->table . " WHERE titulo = :titulo AND estado = 'Publicada'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":titulo", $titulo);
        $stmt->execute();
        if($stmt->rowCount() > 0) {
            return ["error" => "Ya existe una noticia publicada con ese título"];
        }
        
        $query = "INSERT INTO " . $this->table . " (titulo, descripcion, imagen, estado, autor_id) 
                  VALUES (:titulo, :descripcion, :imagen, 'Borrador', :autor_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":titulo", $titulo);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":imagen", $imagen);
        $stmt->bindParam(":autor_id", $autor_id);
        
        if($stmt->execute()) {
            $noticia_id = $this->conn->lastInsertId();
            $auditoria = new Auditoria();
            $auditoria->registrar($noticia_id, $autor_id, "Creación", null, "Borrador");
            return ["success" => true, "id" => $noticia_id];
        }
        return ["error" => "Error al crear la noticia"];
    }
    
    public function actualizar($id, $titulo, $descripcion, $autor_id, $imagen = null) {
        $noticia = $this->obtenerPorId($id);
        
        if(!$noticia) {
            return ["error" => "Noticia no encontrada"];
        }
        
        if($noticia['estado'] == 'Publicada' || $noticia['estado'] == 'Expirada') {
            return ["error" => "Las noticias publicadas o expiradas no pueden ser editadas"];
        }
        
        if($noticia['estado'] != 'Borrador' && $noticia['estado'] != 'Para Corrección') {
            return ["error" => "Esta noticia no puede ser editada en su estado actual"];
        }
        
        if(strlen($titulo) < 10 || strlen($titulo) > 100) {
            return ["error" => "El título debe tener entre 10 y 100 caracteres"];
        }
        
        if(strlen($descripcion) < 50) {
            return ["error" => "La descripción debe tener al menos 50 caracteres"];
        }
        
        $query = "UPDATE " . $this->table . " SET titulo = :titulo, descripcion = :descripcion";
        $params = [
            ":titulo" => $titulo,
            ":descripcion" => $descripcion,
            ":id" => $id
        ];
        
        if($imagen) {
            $query .= ", imagen = :imagen";
            $params[":imagen"] = $imagen;
        }
        
        $query .= " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        if($stmt->execute($params)) {
            $auditoria = new Auditoria();
            $nuevo_estado = $noticia['estado'] == 'Para Corrección' ? 'Borrador' : $noticia['estado'];
            $auditoria->registrar($id, $autor_id, "Edición", $noticia['estado'], $nuevo_estado);
            return ["success" => true];
        }
        return ["error" => "Error al actualizar la noticia"];
    }
    
    public function cambiarEstado($id, $nuevo_estado, $usuario_id) {
        $noticia = $this->obtenerPorId($id);
        
        if(!$noticia) {
            return ["error" => "Noticia no encontrada"];
        }
        
        require_once __DIR__ . '/Usuario.php';
        $usuario = new Usuario();
        $estado_actual = $noticia['estado'];
        
        // Validar transiciones de estado
        $transiciones_validas = [
            'Borrador' => ['Lista para Validación', 'Anulada'],
            'Lista para Validación' => ['Publicada', 'Para Corrección'],
            'Para Corrección' => ['Borrador', 'Lista para Validación']
        ];
        
        if(!isset($transiciones_validas[$estado_actual]) || !in_array($nuevo_estado, $transiciones_validas[$estado_actual])) {
            return ["error" => "Transición de estado no válida"];
        }
        
        // Validar permisos para publicación
        if($nuevo_estado == 'Publicada') {
            if(!$usuario->esValidador($usuario_id)) {
                return ["error" => "Solo los validadores pueden publicar noticias"];
            }
            
            if(strlen($noticia['descripcion']) < 50) {
                return ["error" => "La descripción no cumple con la longitud mínima"];
            }
            
            $query = "UPDATE " . $this->table . " SET fecha_publicacion = NOW() WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
        }
        
        // Ejecutar cambio de estado
        $query = "UPDATE " . $this->table . " SET estado = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":estado", $nuevo_estado);
        $stmt->bindParam(":id", $id);
        
        if($stmt->execute()) {
            $auditoria = new Auditoria();
            $auditoria->registrar($id, $usuario_id, "Cambio de estado", $estado_actual, $nuevo_estado);
            return ["success" => true];
        }
        return ["error" => "Error al cambiar el estado"];
    }
    
    public function verificarExpiracion() {
        $dias = $this->obtenerParametro('dias_publicacion');
        if($dias) {
            $query = "UPDATE " . $this->table . " SET estado = 'Expirada', fecha_expiracion = NOW() 
                      WHERE estado = 'Publicada' AND fecha_publicacion <= DATE_SUB(NOW(), INTERVAL :dias DAY)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":dias", $dias);
            $stmt->execute();
        }
    }
    
    public function obtenerPorId($id) {
        $query = "SELECT n.*, u.nombre as autor_nombre 
                  FROM " . $this->table . " n
                  JOIN usuarios u ON n.autor_id = u.id
                  WHERE n.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function listarPorEstado($estado = null, $usuario_id = null) {
        $query = "SELECT n.*, u.nombre as autor_nombre 
                  FROM " . $this->table . " n
                  JOIN usuarios u ON n.autor_id = u.id";
        
        if($estado) {
            $query .= " WHERE n.estado = :estado";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":estado", $estado);
        } else if($usuario_id) {
            $query .= " WHERE n.autor_id = :usuario_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":usuario_id", $usuario_id);
        } else {
            $stmt = $this->conn->prepare($query);
        }
        
        $query .= " ORDER BY n.fecha_creacion DESC";
        $stmt = $this->conn->prepare($query);
        
        if($estado) {
            $stmt->bindParam(":estado", $estado);
        } else if($usuario_id) {
            $stmt->bindParam(":usuario_id", $usuario_id);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function obtenerPendientesValidacion() {
        $query = "SELECT n.*, u.nombre as autor_nombre 
                  FROM " . $this->table . " n
                  JOIN usuarios u ON n.autor_id = u.id
                  WHERE n.estado = 'Lista para Validación'
                  ORDER BY n.fecha_creacion ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function listarPublicadas($limit = null) {
        $query = "SELECT n.*, u.nombre as autor_nombre 
                  FROM " . $this->table . " n
                  JOIN usuarios u ON n.autor_id = u.id
                  WHERE n.estado = 'Publicada'
                  ORDER BY n.fecha_publicacion DESC";
        
        if($limit) {
            $query .= " LIMIT :limit";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        } else {
            $stmt = $this->conn->prepare($query);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function listarDestacadas($limit = 3) {
        $query = "SELECT n.*, u.nombre as autor_nombre 
                  FROM " . $this->table . " n
                  JOIN usuarios u ON n.autor_id = u.id
                  WHERE n.estado = 'Publicada'
                  ORDER BY n.fecha_publicacion DESC
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function buscarPublicadas($termino) {
        $termino = "%{$termino}%";
        $query = "SELECT n.*, u.nombre as autor_nombre 
                  FROM " . $this->table . " n
                  JOIN usuarios u ON n.autor_id = u.id
                  WHERE n.estado = 'Publicada' 
                  AND (n.titulo LIKE :termino OR n.descripcion LIKE :termino)
                  ORDER BY n.fecha_publicacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":termino", $termino);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function incrementarVistas($id) {
        $query = "UPDATE " . $this->table . " SET vistas = vistas + 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
    }
}
?>