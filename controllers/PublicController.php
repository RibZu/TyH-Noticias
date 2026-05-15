<?php
require_once __DIR__ . '/../models/Noticia.php';

class PublicController {
    private $noticiaModel;
    
    public function __construct() {
        $this->noticiaModel = new Noticia();
    }
    
    public function index() {
        // Obtener solo noticias publicadas
        $noticias = $this->noticiaModel->listarPublicadas();
        
        // Obtener noticias destacadas (últimas 3)
        $destacadas = $this->noticiaModel->listarDestacadas();
        
        include_once __DIR__ . '/../views/public/index.php';
    }
    
    public function ver($id) {
        $noticia = $this->noticiaModel->obtenerPorId($id);
        
        // Verificar que la noticia existe y está publicada o expirada
        if(!$noticia || ($noticia['estado'] != 'Publicada' && $noticia['estado'] != 'Expirada')) {
            $_SESSION['error'] = "La noticia no está disponible";
            header("Location: index.php?action=inicio");
            exit();
        }
        
        // Incrementar contador de vistas
        $this->noticiaModel->incrementarVistas($id);
        
        include_once __DIR__ . '/../views/public/ver.php';
    }
    
    public function buscar() {
        $busqueda = $_GET['q'] ?? '';
        $noticias = [];
        
        if(!empty($busqueda)) {
            $noticias = $this->noticiaModel->buscarPublicadas($busqueda);
        }
        
        include_once __DIR__ . '/../views/public/buscar.php';
    }
}
?>