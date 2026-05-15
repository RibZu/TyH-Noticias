<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-warning">
                <h4 class="mb-0">Editar Noticia</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título *</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" 
                               value="<?php echo htmlspecialchars($noticia['titulo']); ?>"
                               minlength="10" maxlength="100" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción *</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" 
                                  rows="5" minlength="50" required><?php echo htmlspecialchars($noticia['descripcion']); ?></textarea>
                    </div>
                    
                    <!-- Mostrar imagen actual SOLO si existe -->
                    <?php if(!empty($noticia['imagen']) && file_exists('../uploads/' . $noticia['imagen'])): ?>
                        <div class="mb-3">
                            <label class="form-label">Imagen actual</label>
                            <div>
                                <img src="../uploads/<?php echo $noticia['imagen']; ?>" alt="Imagen actual" style="max-width: 200px; border-radius: 5px;">
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="imagen" class="form-label">
                            <?php echo (!empty($noticia['imagen']) && file_exists('../uploads/' . $noticia['imagen'])) ? 'Cambiar imagen' : 'Imagen (Opcional)'; ?>
                        </label>
                        <input type="file" class="form-control" id="imagen" name="imagen" 
                               accept=".jpg,.jpeg,.png">
                        <small class="text-muted">Formatos JPG o PNG, máximo 2MB</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="index.php?action=dashboard" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>