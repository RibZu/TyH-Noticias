<?php
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    public function login() {
        if(isset($_SESSION['usuario_id'])) {
            header("Location: index.php?action=dashboard");
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if(empty($email) || empty($password)) {
                $_SESSION['error'] = "Por favor complete todos los campos";
                header("Location: index.php?action=login");
                exit();
            }

            $usuario = $this->usuarioModel->login($email, $password);

            if($usuario) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['rol_editor'] = $usuario['rol_editor'] == 1;
                $_SESSION['rol_validador'] = $usuario['rol_validador'] == 1;

                $_SESSION['success'] = "Bienvenido " . $usuario['nombre'];
                header("Location: index.php?action=dashboard");
                exit();
            } else {
                $_SESSION['error'] = "Email o contraseña incorrectos";
                header("Location: index.php?action=login");
                exit();
            }
        }

        include_once __DIR__ . '/../views/auth/login.php';
    }

    public function registrar() {
        if(isset($_SESSION['usuario_id'])) {
            header("Location: index.php?action=dashboard");
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmar_password = $_POST['confirmar_password'] ?? '';

            $errores = [];

            if(empty($nombre) || strlen($nombre) < 3) {
                $errores[] = "El nombre debe tener al menos 3 caracteres";
            }

            if(strlen($nombre) > 100) {
                $errores[] = "El nombre no puede exceder los 100 caracteres";
            }

            if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errores[] = "Ingrese un email válido";
            }

            if(empty($password)) {
                $errores[] = "La contraseña es obligatoria";
            } elseif(strlen($password) < 6) {
                $errores[] = "La contraseña debe tener al menos 6 caracteres";
            }

            if($password !== $confirmar_password) {
                $errores[] = "Las contraseñas no coinciden";
            }

            $usuarioExistente = $this->usuarioModel->obtenerPorEmail($email);
            if($usuarioExistente) {
                $errores[] = "El email ya está registrado";
            }

            if(empty($errores)) {
                $rol_editor = isset($_POST['rol_editor']) ? 1 : 0;
                $rol_validador = isset($_POST['rol_validador']) ? 1 : 0;

                $resultado = $this->usuarioModel->registrar($nombre, $email, $password, $rol_editor, $rol_validador);

                if($resultado) {
                    $_SESSION['success'] = "Usuario registrado exitosamente. Ahora puedes iniciar sesión.";
                    header("Location: index.php?action=login");
                    exit();
                } else {
                    $_SESSION['error'] = "Error al registrar usuario. Intente nuevamente.";
                }
            } else {
                $_SESSION['error'] = implode("<br>", $errores);
            }

            header("Location: index.php?action=registrar");
            exit();
        }

        include_once __DIR__ . '/../views/auth/registrar.php';
    }

    public function logout() {
        $_SESSION = array();

        if(ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        $_SESSION['success'] = "Has cerrado sesión exitosamente";
        header("Location: index.php?action=login");
        exit();
    }

    public function dashboard() {
        if(!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?action=login");
            exit();
        }

        require_once __DIR__ . '/../models/Noticia.php';
        $noticiaModel = new Noticia();

        $noticiaModel->verificarExpiracion();

        $mis_noticias = [];
        if($_SESSION['rol_editor']) {
            $mis_noticias = $noticiaModel->listarPorEstado(null, $_SESSION['usuario_id']);
        }

        $pendientes_validacion = [];
        if($_SESSION['rol_validador']) {
            $pendientes_validacion = $noticiaModel->obtenerPendientesValidacion();
        }

        $stats = $this->obtenerEstadisticas();

        include_once __DIR__ . '/../views/layout/header.php';
        include_once __DIR__ . '/../views/dashboard.php';
        include_once __DIR__ . '/../views/layout/footer.php';
    }

    private function obtenerEstadisticas() {
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $conn = $database->getConnection();

        $stats = [];

        $query = "SELECT COUNT(*) as total FROM noticias";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $stats['total_noticias'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        $query = "SELECT estado, COUNT(*) as cantidad FROM noticias GROUP BY estado";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $stats['noticias_por_estado'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $query = "SELECT COUNT(*) as total FROM usuarios";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $stats['total_usuarios'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        return $stats;
    }

    public function perfil() {
        if(!isset($_SESSION['usuario_id'])) {
            header("Location: index.php?action=login");
            exit();
        }

        $usuario = $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $accion = $_POST['accion'] ?? '';

            if($accion == 'cambiar_password') {
                $password_actual = $_POST['password_actual'] ?? '';
                $nueva_password = $_POST['nueva_password'] ?? '';
                $confirmar_password = $_POST['confirmar_password'] ?? '';

                $login_check = $this->usuarioModel->login($usuario['email'], $password_actual);

                if(!$login_check) {
                    $_SESSION['error'] = "Contraseña actual incorrecta";
                } elseif(strlen($nueva_password) < 6) {
                    $_SESSION['error'] = "La nueva contraseña debe tener al menos 6 caracteres";
                } elseif($nueva_password !== $confirmar_password) {
                    $_SESSION['error'] = "Las contraseñas no coinciden";
                } else {
                    if($this->usuarioModel->cambiarPassword($_SESSION['usuario_id'], $nueva_password)) {
                        $_SESSION['success'] = "Contraseña actualizada exitosamente";
                    } else {
                        $_SESSION['error'] = "Error al actualizar la contraseña";
                    }
                }
            }

            header("Location: index.php?action=perfil");
            exit();
        }

        include_once __DIR__ . '/../views/layout/header.php';
        include_once __DIR__ . '/../views/auth/perfil.php';
        include_once __DIR__ . '/../views/layout/footer.php';
    }
}
?>
