<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Noticias - TyH Noticias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-public {
            background-color: #2c3e50;
        }
        .resultado-card {
            transition: transform 0.2s;
            margin-bottom: 20px;
        }
        .resultado-card:hover {
            transform: translateY(-3px);
        }
        .footer-public {
            background-color: #2c3e50;
            color: white;
            padding: 30px 0;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-public">
        <div class="container">
            <a class="navbar-brand" href="index.php?action=inicio">
                <i class="bi bi-newspaper"></i> TyH Noticias
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=inicio">
                            <i class="bi bi-house"></i> Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=login">
                            <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-search"></i> Buscar Noticias</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="index.php">
                            <input type="hidden" name="action" value="buscar">
                            <div class="input-group">
                                <input type="text" class="form-control" name="q" 
                                       placeholder="Buscar por título o contenido..." 
                                       value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-search"></i> Buscar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if(isset($_GET['q']) && !empty($_GET['q'])): ?>
                    <div class="mt-4">
                        <h5>Resultados para: "<?php echo htmlspecialchars($_GET['q']); ?>"</h5>
                        <p class="text-muted"><?php echo count($noticias); ?> noticias encontradas</p>
                        
                        <?php if(count($noticias) > 0): ?>
                            <?php foreach($noticias as $noticia): ?>
                                <div class="card resultado-card">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <a href="index.php?action=ver_noticia&id=<?php echo $noticia['id']; ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($noticia['titulo']); ?>
                                            </a>
                                        </h5>
                                        <p class="card-text">
                                            <?php echo substr(htmlspecialchars($noticia['descripcion']), 0, 150) . '...'; ?>
                                        </p>
                                        <div class="text-muted small">
                                            <i class="bi bi-calendar"></i> <?php echo date('d/m/Y', strtotime($noticia['fecha_publicacion'])); ?>
                                            &nbsp;&nbsp;
                                            <i class="bi bi-person"></i> <?php echo htmlspecialchars($noticia['autor_nombre']); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> No se encontraron noticias que coincidan con tu búsqueda.
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="footer-public">
        <div class="container text-center">
            <p>&copy; <?php echo date('Y'); ?> TyH Noticias - Todos los derechos reservados</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>