<?php
require_once __DIR__ . '/../models/Noticia.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Auditoria.php';

class NoticiaController {
    private $noticiaModel;
    private $usuarioModel;
    private $auditoriaModel;
    
    public function __construct() {
        $this->noticiaModel = new Noticia();
        $this->usuarioModel = new Usuario();
        $this->auditoriaModel = new Auditoria();
    }
    
    private function verificarSesion() {
        if(!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?action=login");
            exit();
        }
    }
    
    private function verificarEditor() {
        if(!isset($_SESSION['rol_editor']) || !$_SESSION['rol_editor']) {
            $_SESSION['error'] = "No tienes permisos de editor";
            header("Location: index.php?action=dashboard");
            exit();
        }
    }
    
    public function crear() {
        $this->verificarSesion();
        $this->verificarEditor();
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $titulo = trim($_POST['titulo'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $imagen = null;
            
            if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                $max_size = $this->obtenerParametro('max_img_size') * 1024 * 1024;
                $extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
                
                if($extension != 'jpg' && $extension != 'png') {
                    $_SESSION['error'] = "La imagen debe ser formato JPG o PNG";
                    header("Location: index.php?action=crear_noticia");
                    exit();
                }
                
                if($_FILES['imagen']['size'] > $max_size) {
                    $_SESSION['error'] = "La imagen excede el tamaño máximo permitido";
                    header("Location: index.php?action=crear_noticia");
                    exit();
                }
                
                $nombre_imagen = uniqid() . '.' . $extension;
                $upload_dir = __DIR__ . '/../uploads/';
                if(!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_dir . $nombre_imagen);
                $imagen = $nombre_imagen;
            }
            
            $resultado = $this->noticiaModel->crear($titulo, $descripcion, $_SESSION['usuario_id'], $imagen);
            
            if(isset($resultado['success'])) {
                $_SESSION['success'] = "Noticia creada exitosamente en estado BORRADOR";
                header("Location: index.php?action=dashboard");
            } else {
                $_SESSION['error'] = $resultado['error'];
                header("Location: index.php?action=crear_noticia");
            }
            exit();
        }
        
        include_once __DIR__ . '/../views/layout/header.php';
        include_once __DIR__ . '/../views/noticias/crear.php';
        include_once __DIR__ . '/../views/layout/footer.php';
    }
    
    public function editar($id) {
        $this->verificarSesion();
        $this->verificarEditor();
        
        $noticia = $this->noticiaModel->obtenerPorId($id);
        
        if(!$noticia) {
            $_SESSION['error'] = "Noticia no encontrada";
            header("Location: index.php?action=dashboard");
            exit();
        }
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $titulo = trim($_POST['titulo'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $imagen = $noticia['imagen'];
            
            if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                $max_size = $this->obtenerParametro('max_img_size') * 1024 * 1024;
                $extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
                
                if($extension != 'jpg' && $extension != 'png') {
                    $_SESSION['error'] = "La imagen debe ser formato JPG o PNG";
                    header("Location: index.php?action=editar_noticia&id=$id");
                    exit();
                }
                
                if($_FILES['imagen']['size'] > $max_size) {
                    $_SESSION['error'] = "La imagen excede el tamaño máximo permitido";
                    header("Location: index.php?action=editar_noticia&id=$id");
                    exit();
                }
                
                $nombre_imagen = uniqid() . '.' . $extension;
                $upload_dir = __DIR__ . '/../uploads/';
                move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_dir . $nombre_imagen);
                $imagen = $nombre_imagen;
            }
            
            $resultado = $this->noticiaModel->actualizar($id, $titulo, $descripcion, $_SESSION['usuario_id'], $imagen);
            
            if(isset($resultado['success'])) {
                $_SESSION['success'] = "Noticia actualizada exitosamente";
                header("Location: index.php?action=dashboard");
            } else {
                $_SESSION['error'] = $resultado['error'];
                header("Location: index.php?action=editar_noticia&id=$id");
            }
            exit();
        }
        
        include_once __DIR__ . '/../views/layout/header.php';
        include_once __DIR__ . '/../views/noticias/editar.php';
        include_once __DIR__ . '/../views/layout/footer.php';
    }
    
    public function enviarAValidacion($id) {
        $this->verificarSesion();
        $this->verificarEditor();
        
        $resultado = $this->noticiaModel->cambiarEstado($id, 'Lista para Validación', $_SESSION['usuario_id']);
        
        if(isset($resultado['success'])) {
            $_SESSION['success'] = "Noticia enviada a validación exitosamente";
        } else {
            $_SESSION['error'] = $resultado['error'];
        }
        
        header("Location: index.php?action=dashboard");
        exit();
    }
    
    public function anularNoticia($id) {
        $this->verificarSesion();
        $this->verificarEditor();
        
        $resultado = $this->noticiaModel->cambiarEstado($id, 'Anulada', $_SESSION['usuario_id']);
        
        if(isset($resultado['success'])) {
            $_SESSION['success'] = "Noticia anulada exitosamente";
        } else {
            $_SESSION['error'] = $resultado['error'];
        }
        
        header("Location: index.php?action=dashboard");
        exit();
    }
    
    public function validar($id) {
        $this->verificarSesion();
        
        if(!isset($_SESSION['rol_validador']) || !$_SESSION['rol_validador']) {
            $_SESSION['error'] = "No tienes permisos de validador";
            header("Location: index.php?action=dashboard");
            exit();
        }
        
        // Manejar acción GET (desde los modales)
        if(isset($_GET['accion'])) {
            $accion = $_GET['accion'];
            
            if($accion == 'publicar') {
                $resultado = $this->noticiaModel->cambiarEstado($id, 'Publicada', $_SESSION['usuario_id']);
                if(isset($resultado['success'])) {
                    $_SESSION['success'] = "Noticia publicada exitosamente";
                } else {
                    $_SESSION['error'] = $resultado['error'];
                }
            } elseif($accion == 'corregir') {
                $resultado = $this->noticiaModel->cambiarEstado($id, 'Para Corrección', $_SESSION['usuario_id']);
                if(isset($resultado['success'])) {
                    $_SESSION['success'] = "Noticia enviada para corrección";
                } else {
                    $_SESSION['error'] = $resultado['error'];
                }
            }
            
            header("Location: index.php?action=dashboard");
            exit();
        }
        
        // Si es POST (desde el formulario de la página de validación)
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $accion = $_POST['accion'] ?? '';
            
            if($accion == 'publicar') {
                $resultado = $this->noticiaModel->cambiarEstado($id, 'Publicada', $_SESSION['usuario_id']);
                if(isset($resultado['success'])) {
                    $_SESSION['success'] = "Noticia publicada exitosamente";
                } else {
                    $_SESSION['error'] = $resultado['error'];
                }
            } elseif($accion == 'corregir') {
                $resultado = $this->noticiaModel->cambiarEstado($id, 'Para Corrección', $_SESSION['usuario_id']);
                if(isset($resultado['success'])) {
                    $_SESSION['success'] = "Noticia enviada para corrección";
                } else {
                    $_SESSION['error'] = $resultado['error'];
                }
            }
            
            header("Location: index.php?action=dashboard");
            exit();
        }
        
        $noticia = $this->noticiaModel->obtenerPorId($id);
        
        include_once __DIR__ . '/../views/layout/header.php';
        include_once __DIR__ . '/../views/noticias/validar.php';
        include_once __DIR__ . '/../views/layout/footer.php';
    }
    
    public function historial($id) {
        $this->verificarSesion();
        
        $noticia = $this->noticiaModel->obtenerPorId($id);
        $historial = $this->auditoriaModel->obtenerHistorial($id);
        
        include_once __DIR__ . '/../views/layout/header.php';
        include_once __DIR__ . '/../views/noticias/historial.php';
        include_once __DIR__ . '/../views/layout/footer.php';
    }
    
    private function obtenerParametro($clave) {
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $conn = $database->getConnection();
        $query = "SELECT valor FROM parametros WHERE clave = :clave";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":clave", $clave);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado['valor'] : null;
    }
}
?>