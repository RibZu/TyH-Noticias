<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TyH Noticias - Portal Institucional</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .card-img-placeholder {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }
        .card {
            transition: transform 0.3s ease;
            margin-bottom: 20px;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
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
        <!-- Noticias Destacadas -->
        <?php if(isset($destacadas) && count($destacadas) > 0): ?>
            <div class="row mb-5">
                <div class="col-12">
                    <h2 class="text-center mb-4">
                        <i class="bi bi-star-fill text-warning"></i> Noticias Destacadas
                    </h2>
                </div>
                <div class="row">
                    <?php foreach($destacadas as $noticia): ?>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <?php if(!empty($noticia['imagen']) && file_exists('uploads/' . $noticia['imagen'])): ?>
                                    <img src="uploads/<?php echo $noticia['imagen']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>" style="height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="card-img-placeholder">
                                        <i class="bi bi-newspaper"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($noticia['titulo']); ?></h5>
                                    <p class="card-text">
                                        <?php echo substr(htmlspecialchars($noticia['descripcion']), 0, 120) . '...'; ?>
                                    </p>
                                    <div class="text-muted small mb-2">
                                        <i class="bi bi-calendar"></i> <?php echo date('d/m/Y', strtotime($noticia['fecha_publicacion'])); ?>
                                        <br>
                                        <i class="bi bi-person"></i> <?php echo htmlspecialchars($noticia['autor_nombre']); ?>
                                    </div>
                                    <a href="index.php?action=ver_noticia&id=<?php echo $noticia['id']; ?>" class="btn btn-primary btn-sm">
                                        Leer más <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Todas las Noticias -->
        <div class="row">
            <div class="col-12">
                <h2 class="text-center mb-4">
                    <i class="bi bi-newspaper"></i> Últimas Noticias
                </h2>
            </div>
        </div>

        <div class="row">
            <?php if(count($noticias) > 0): ?>
                <?php foreach($noticias as $noticia): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <?php if(!empty($noticia['imagen']) && file_exists('uploads/' . $noticia['imagen'])): ?>
                                <img src="uploads/<?php echo $noticia['imagen']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>" style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="card-img-placeholder">
                                    <i class="bi bi-image"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($noticia['titulo']); ?></h5>
                                <p class="card-text">
                                    <?php echo substr(htmlspecialchars($noticia['descripcion']), 0, 100) . '...'; ?>
                                </p>
                                <div class="text-muted small mb-2">
                                    <i class="bi bi-calendar"></i> <?php echo date('d/m/Y', strtotime($noticia['fecha_publicacion'])); ?>
                                </div>
                                <a href="index.php?action=ver_noticia&id=<?php echo $noticia['id']; ?>" class="btn btn-outline-primary btn-sm">
                                    Leer más <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle"></i> No hay noticias publicadas aún.
                    </div>
                </div>
            <?php endif; ?>
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