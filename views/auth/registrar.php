<?php include_once __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Registro de Usuario</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="" id="registroForm">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre Completo *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                               required minlength="3" maxlength="100">
                        <div class="invalid-feedback">El nombre debe tener al menos 3 caracteres</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               required>
                        <div class="invalid-feedback">Ingrese un email válido</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña *</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               required minlength="6">
                        <div class="invalid-feedback">La contraseña debe tener al menos 6 caracteres</div>
                        <small class="text-muted">Mínimo 6 caracteres</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirmar_password" class="form-label">Confirmar Contraseña *</label>
                        <input type="password" class="form-control" id="confirmar_password" 
                               name="confirmar_password" required>
                        <div class="invalid-feedback">Las contraseñas no coinciden</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Roles</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="rol_editor" id="rol_editor" value="1">
                            <label class="form-check-label" for="rol_editor">
                                Editor - Puede crear y editar noticias
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="rol_validador" id="rol_validador" value="1">
                            <label class="form-check-label" for="rol_validador">
                                Validador - Puede publicar noticias
                            </label>
                        </div>
                        <small class="text-muted">Puede seleccionar uno o ambos roles</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Registrarse</button>
                </form>
                
                <div class="mt-3 text-center">
                    <a href="index.php?action=login">¿Ya tienes cuenta? Inicia Sesión aquí</a>
                </div>
                
                <hr>
                <div class="text-center">
                    <small class="text-muted">
                        Usuario administrador por defecto:<br>
                        Email: admin@tyh.com | Contraseña: admin123
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('registroForm').addEventListener('submit', function(e) {
    let isValid = true;
    
    // Validar nombre
    const nombre = document.getElementById('nombre');
    if(nombre.value.length < 3) {
        nombre.classList.add('is-invalid');
        isValid = false;
    } else {
        nombre.classList.remove('is-invalid');
    }
    
    // Validar email
    const email = document.getElementById('email');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if(!emailRegex.test(email.value)) {
        email.classList.add('is-invalid');
        isValid = false;
    } else {
        email.classList.remove('is-invalid');
    }
    
    // Validar contraseña
    const password = document.getElementById('password');
    if(password.value.length < 6) {
        password.classList.add('is-invalid');
        isValid = false;
    } else {
        password.classList.remove('is-invalid');
    }
    
    // Validar confirmación de contraseña
    const confirmar = document.getElementById('confirmar_password');
    if(confirmar.value !== password.value) {
        confirmar.classList.add('is-invalid');
        isValid = false;
    } else {
        confirmar.classList.remove('is-invalid');
    }
    
    if(!isValid) {
        e.preventDefault();
    }
});
</script>

<?php include_once __DIR__ . '/../layout/footer.php'; ?>