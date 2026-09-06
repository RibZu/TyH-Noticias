<?php
require_once __DIR__ . '/../config/database.php';

class ConfigController {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    private function verificarAdmin() {
        if(!isset($_SESSION['usuario_id'])) {
            $_SESSION['error'] = "Debes iniciar sesión para acceder a esta página";
            header("Location: index.php?action=login");
            exit();
        }

        if(!isset($_SESSION['rol_editor']) || !isset($_SESSION['rol_validador']) ||
           !$_SESSION['rol_editor'] || !$_SESSION['rol_validador']) {
            $_SESSION['error'] = "Acceso denegado. Solo los administradores pueden acceder a la configuración.";
            header("Location: index.php?action=dashboard");
            exit();
        }
    }

    public function parametros() {
        $this->verificarAdmin();

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $dias_publicacion = $_POST['dias_publicacion'] ?? 30;
            $max_img_size = $_POST['max_img_size'] ?? 2;

            if($dias_publicacion < 1 || $dias_publicacion > 365) {
                $_SESSION['error'] = "Los días de publicación deben estar entre 1 y 365";
                header("Location: index.php?action=parametros");
                exit();
            }

            if($max_img_size < 0.5 || $max_img_size > 10) {
                $_SESSION['error'] = "El tamaño máximo de imagen debe estar entre 0.5 y 10 MB";
                header("Location: index.php?action=parametros");
                exit();
            }

            $query = "UPDATE parametros SET valor = :valor WHERE clave = :clave";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":valor", $dias_publicacion);
            $dias_clave = 'dias_publicacion';
            $stmt->bindParam(":clave", $dias_clave);
            $stmt->execute();

            $stmt->bindParam(":valor", $max_img_size);
            $img_clave = 'max_img_size';
            $stmt->bindParam(":clave", $img_clave);
            $stmt->execute();

            $_SESSION['success'] = "Parámetros actualizados exitosamente";
            header("Location: index.php?action=parametros");
            exit();
        }

        $query = "SELECT * FROM parametros";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $parametros = $stmt->fetchAll(PDO::FETCH_ASSOC);

        include_once __DIR__ . '/../views/layout/header.php';
        include_once __DIR__ . '/../views/config/parametros.php';
        include_once __DIR__ . '/../views/layout/footer.php';
    }

    public function usuarios() {
        $this->verificarAdmin();

        require_once __DIR__ . '/../models/Usuario.php';
        $usuarioModel = new Usuario();
        $usuarios = $usuarioModel->listarTodos();

        include_once __DIR__ . '/../views/layout/header.php';
        include_once __DIR__ . '/../views/config/usuarios.php';
        include_once __DIR__ . '/../views/layout/footer.php';
    }

    public function actualizarRol() {
        $this->verificarAdmin();

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario_id = $_POST['usuario_id'] ?? 0;
            $rol_editor = isset($_POST['rol_editor']) ? 1 : 0;
            $rol_validador = isset($_POST['rol_validador']) ? 1 : 0;

            require_once __DIR__ . '/../models/Usuario.php';
            $usuarioModel = new Usuario();

            $resultado = $usuarioModel->actualizarRoles($usuario_id, $rol_editor, $rol_validador);

            if($resultado) {
                $_SESSION['success'] = "Roles actualizados exitosamente";
            } else {
                $_SESSION['error'] = "Error al actualizar roles";
            }

            header("Location: index.php?action=usuarios");
            exit();
        }
    }
}
?>
