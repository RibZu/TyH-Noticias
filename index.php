<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action = $_GET['action'] ?? 'inicio';

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/NoticiaController.php';
require_once __DIR__ . '/controllers/ConfigController.php';
require_once __DIR__ . '/controllers/PublicController.php';

$authController = new AuthController();
$noticiaController = new NoticiaController();
$configController = new ConfigController();
$publicController = new PublicController();

switch($action) {
    case 'inicio':
        $publicController->index();
        break;
    case 'ver_noticia':
        $id = $_GET['id'] ?? null;
        if($id) $publicController->ver($id);
        else header("Location: index.php?action=inicio");
        break;
    case 'buscar':
        $publicController->buscar();
        break;

    case 'login':
        $authController->login();
        break;
    case 'registrar':
        $authController->registrar();
        break;
    case 'logout':
        $authController->logout();
        break;

    case 'dashboard':
        $authController->dashboard();
        break;
    case 'perfil':
        $authController->perfil();
        break;

    case 'crear_noticia':
        $noticiaController->crear();
        break;
    case 'editar_noticia':
        $id = $_GET['id'] ?? null;
        if($id) $noticiaController->editar($id);
        else header("Location: index.php?action=dashboard");
        break;
    case 'enviar_validacion':
        $id = $_GET['id'] ?? null;
        if($id) $noticiaController->enviarAValidacion($id);
        else header("Location: index.php?action=dashboard");
        break;
    case 'anular_noticia':
        $id = $_GET['id'] ?? null;
        if($id) $noticiaController->anularNoticia($id);
        else header("Location: index.php?action=dashboard");
        break;
    case 'validar_noticia':
        $id = $_GET['id'] ?? null;
        if($id) $noticiaController->validar($id);
        else header("Location: index.php?action=dashboard");
        break;
    case 'historial_noticia':
        $id = $_GET['id'] ?? null;
        if($id) $noticiaController->historial($id);
        else header("Location: index.php?action=dashboard");
        break;

    case 'parametros':
        $configController->parametros();
        break;
    case 'usuarios':
        $configController->usuarios();
        break;
    case 'actualizar_rol':
        $configController->actualizarRol();
        break;
    
    default:
        header("Location: index.php?action=inicio");
        break;
}
?>