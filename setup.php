<?php
// Script unificado: Crea la base de datos y la actualiza con todas las mejoras

try {
    // Primero conectamos sin seleccionar base de datos
    $conn = new PDO("mysql:host=localhost", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div style='font-family: Arial, sans-serif; max-width: 900px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>";
    echo "<h1 style='color: #333;'>🚀 Instalación y Actualización del Sistema TyH Noticias</h1>";
    echo "<hr>";
    
    // Crear base de datos si no existe
    $conn->exec("CREATE DATABASE IF NOT EXISTS noticias_tyh");
    echo "<p style='color: green;'>✅ Base de datos 'noticias_tyh' creada/verificada</p>";
    
    // Seleccionar la base de datos
    $conn->exec("USE noticias_tyh");
    
    // ============================================
    // 1. TABLA DE USUARIOS
    // ============================================
    $conn->exec("CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        rol_editor BOOLEAN DEFAULT FALSE,
        rol_validador BOOLEAN DEFAULT FALSE,
        fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p style='color: green;'>✅ Tabla 'usuarios' creada/verificada</p>";
    
    // ============================================
    // 2. TABLA DE NOTICIAS (con todos los campos)
    // ============================================
    $conn->exec("CREATE TABLE IF NOT EXISTS noticias (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(100) NOT NULL,
        descripcion TEXT NOT NULL,
        imagen VARCHAR(255),
        estado ENUM('Borrador', 'Lista para Validación', 'Para Corrección', 'Publicada', 'Expirada', 'Anulada') DEFAULT 'Borrador',
        autor_id INT NOT NULL,
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        fecha_publicacion TIMESTAMP NULL,
        fecha_expiracion TIMESTAMP NULL,
        vistas INT DEFAULT 0,
        FOREIGN KEY (autor_id) REFERENCES usuarios(id) ON DELETE CASCADE
    )");
    echo "<p style='color: green;'>✅ Tabla 'noticias' creada/verificada</p>";
    
    // Verificar y agregar columna 'vistas' si no existe (para versiones anteriores)
    try {
        $conn->exec("ALTER TABLE noticias ADD COLUMN vistas INT DEFAULT 0");
        echo "<p style='color: blue;'>ℹ️ Campo 'vistas' agregado a la tabla noticias</p>";
    } catch(PDOException $e) {
        if(strpos($e->getMessage(), "Duplicate column") !== false) {
            echo "<p style='color: blue;'>ℹ️ Campo 'vistas' ya existe</p>";
        }
    }
    
    // Crear índices para optimización
    try {
        $conn->exec("CREATE INDEX idx_estado_fecha ON noticias(estado, fecha_publicacion)");
        echo "<p style='color: blue;'>ℹ️ Índice optimizado creado en noticias</p>";
    } catch(PDOException $e) {
        if(strpos($e->getMessage(), "Duplicate key name") !== false) {
            echo "<p style='color: blue;'>ℹ️ Índice ya existe</p>";
        }
    }
    
    // ============================================
    // 3. TABLA DE AUDITORÍA
    // ============================================
    $conn->exec("CREATE TABLE IF NOT EXISTS auditoria (
        id INT AUTO_INCREMENT PRIMARY KEY,
        noticia_id INT NOT NULL,
        usuario_id INT NOT NULL,
        accion VARCHAR(100) NOT NULL,
        estado_anterior VARCHAR(50),
        estado_nuevo VARCHAR(50),
        fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (noticia_id) REFERENCES noticias(id) ON DELETE CASCADE,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
    )");
    echo "<p style='color: green;'>✅ Tabla 'auditoria' creada/verificada</p>";
    
    // ============================================
    // 4. TABLA DE PARÁMETROS CONFIGURABLES
    // ============================================
    $conn->exec("CREATE TABLE IF NOT EXISTS parametros (
        id INT AUTO_INCREMENT PRIMARY KEY,
        clave VARCHAR(50) UNIQUE NOT NULL,
        valor VARCHAR(255) NOT NULL,
        descripcion TEXT
    )");
    echo "<p style='color: green;'>✅ Tabla 'parametros' creada/verificada</p>";
    
    // Insertar o actualizar parámetros por defecto
    $params_default = [
        ['dias_publicacion', '30', 'Días que una noticia permanece publicada antes de expirar'],
        ['max_img_size', '2', 'Tamaño máximo de imagen en MB']
    ];
    
    foreach($params_default as $param) {
        $query = "INSERT IGNORE INTO parametros (clave, valor, descripcion) VALUES (:clave, :valor, :descripcion)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":clave", $param[0]);
        $stmt->bindParam(":valor", $param[1]);
        $stmt->bindParam(":descripcion", $param[2]);
        $stmt->execute();
    }
    echo "<p style='color: green;'>✅ Parámetros por defecto insertados/verificados</p>";
    
    // ============================================
    // 5. CREAR USUARIO ADMIN POR DEFECTO (si no existe)
    // ============================================
    $query = "SELECT COUNT(*) as total FROM usuarios WHERE email = 'admin@tyh.com'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($result['total'] == 0) {
        $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $query = "INSERT INTO usuarios (nombre, email, password, rol_editor, rol_validador) 
                  VALUES ('Administrador General', 'admin@tyh.com', :password, TRUE, TRUE)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":password", $password_hash);
        $stmt->execute();
        echo "<p style='color: green;'>✅ Usuario administrador creado</p>";
    } else {
        echo "<p style='color: blue;'>ℹ️ Usuario administrador ya existe</p>";
    }
    
    // ============================================
    // 6. DATOS DE PRUEBA (opcional - solo si no hay noticias)
    // ============================================
    $query = "SELECT COUNT(*) as total FROM noticias";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($result['total'] == 0) {
        echo "<hr>";
        echo "<h2>📝 Insertando datos de prueba...</h2>";
        
        // Obtener IDs de usuarios
        $usuarios = [];
        $query = "SELECT id, email FROM usuarios";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[$row['email']] = $row['id'];
        }
        
        $admin_id = $usuarios['admin@tyh.com'] ?? 1;
        
        // Función para insertar noticia
        $insertNoticia = function($titulo, $descripcion, $autor_id, $estado, $imagen = null, $fecha_publicacion = null) use ($conn) {
            $query = "INSERT INTO noticias (titulo, descripcion, imagen, estado, autor_id, fecha_creacion, fecha_publicacion) 
                      VALUES (:titulo, :descripcion, :imagen, :estado, :autor_id, NOW(), :fecha_publicacion)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(":titulo", $titulo);
            $stmt->bindParam(":descripcion", $descripcion);
            $stmt->bindParam(":imagen", $imagen);
            $stmt->bindParam(":estado", $estado);
            $stmt->bindParam(":autor_id", $autor_id);
            $stmt->bindParam(":fecha_publicacion", $fecha_publicacion);
            $stmt->execute();
            return $conn->lastInsertId();
        };
        
        // Crear noticias de prueba
        $descripcion_base = "Esta es una noticia de prueba con una descripción que supera los 50 caracteres para cumplir con los requisitos del sistema de gestión de noticias institucionales.";
        
        // Borrador
        $insertNoticia(
            "Lanzamiento del nuevo sistema de gestión",
            $descripcion_base,
            $admin_id,
            'Borrador',
            null,
            null
        );
        echo "<p style='color: green;'>✓ Noticia en estado BORRADOR creada</p>";
        
        // Lista para Validación
        $id = $insertNoticia(
            "Actualización de políticas de seguridad",
            $descripcion_base,
            $admin_id,
            'Lista para Validación',
            null,
            null
        );
        echo "<p style='color: green;'>✓ Noticia en estado LISTA PARA VALIDACIÓN creada</p>";
        
        // Para Corrección
        $id = $insertNoticia(
            "Resultados de la encuesta de satisfacción",
            $descripcion_base,
            $admin_id,
            'Para Corrección',
            null,
            null
        );
        echo "<p style='color: green;'>✓ Noticia en estado PARA CORRECCIÓN creada</p>";
        
        // Publicada
        $insertNoticia(
            "Bienvenida al nuevo equipo de desarrollo",
            $descripcion_base,
            $admin_id,
            'Publicada',
            null,
            date('Y-m-d H:i:s')
        );
        echo "<p style='color: green;'>✓ Noticia en estado PUBLICADA creada</p>";
        
        // Expirada (hace 40 días)
        $fecha_expirada = date('Y-m-d H:i:s', strtotime('-40 days'));
        $insertNoticia(
            "Recordatorio de fin de año fiscal",
            $descripcion_base,
            $admin_id,
            'Expirada',
            null,
            $fecha_expirada
        );
        echo "<p style='color: green;'>✓ Noticia en estado EXPIRADA creada</p>";
        
        // Anulada
        $insertNoticia(
            "Evento cancelado: Conferencia anual",
            $descripcion_base,
            $admin_id,
            'Anulada',
            null,
            null
        );
        echo "<p style='color: green;'>✓ Noticia en estado ANULADA creada</p>";
        
        echo "<p style='color: blue;'>✅ Total: 6 noticias de prueba insertadas</p>";
    } else {
        echo "<p style='color: blue;'>ℹ️ Ya existen noticias en el sistema. Saltando inserción de datos de prueba.</p>";
    }
    
    // ============================================
    // 7. RESUMEN FINAL
    // ============================================
    echo "<hr>";
    echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
    echo "<h3 style='margin-top: 0;'>✅ Instalación/Actualización completada exitosamente</h3>";
    echo "<p><strong>Credenciales de acceso:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Administrador:</strong> admin@tyh.com / admin123</li>";
    echo "</ul>";
    echo "<p><strong>Funcionalidades disponibles:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Gestión completa de noticias (CRUD)</li>";
    echo "<li>✅ Sistema de estados y validación</li>";
    echo "<li>✅ Auditoría de cambios</li>";
    echo "<li>✅ Parámetros configurables</li>";
    echo "<li>✅ Vista pública de noticias sin login</li>";
    echo "<li>✅ Búsqueda de noticias</li>";
    echo "<li>✅ Contador de vistas</li>";
    echo "</ul>";
    echo "<p><strong>Enlaces útiles:</strong></p>";
    echo "<ul>";
    echo "<li><a href='index.php'>Ver sitio público</a></li>";
    echo "<li><a href='index.php?action=login'>Panel de administración</a></li>";
    echo "</ul>";
    echo "</div>";
    
    echo "</div>";
    
} catch(PDOException $e) {
    echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 20px auto; padding: 20px; background-color: #f8d7da; color: #721c24; border-radius: 5px;'>";
    echo "<h2>❌ Error en la instalación</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Verifica que:</p>";
    echo "<ul>";
    echo "<li>MySQL esté funcionando</li>";
    echo "<li>Las credenciales en config/database.php sean correctas</li>";
    echo "<li>El usuario tenga permisos para crear bases de datos</li>";
    echo "</ul>";
    echo "</div>";
}
?>