<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="bi bi-gear"></i> Configuración del Sistema
                </h4>
            </div>
            <div class="card-body">
                <?php 
                $params = [];
                foreach($parametros as $param) {
                    $params[$param['clave']] = $param['valor'];
                }
                ?>
                
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> 
                    <strong>Área de administración</strong> - Solo accesible para usuarios con permisos de administrador.
                </div>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="dias_publicacion" class="form-label">
                            <i class="bi bi-calendar"></i> Días hasta expiración
                        </label>
                        <input type="number" class="form-control" id="dias_publicacion" 
                               name="dias_publicacion" value="<?php echo $params['dias_publicacion'] ?? 30; ?>" 
                               min="1" max="365" required>
                        <small class="text-muted">
                            Número de días que una noticia permanece publicada antes de expirar (1-365 días)
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="max_img_size" class="form-label">
                            <i class="bi bi-image"></i> Tamaño máximo de imagen (MB)
                        </label>
                        <input type="number" step="0.5" class="form-control" id="max_img_size" 
                               name="max_img_size" value="<?php echo $params['max_img_size'] ?? 2; ?>" 
                               min="0.5" max="10" required>
                        <small class="text-muted">
                            Tamaño máximo permitido para las imágenes (0.5-10 MB)
                        </small>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Nota:</strong> Los cambios en la configuración afectarán a todas las noticias nuevas.
                        Las noticias existentes no se verán afectadas por el cambio de días de expiración.
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar Configuración
                        </button>
                        <a href="index.php?action=dashboard" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>