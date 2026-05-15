<div class="row">
    <div class="col-12">
        <h1 class="mb-4">Panel de Control</h1>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total Noticias</h5>
                <h2 class="mb-0"><?php echo $stats['total_noticias']; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Noticias Publicadas</h5>
                <h2 class="mb-0">
                    <?php 
                    $publicadas = 0;
                    foreach($stats['noticias_por_estado'] as $item) {
                        if($item['estado'] == 'Publicada') $publicadas = $item['cantidad'];
                    }
                    echo $publicadas;
                    ?>
                </h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5 class="card-title">En Validación</h5>
                <h2 class="mb-0">
                    <?php 
                    $validacion = 0;
                    foreach($stats['noticias_por_estado'] as $item) {
                        if($item['estado'] == 'Lista para Validación') $validacion = $item['cantidad'];
                    }
                    echo $validacion;
                    ?>
                </h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">Total Usuarios</h5>
                <h2 class="mb-0"><?php echo $stats['total_usuarios']; ?></h2>
            </div>
        </div>
    </div>
</div>

<?php if($_SESSION['rol_validador'] && count($pendientes_validacion) > 0): ?>
<div class="card mb-4">
    <div class="card-header bg-warning">
        <h5 class="mb-0">
            <i class="bi bi-clock-history"></i> Noticias Pendientes de Validación
            <span class="badge bg-danger float-end"><?php echo count($pendientes_validacion); ?></span>
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Fecha Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($pendientes_validacion as $noticia): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($noticia['titulo']); ?></td>
                        <td><?php echo htmlspecialchars($noticia['autor_nombre']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($noticia['fecha_creacion'])); ?></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalValidar<?php echo $noticia['id']; ?>">
                                <i class="bi bi-check-circle"></i> Validar
                            </button>
                        </td>
                    </tr>
                    
                    <!-- Modal para cada noticia -->
                    <div class="modal fade" id="modalValidar<?php echo $noticia['id']; ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">
                                        <i class="bi bi-check-circle"></i> Validar Noticia
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Selecciona la acción para la noticia:</p>
                                    <div class="alert alert-info">
                                        <strong>Título:</strong> <?php echo htmlspecialchars($noticia['titulo']); ?>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <a href="index.php?action=validar_noticia&id=<?php echo $noticia['id']; ?>&accion=publicar" 
                                               class="btn btn-success w-100">
                                                <i class="bi bi-check-circle"></i> Publicar Noticia
                                            </a>
                                        </div>
                                        <div class="col-12">
                                            <a href="index.php?action=validar_noticia&id=<?php echo $noticia['id']; ?>&accion=corregir" 
                                               class="btn btn-warning w-100">
                                                <i class="bi bi-pencil"></i> Solicitar Corrección
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle"></i> Cancelar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if($_SESSION['rol_editor']): ?>
<div class="card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i class="bi bi-newspaper"></i> Mis Noticias
            <a href="index.php?action=crear_noticia" class="btn btn-sm btn-light float-end">
                <i class="bi bi-plus-circle"></i> Nueva Noticia
            </a>
        </h5>
    </div>
    <div class="card-body">
        <?php if(count($mis_noticias) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Título</th>
                        <th>Estado</th>
                        <th>Fecha Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($mis_noticias as $noticia): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($noticia['titulo']); ?></td>
                        <td>
                            <span class="badge bg-<?php 
                                echo $noticia['estado'] == 'Publicada' ? 'success' : 
                                    ($noticia['estado'] == 'Borrador' ? 'secondary' : 
                                    ($noticia['estado'] == 'Para Corrección' ? 'warning' : 
                                    ($noticia['estado'] == 'Lista para Validación' ? 'info' : 'danger'))); 
                            ?>">
                                <?php echo $noticia['estado']; ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($noticia['fecha_creacion'])); ?></td>
                        <td>
                            <?php if($noticia['estado'] == 'Borrador' || $noticia['estado'] == 'Para Corrección'): ?>
                                <a href="index.php?action=editar_noticia&id=<?php echo $noticia['id']; ?>" 
                                   class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                            <?php endif; ?>
                            
                            <?php if($noticia['estado'] == 'Borrador'): ?>
                                <button type="button" class="btn btn-sm btn-success" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEnviar<?php echo $noticia['id']; ?>">
                                    <i class="bi bi-send"></i> Enviar
                                </button>
                                
                                <!-- Modal Enviar a Validación -->
                                <div class="modal fade" id="modalEnviar<?php echo $noticia['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title">
                                                    <i class="bi bi-send"></i> Enviar a Validación
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>¿Enviar esta noticia para validación?</p>
                                                <div class="alert alert-info">
                                                    <strong><?php echo htmlspecialchars($noticia['titulo']); ?></strong>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <a href="index.php?action=enviar_validacion&id=<?php echo $noticia['id']; ?>" class="btn btn-success">Enviar</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if($noticia['estado'] == 'Borrador' || $noticia['estado'] == 'Lista para Validación'): ?>
                                <button type="button" class="btn btn-sm btn-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalAnular<?php echo $noticia['id']; ?>">
                                    <i class="bi bi-x-circle"></i> Anular
                                </button>
                                
                                <!-- Modal Anular Noticia -->
                                <div class="modal fade" id="modalAnular<?php echo $noticia['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">
                                                    <i class="bi bi-exclamation-triangle"></i> Anular Noticia
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>¿Anular esta noticia?</p>
                                                <div class="alert alert-warning">
                                                    <strong><?php echo htmlspecialchars($noticia['titulo']); ?></strong>
                                                </div>
                                                <p class="text-danger">Esta acción no se puede deshacer.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <a href="index.php?action=anular_noticia&id=<?php echo $noticia['id']; ?>" class="btn btn-danger">Sí, Anular</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <a href="index.php?action=historial_noticia&id=<?php echo $noticia['id']; ?>" 
                               class="btn btn-sm btn-secondary">
                                <i class="bi bi-clock-history"></i> Historial
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="bi bi-info-circle"></i> No tienes noticias creadas.
            <a href="index.php?action=crear_noticia" class="alert-link">Crea tu primera noticia</a>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>