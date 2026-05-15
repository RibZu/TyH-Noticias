<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($noticia['titulo']); ?> - TyH Noticias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .noticia-imagen {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin: 20px 0;
        }
        .sin-imagen {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 60px;
            text-align: center;
            border-radius: 10px;
            margin: 20px 0;
        }
        .sin-imagen i {
            font-size: 4rem;
            color: white;
        }
        .sin-imagen p {
            color: white;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
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
                        <a class="nav-link" href="index.php?action=buscar">
                            <i class="bi bi-search"></i> Buscar
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
                <article class="card shadow-sm">
                    <div class="card-body">
                        <h1 class="card-title mb-4"><?php echo htmlspecialchars($noticia['titulo']); ?></h1>
                        
                        <div class="text-muted mb-4 pb-2 border-bottom">
                            <i class="bi bi-calendar"></i> Publicado: <?php echo date('d/m/Y H:i', strtotime($noticia['fecha_publicacion'])); ?>
                            &nbsp;&nbsp;
                            <i class="bi bi-person"></i> Por: <?php echo htmlspecialchars($noticia['autor_nombre']); ?>
                            &nbsp;&nbsp;
                            <i class="bi bi-eye"></i> Vistas: <?php echo $noticia['vistas'] ?? 0; ?>
                        </div>

                        <?php if($noticia['estado'] == 'Expirada'): ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i> Esta noticia ha expirado y está disponible solo para consulta.
                            </div>
                        <?php endif; ?>

                        <!-- Mostrar imagen SOLO si existe -->
                        <?php if(!empty($noticia['imagen']) && file_exists('uploads/' . $noticia['imagen'])): ?>
                            <div class="text-center">
                                <img src="uploads/<?php echo $noticia['imagen']; ?>" class="noticia-imagen img-fluid" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
                            </div>
                        <?php endif; ?>

                        <div class="noticia-contenido mt-4" style="font-size: 1.1rem; line-height: 1.8;">
                            <?php echo nl2br(htmlspecialchars($noticia['descripcion'])); ?>
                        </div>
                    </div>
                </article>

                <div class="mt-4 text-center">
                    <a href="index.php?action=inicio" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al inicio
                    </a>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container text-center">
            <p>&copy; <?php echo date('Y'); ?> TyH Noticias - Todos los derechos reservados</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>