<?php include_once __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h4 class="mb-0">
                    <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="usuario@ejemplo.com" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-box-arrow-in-right"></i> Ingresar
                    </button>
                </form>
                
                <hr>
                
                <div class="text-center">
                    <p class="mb-2">¿No tienes cuenta?</p>
                    <a href="index.php?action=registrar" class="btn btn-outline-primary">
                        <i class="bi bi-person-plus"></i> Registrarse
                    </a>
                </div>
                
                <hr>
                
                <div class="alert alert-info small mt-3">
                    <strong>Credenciales de prueba:</strong><br>
                    📧 Email: admin@tyh.com<br>
                    🔑 Contraseña: admin123
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layout/footer.php'; ?>