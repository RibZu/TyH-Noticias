<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0">
                    <i class="bi bi-check-circle"></i> Validar Noticia
                </h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Título:</label>
                    <p><?php echo htmlspecialchars($noticia['titulo']); ?></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Descripción:</label>
                    <div class="border rounded p-3 bg-light">
                        <?php echo nl2br(htmlspecialchars($noticia['descripcion'])); ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Autor:</label>
                    <p><?php echo htmlspecialchars($noticia['autor_nombre']); ?></p>
                </div>
                
                <?php if(!empty($noticia['imagen'])): ?>
                    <?php 
                    $src = (filter_var($noticia['imagen'], FILTER_VALIDATE_URL)) ? $noticia['imagen'] : '../uploads/' . $noticia['imagen'];
                    ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Imagen:</label><br>
                        <img src="<?php echo $src; ?>" alt="Imagen" style="max-width: 300px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);"
                             onerror="this.style.display='none'">
                    </div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Fecha creación:</label>
                    <p><?php echo $noticia['fecha_creacion']; ?></p>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <a href="index.php?action=validar_noticia&id=<?php echo $noticia['id']; ?>&accion=publicar" 
                           class="btn btn-success w-100" 
                           style="padding: 12px;"
                           onclick="return confirm('¿Estás seguro de que deseas PUBLICAR esta noticia?')">
                            <i class="bi bi-check-circle"></i> Publicar Noticia
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="index.php?action=validar_noticia&id=<?php echo $noticia['id']; ?>&accion=corregir" 
                           class="btn btn-warning w-100" 
                           style="padding: 12px;"
                           onclick="return confirm('¿Estás seguro de que deseas solicitar CORRECCIONES para esta noticia?')">
                            <i class="bi bi-pencil"></i> Solicitar Corrección
                        </a>
                    </div>
                </div>
                
                <div class="mt-3 text-center">
                    <a href="index.php?action=dashboard" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>