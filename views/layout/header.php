<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TyH Noticias - Sistema de Gestión Institucional</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link href="assets/css/styles.css" rel="stylesheet">
    
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        
        .footer {
            margin-top: auto;
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #0d6efd;
        }
        
        .btn-primary {
            background-color: #0d6efd;
            border: none;
        }
        
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
        
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .badge {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 600;
        }
        
        .alert {
            border: none;
            border-radius: 10px;
        }
        
        .dropdown-menu {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php?action=inicio">
                <i class="bi bi-newspaper"></i> TyH Noticias
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=inicio">
                            <i class="bi bi-house-door"></i> Ver Sitio
                        </a>
                    </li>
                    
                    <?php if(isset($_SESSION['usuario_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="index.php?action=dashboard">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a></li>
                                <li><a class="dropdown-item" href="index.php?action=perfil">
                                    <i class="bi bi-person"></i> Mi Perfil
                                </a></li>
                                <?php if($_SESSION['rol_editor']): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="index.php?action=crear_noticia">
                                        <i class="bi bi-plus-circle"></i> Nueva Noticia
                                    </a></li>
                                <?php endif; ?>
                                <?php if($_SESSION['rol_validador']): ?>
                                    <li><a class="dropdown-item" href="index.php?action=parametros">
                                        <i class="bi bi-gear"></i> Configuración
                                    </a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="index.php?action=logout">
                                    <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?action=buscar">
                                <i class="bi bi-search"></i> Buscar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?action=login">
                                <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?action=registrar">
                                <i class="bi bi-person-plus"></i> Registrarse
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['warning'])): ?>
            <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-circle-fill"></i> <?php echo $_SESSION['warning']; unset($_SESSION['warning']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>