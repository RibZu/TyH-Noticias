    </div>

    <footer class="footer mt-auto py-4 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5 class="text-primary">TyH Noticias</h5>
                    <p class="text-muted small">
                        Sistema de gestión, validación y publicación de noticias institucionales.
                    </p>
                </div>
                
                <div class="col-md-4 mb-3 mb-md-0">
                    <h6 class="text-dark">Enlaces rápidos</h6>
                    <ul class="list-unstyled small">
                        <?php if(isset($_SESSION['usuario_id'])): ?>
                            <li><a href="index.php?action=dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                            <?php if($_SESSION['rol_editor']): ?>
                                <li><a href="index.php?action=crear_noticia" class="text-decoration-none text-muted">Crear Noticia</a></li>
                            <?php endif; ?>
                            <li><a href="index.php?action=perfil" class="text-decoration-none text-muted">Mi Perfil</a></li>
                            <li><a href="index.php?action=logout" class="text-decoration-none text-muted">Cerrar Sesión</a></li>
                        <?php else: ?>
                            <li><a href="index.php?action=login" class="text-decoration-none text-muted">Iniciar Sesión</a></li>
                            <li><a href="index.php?action=registrar" class="text-decoration-none text-muted">Registrarse</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="col-md-4">
                    <h6 class="text-dark">Información</h6>
                    <ul class="list-unstyled small">
                        <li class="text-muted">Versión: 1.0.0</li>
                        <li class="text-muted">Desarrollado para TyH</li>
                        <li class="text-muted">&copy; <?php echo date('Y'); ?> - Todos los derechos reservados</li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-3">
            
            <div class="row">
                <div class="col-12 text-center">
                    <small class="text-muted">
                        Técnicas y Herramientas para el Desarrollo Web con Calidad - Trabajo Integrador
                    </small>
                </div>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });

        function confirmarAccion(mensaje) {
            return confirm(mensaje);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        });
    </script>
</body>
</html>