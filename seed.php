<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Usuario.php';
require_once __DIR__ . '/models/Noticia.php';
require_once __DIR__ . '/models/Auditoria.php';

class SeedCompleto {
    private $conn;
    private $usuarioModel;
    private $noticiaModel;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->usuarioModel = new Usuario();
        $this->noticiaModel = new Noticia();
    }

    public function run() {
        echo "<div style='font-family: Arial, sans-serif; max-width: 1000px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; background: #f9f9f9;'>";
        echo "<h1 style='color: #333; text-align: center;'>🌱 Carga de Datos de Prueba</h1>";
        echo "<hr>";

        $this->limpiarDatos();

        $usuarios = $this->crearUsuarios();

        $this->crearNoticias($usuarios);

        $this->mostrarResumen();

        echo "</div>";
    }

    private function limpiarDatos() {
        echo "<h2>🗑️ Limpiando datos existentes...</h2>";

        try {
            $this->conn->exec("SET FOREIGN_KEY_CHECKS = 0");
            $this->conn->exec("TRUNCATE TABLE auditoria");
            $this->conn->exec("TRUNCATE TABLE noticias");
            $this->conn->exec("TRUNCATE TABLE usuarios");
            $this->conn->exec("SET FOREIGN_KEY_CHECKS = 1");

            $this->conn->exec("INSERT INTO parametros (clave, valor, descripcion) VALUES
                ('dias_publicacion', '30', 'Días que una noticia permanece publicada antes de expirar'),
                ('max_img_size', '2', 'Tamaño máximo de imagen en MB')
                ON DUPLICATE KEY UPDATE valor = VALUES(valor)");

            echo "<p style='color: green;'>✅ Datos existentes eliminados correctamente</p>";
        } catch(PDOException $e) {
            echo "<p style='color: orange;'>⚠️ No se pudieron limpiar datos: " . $e->getMessage() . "</p>";
        }
    }

    private function crearUsuarios() {
        echo "<h2>👥 Creando usuarios...</h2>";

        $usuarios = [];

        $id = $this->usuarioModel->registrar(
            'Juan Editor',
            'editor@test.com',
            'editor123',
            1,
            0
        );
        if($id) {
            $usuarios['editor'] = ['id' => $id, 'nombre' => 'Juan Editor', 'email' => 'editor@test.com'];
            echo "<p style='color: green;'>✅ Usuario EDITOR creado: editor@test.com / editor123</p>";
        }

        $id = $this->usuarioModel->registrar(
            'Maria Validadora',
            'validador@test.com',
            'validador123',
            0,
            1
        );
        if($id) {
            $usuarios['validador'] = ['id' => $id, 'nombre' => 'Maria Validadora', 'email' => 'validador@test.com'];
            echo "<p style='color: green;'>✅ Usuario VALIDADOR creado: validador@test.com / validador123</p>";
        }

        $id = $this->usuarioModel->registrar(
            'Carlos Dual',
            'dual@test.com',
            'dual123',
            1,
            1
        );
        if($id) {
            $usuarios['dual'] = ['id' => $id, 'nombre' => 'Carlos Dual', 'email' => 'dual@test.com'];
            echo "<p style='color: green;'>✅ Usuario DUAL (Editor+Validador) creado: dual@test.com / dual123</p>";
        }

        $id = $this->usuarioModel->registrar(
            'Admin General',
            'admin@tyh.com',
            'admin123',
            1,
            1
        );
        if($id) {
            $usuarios['admin'] = ['id' => $id, 'nombre' => 'Admin General', 'email' => 'admin@tyh.com'];
            echo "<p style='color: green;'>✅ Usuario ADMIN creado: admin@tyh.com / admin123</p>";
        }

        return $usuarios;
    }

    private function crearNoticias($usuarios) {
        echo "<h2>📰 Creando noticias...</h2>";

        $editor_id = $usuarios['editor']['id'];
        $validador_id = $usuarios['validador']['id'];
        $dual_id = $usuarios['dual']['id'];
        $admin_id = $usuarios['admin']['id'];

        echo "<h3 style='color: #0056b3; margin-top: 20px;'>📝 Noticias del Editor</h3>";

        $this->crearNoticia(
            "Editor: Nueva propuesta de trabajo remoto",
            "Esta es una propuesta para implementar el trabajo remoto en el departamento de tecnología. Se propone un modelo híbrido con 3 días presenciales y 2 remotos. Por favor revisar y comentar.",
            $editor_id,
            'Borrador',
            null
        );

        $this->crearNoticia(
            "Editor: Infraestructura tecnológica 2026",
            "Se necesita actualizar la infraestructura tecnológica para el próximo año. Se requieren nuevos servidores, actualización de software y capacitación al personal. Presupuesto estimado: $50,000.",
            $editor_id,
            'Borrador',
            'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=400'
        );

        $id = $this->crearNoticia(
            "Editor: Resultados encuesta clima laboral",
            "Los resultados de la encuesta de clima laboral muestran un 85% de satisfacción general. Las áreas de mejora identificadas son: comunicación interna y procesos de retroalimentación.",
            $editor_id,
            'Lista para Validación',
            null
        );
        if($id) $this->agregarHistorial($id, $editor_id, 'Enviado a validación', 'Borrador', 'Lista para Validación');

        $id = $this->crearNoticia(
            "Editor: Nuevo edificio corporativo",
            "Se anuncia la construcción del nuevo edificio corporativo que albergará a 500 empleados. La inauguración está prevista para diciembre de 2026. Incluirá áreas verdes y espacios colaborativos.",
            $editor_id,
            'Lista para Validación',
            'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=400'
        );
        if($id) $this->agregarHistorial($id, $editor_id, 'Enviado a validación', 'Borrador', 'Lista para Validación');

        $id = $this->crearNoticia(
            "Editor: Actualización de políticas de seguridad",
            "Es necesario actualizar las políticas de seguridad informática para cumplir con nuevas regulaciones. Los cambios incluyen contraseñas más seguras y autenticación de dos factores.",
            $editor_id,
            'Para Corrección',
            null
        );
        if($id) {
            $this->agregarHistorial($id, $editor_id, 'Enviado a validación', 'Borrador', 'Lista para Validación');
            $this->agregarHistorial($id, $validador_id, 'Solicita corrección', 'Lista para Validación', 'Para Corrección');
        }

        $id = $this->crearNoticia(
            "Editor: Programa de bienestar laboral",
            "Se lanza el nuevo programa de bienestar laboral que incluye: gimnasio en sitio, clases de yoga, nutricionista y psicólogo disponible. Los empleados pueden inscribirse desde la intranet.",
            $editor_id,
            'Publicada',
            null,
            date('Y-m-d H:i:s')
        );
        if($id) {
            $this->agregarHistorial($id, $editor_id, 'Enviado a validación', 'Borrador', 'Lista para Validación');
            $this->agregarHistorial($id, $validador_id, 'Publicado', 'Lista para Validación', 'Publicada');
        }

        echo "<h3 style='color: #0056b3; margin-top: 20px;'>✅ Noticias del Validador</h3>";

        $this->crearNoticia(
            "Validador: Nuevo sistema de evaluación",
            "Propuesta para implementar un nuevo sistema de evaluación de desempeño basado en objetivos trimestrales. El sistema incluirá autoevaluación y evaluación de pares.",
            $validador_id,
            'Borrador',
            null
        );

        $id = $this->crearNoticia(
            "Validador: Centro de datos renovado",
            "El centro de datos ha sido renovado con nueva tecnología de refrigeración y respaldo energético. La inversión fue de $200,000 y garantiza 99.99% de disponibilidad.",
            $validador_id,
            'Lista para Validación',
            'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=400'
        );

        $id = $this->crearNoticia(
            "Validador: Capacitaciones disponibles",
            "Están abiertas las inscripciones para capacitaciones en: Python avanzado, Gestión de proyectos y Cloud Computing. Cupos limitados.",
            $validador_id,
            'Publicada',
            null,
            date('Y-m-d H:i:s', strtotime('-2 days'))
        );

        echo "<h3 style='color: #0056b3; margin-top: 20px;'>🔄 Noticias del Usuario Dual</h3>";

        $this->crearNoticia(
            "Dual: Implementación de IA en procesos",
            "Se está evaluando la implementación de inteligencia artificial para automatizar procesos administrativos. Se espera reducir tiempos de respuesta en un 40%.",
            $dual_id,
            'Borrador',
            'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=400'
        );

        $id = $this->crearNoticia(
            "Dual: Presupuesto 2026 aprobado",
            "El presupuesto para el año fiscal 2026 ha sido aprobado por la junta directiva. Incluye incremento del 15% para tecnología y 10% para capacitación.",
            $dual_id,
            'Lista para Validación',
            null
        );
        if($id) $this->agregarHistorial($id, $dual_id, 'Enviado a validación', 'Borrador', 'Lista para Validación');

        $id = $this->crearNoticia(
            "Dual: Lanzamiento nueva plataforma",
            "Presentamos la nueva plataforma de gestión interna que unifica todos los sistemas. Características: dashboard personalizado, reportes en tiempo real y móvil responsive.",
            $dual_id,
            'Publicada',
            'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400',
            date('Y-m-d H:i:s', strtotime('-1 day'))
        );
        if($id) {
            $this->agregarHistorial($id, $dual_id, 'Enviado a validación', 'Borrador', 'Lista para Validación');
            $this->agregarHistorial($id, $dual_id, 'Publicado', 'Lista para Validación', 'Publicada');
        }

        echo "<h3 style='color: #0056b3; margin-top: 20px;'>👑 Noticias del Administrador</h3>";

        $this->crearNoticia(
            "Admin: Reestructuración organizacional",
            "Se anuncia una reestructuración organizacional para optimizar recursos. Nuevas áreas: Innovación, Datos y Experiencia del Empleado.",
            $admin_id,
            'Borrador',
            null
        );

        $id = $this->crearNoticia(
            "Admin: Alianza estratégica internacional",
            "Firmamos alianza con TechGlobal para expandir operaciones a Latinoamérica. Esto generará 200 nuevos empleos y aumentará la presencia regional.",
            $admin_id,
            'Lista para Validación',
            'https://images.unsplash.com/photo-1557804506-669a67965ba0?w=400'
        );

        $id = $this->crearNoticia(
            "Admin: Resultados financieros 2025",
            "Los resultados financieros del año 2025 muestran crecimiento del 25% en ingresos y 18% en utilidades. Se espera mantener la tendencia para 2026.",
            $admin_id,
            'Publicada',
            null,
            date('Y-m-d H:i:s', strtotime('-5 days'))
        );

        $id = $this->crearNoticia(
            "Admin: Evento de fin de año",
            "El evento de fin de año se realizará el 15 de diciembre en el Hotel Central. Habrá cena baile y sorteos. Confirmar asistencia antes del 30 de noviembre.",
            $admin_id,
            'Expirada',
            'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=400',
            date('Y-m-d H:i:s', strtotime('-35 days'))
        );

        $id = $this->crearNoticia(
            "Admin: Proyecto cancelado",
            "El proyecto de expansión a Europa ha sido cancelado por cambios en el mercado. Los recursos serán redirigidos a fortalecer operaciones locales.",
            $admin_id,
            'Anulada',
            null
        );

        echo "<p style='color: green; font-weight: bold; margin-top: 15px;'>✅ Total de 14 noticias creadas</p>";
    }

    private function crearNoticia($titulo, $descripcion, $autor_id, $estado, $imagen = null, $fecha_publicacion = null) {
        if(strlen($descripcion) < 50) {
            $descripcion = $descripcion . " " . str_repeat("Este es un texto adicional para cumplir con el requisito de mínimo 50 caracteres requerido por el sistema. ", 1);
            $descripcion = substr($descripcion, 0, 500);
        }

        $query = "INSERT INTO noticias (titulo, descripcion, imagen, estado, autor_id, fecha_creacion, fecha_publicacion, vistas)
                  VALUES (:titulo, :descripcion, :imagen, :estado, :autor_id, NOW(), :fecha_publicacion, :vistas)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":titulo", $titulo);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":imagen", $imagen);
        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(":autor_id", $autor_id);
        $stmt->bindParam(":fecha_publicacion", $fecha_publicacion);

        $vistas = rand(10, 500);
        $stmt->bindParam(":vistas", $vistas);

        if($stmt->execute()) {
            $id = $this->conn->lastInsertId();
            $icono = $imagen ? "📷" : "📄";
            echo "<p style='color: green; margin-left: 20px;'>$icono $titulo - <strong>$estado</strong> " . ($imagen ? "(con imagen)" : "(sin imagen)") . "</p>";
            return $id;
        }
        return null;
    }

    private function agregarHistorial($noticia_id, $usuario_id, $accion, $estado_anterior, $estado_nuevo) {
        $query = "INSERT INTO auditoria (noticia_id, usuario_id, accion, estado_anterior, estado_nuevo, fecha_hora)
                  VALUES (:noticia_id, :usuario_id, :accion, :estado_anterior, :estado_nuevo, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":noticia_id", $noticia_id);
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":accion", $accion);
        $stmt->bindParam(":estado_anterior", $estado_anterior);
        $stmt->bindParam(":estado_nuevo", $estado_nuevo);
        $stmt->execute();
    }

    private function mostrarResumen() {
        echo "<hr>";
        echo "<div style='background-color: #d4edda; color: #155724; padding: 20px; border-radius: 10px; margin-top: 20px;'>";
        echo "<h2 style='margin-top: 0;'>📊 Resumen de Datos Cargados</h2>";

        $query = "SELECT COUNT(*) as total FROM usuarios";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $total_usuarios = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        $query = "SELECT estado, COUNT(*) as total FROM noticias GROUP BY estado";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $noticias_por_estado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $query = "SELECT
                    SUM(CASE WHEN imagen IS NOT NULL AND imagen != '' THEN 1 ELSE 0 END) as con_imagen,
                    SUM(CASE WHEN imagen IS NULL OR imagen = '' THEN 1 ELSE 0 END) as sin_imagen
                  FROM noticias";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $imagenes = $stmt->fetch(PDO::FETCH_ASSOC);

        echo "<ul style='list-style: none; padding-left: 0;'>";
        echo "<li><strong>👥 Usuarios:</strong> $total_usuarios (Editor, Validador, Dual, Admin)</li>";
        echo "<li><strong>📰 Total noticias:</strong> " . array_sum(array_column($noticias_por_estado, 'total')) . "</li>";
        echo "<li><strong>📊 Noticias por estado:</strong><br>";
        echo "<ul>";
        foreach($noticias_por_estado as $item) {
            $estado = $item['estado'];
            $total = $item['total'];
            $icono = $estado == 'Borrador' ? '📝' : ($estado == 'Lista para Validación' ? '⏳' : ($estado == 'Para Corrección' ? '✏️' : ($estado == 'Publicada' ? '✅' : ($estado == 'Expirada' ? '⌛' : '❌'))));
            echo "<li>$icono $estado: $total noticias</li>";
        }
        echo "</ul></li>";
        echo "<li><strong>🖼️ Imágenes:</strong> {$imagenes['con_imagen']} con imagen, {$imagenes['sin_imagen']} sin imagen</li>";
        echo "</ul>";

        echo "<hr>";
        echo "<h3>🔑 Credenciales de Acceso:</h3>";
        echo "<table style='width: 100%; border-collapse: collapse;'>";
        echo "<tr style='background-color: #155724; color: white;'><th style='padding: 8px; text-align: left;'>Usuario</th><th style='padding: 8px; text-align: left;'>Email</th><th style='padding: 8px; text-align: left;'>Contraseña</th><th style='padding: 8px; text-align: left;'>Roles</th></tr>";
        echo "<tr><td style='padding: 8px; border-bottom: 1px solid #ddd;'>Editor</td><td style='padding: 8px; border-bottom: 1px solid #ddd;'>editor@test.com</td><td style='padding: 8px; border-bottom: 1px solid #ddd;'>editor123</td><td style='padding: 8px; border-bottom: 1px solid #ddd;'>✏️ Editor</td></tr>";
        echo "<tr><td style='padding: 8px; border-bottom: 1px solid #ddd;'>Validador</td><td style='padding: 8px; border-bottom: 1px solid #ddd;'>validador@test.com</td><td style='padding: 8px; border-bottom: 1px solid #ddd;'>validador123</td><td style='padding: 8px; border-bottom: 1px solid #ddd;'>✅ Validador</td></tr>";
        echo "<tr><td style='padding: 8px; border-bottom: 1px solid #ddd;'>Dual</td><td style='padding: 8px; border-bottom: 1px solid #ddd;'>dual@test.com</td><td style='padding: 8px; border-bottom: 1px solid #ddd;'>dual123</td><td style='padding: 8px; border-bottom: 1px solid #ddd;'>✏️ Editor + ✅ Validador</td></tr>";
        echo "<tr><td style='padding: 8px; border-bottom: 1px solid #ddd;'>Admin</td><td style='padding: 8px; border-bottom: 1px solid #ddd;'>admin@tyh.com</td><td style='padding: 8px; border-bottom: 1px solid #ddd;'>admin123</td><td style='padding: 8px; border-bottom: 1px solid #ddd;'>✏️ Editor + ✅ Validador</td></tr>";
        echo "</table>";

        echo "<br>";
        echo "<div style='background-color: #fff3cd; color: #856404; padding: 10px; border-radius: 5px; margin-top: 15px;'>";
        echo "<strong>💡 Consejos para pruebas:</strong><br>";
        echo "• Inicia sesión como <strong>editor@test.com</strong> para crear y enviar noticias a validación<br>";
        echo "• Inicia sesión como <strong>validador@test.com</strong> para publicar o solicitar correcciones<br>";
        echo "• Inicia sesión como <strong>dual@test.com</strong> para probar ambos flujos<br>";
        echo "• Inicia sesión como <strong>admin@tyh.com</strong> para acceso completo<br>";
        echo "</div>";

        echo "<div style='margin-top: 20px; text-align: center;'>";
        echo "<a href='index.php' style='background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🏠 Ver Sitio Público</a>";
        echo "<a href='index.php?action=login' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Iniciar Sesión</a>";
        echo "</div>";

        echo "</div>";
    }
}

$seeder = new SeedCompleto();
$seeder->run();
?>
