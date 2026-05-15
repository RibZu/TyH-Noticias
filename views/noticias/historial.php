<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0">Historial de Cambios</h4>
            </div>
            <div class="card-body">
                <h5>Noticia: <?php echo htmlspecialchars($noticia['titulo']); ?></h5>
                <div class="table-responsive mt-3">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Fecha y Hora</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Estado Anterior</th>
                                <th>Estado Nuevo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($historial as $registro): ?>
                                <tr>
                                    <td><?php echo $registro['fecha_hora']; ?></td>
                                    <td><?php echo htmlspecialchars($registro['usuario_nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($registro['accion']); ?></td>
                                    <td>
                                        <?php if($registro['estado_anterior']): ?>
                                            <span class="badge bg-secondary"><?php echo $registro['estado_anterior']; ?></span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($registro['estado_nuevo']): ?>
                                            <span class="badge bg-primary"><?php echo $registro['estado_nuevo']; ?></span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="index.php?action=dashboard" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    </div>
</div>