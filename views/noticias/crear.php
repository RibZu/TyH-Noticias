<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Crear Nueva Noticia</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título *</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" 
                               minlength="10" maxlength="100" required>
                        <small class="text-muted">Entre 10 y 100 caracteres</small>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción *</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" 
                                  rows="5" minlength="50" required></textarea>
                        <small class="text-muted">Mínimo 50 caracteres</small>
                    </div>
                    <div class="mb-3">
                        <label for="imagen" class="form-label">Imagen (Opcional)</label>
                        <input type="file" class="form-control" id="imagen" name="imagen" 
                               accept=".jpg,.jpeg,.png">
                        <small class="text-muted">Formatos JPG o PNG, máximo 2MB</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Borrador</button>
                    <a href="index.php?action=dashboard" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>