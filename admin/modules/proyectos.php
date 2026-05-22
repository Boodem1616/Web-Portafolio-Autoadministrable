<?php
require_once __DIR__ . '/../../config/database.php';

// Verificar sesión
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

header('Content-Type: application/json');

try {
    $conn = getConnection();
    
    // Determinar la acción
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    switch($action) {
        case 'list':
            // Listar todos los proyectos
            $stmt = $conn->prepare("SELECT * FROM proyectos ORDER BY orden ASC");
            $stmt->execute();
            $proyectos = $stmt->fetchAll();
            echo json_encode(['success' => true, 'proyectos' => $proyectos]);
            break;
            
        case 'get':
            // Obtener un proyecto específico
            $id = intval($_GET['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID inválido']);
                break;
            }
            
            $stmt = $conn->prepare("SELECT * FROM proyectos WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $proyecto = $stmt->fetch();
            
            if ($proyecto) {
                echo json_encode(['success' => true, 'proyecto' => $proyecto]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Proyecto no encontrado']);
            }
            break;
            
        case 'add':
            // Agregar nuevo proyecto
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                break;
            }
            
            $titulo = trim($_POST['titulo'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $url_demo = trim($_POST['url_demo'] ?? '');
            $url_github = trim($_POST['url_github'] ?? '');
            $tecnologias_usadas = trim($_POST['tecnologias_usadas'] ?? '');
            $fecha_creacion = trim($_POST['fecha_creacion'] ?? date('Y-m-d'));
            $orden = intval($_POST['orden'] ?? 0);
            
            $errors = [];
            if (empty($titulo)) $errors[] = 'El título es requerido';
            if (empty($descripcion)) $errors[] = 'La descripción es requerida';
            if (empty($tecnologias_usadas)) $errors[] = 'Las tecnologías son requeridas';
            if (empty($fecha_creacion)) $errors[] = 'La fecha es requerida';
            
            if (!empty($errors)) {
                echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
                break;
            }
            
            // Manejar upload de imagen
            $imagen_filename = 'default-project.jpg';
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $max_size = 2 * 1024 * 1024;
                
                $file_type = $_FILES['imagen']['type'];
                $file_size = $_FILES['imagen']['size'];
                
                if (!in_array($file_type, $allowed_types)) {
                    echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido. Use JPG, PNG, GIF o WebP']);
                    break;
                }
                
                if ($file_size > $max_size) {
                    echo json_encode(['success' => false, 'message' => 'El archivo excede el tamaño máximo de 2MB']);
                    break;
                }
                
                $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $imagen_filename = 'proyecto_' . uniqid() . '.' . $ext;
                $upload_path = __DIR__ . '/../../assets/images/proyectos/' . $imagen_filename;
                
                // Crear directorio si no existe
                if (!is_dir(dirname($upload_path))) {
                    mkdir(dirname($upload_path), 0755, true);
                }
                
                if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
                    echo json_encode(['success' => false, 'message' => 'Error al subir la imagen']);
                    break;
                }
            }
            
            $stmt = $conn->prepare("INSERT INTO proyectos (titulo, descripcion, imagen, url_demo, url_github, 
                                    tecnologias_usadas, fecha_creacion, orden) 
                                    VALUES (:titulo, :descripcion, :imagen, :url_demo, :url_github, 
                                    :tecnologias, :fecha, :orden)");
            $stmt->execute([
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'imagen' => $imagen_filename,
                'url_demo' => $url_demo,
                'url_github' => $url_github,
                'tecnologias' => $tecnologias_usadas,
                'fecha' => $fecha_creacion,
                'orden' => $orden
            ]);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Proyecto agregado correctamente',
                'id' => $conn->lastInsertId()
            ]);
            break;
            
        case 'update':
            // Actualizar proyecto existente
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                break;
            }
            
            $id = intval($_POST['id'] ?? 0);
            $titulo = trim($_POST['titulo'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $url_demo = trim($_POST['url_demo'] ?? '');
            $url_github = trim($_POST['url_github'] ?? '');
            $tecnologias_usadas = trim($_POST['tecnologias_usadas'] ?? '');
            $fecha_creacion = trim($_POST['fecha_creacion'] ?? '');
            $orden = intval($_POST['orden'] ?? 0);
            $activo = isset($_POST['activo']) ? 1 : 0;
            
            $errors = [];
            if ($id <= 0) $errors[] = 'ID inválido';
            if (empty($titulo)) $errors[] = 'El título es requerido';
            if (empty($descripcion)) $errors[] = 'La descripción es requerida';
            
            if (!empty($errors)) {
                echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
                break;
            }
            
            // Manejar imagen
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $max_size = 2 * 1024 * 1024;
                
                if (!in_array($_FILES['imagen']['type'], $allowed_types)) {
                    echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido']);
                    break;
                }
                
                if ($_FILES['imagen']['size'] > $max_size) {
                    echo json_encode(['success' => false, 'message' => 'El archivo excede el tamaño máximo de 2MB']);
                    break;
                }
                
                $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $imagen_filename = 'proyecto_' . uniqid() . '.' . $ext;
                $upload_path = __DIR__ . '/../../assets/images/proyectos/' . $imagen_filename;
                
                if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
                    echo json_encode(['success' => false, 'message' => 'Error al subir la imagen']);
                    break;
                }
                
                $stmt = $conn->prepare("UPDATE proyectos SET titulo = :titulo, descripcion = :descripcion, 
                                        imagen = :imagen, url_demo = :url_demo, url_github = :url_github, 
                                        tecnologias_usadas = :tecnologias, fecha_creacion = :fecha, 
                                        orden = :orden, activo = :activo WHERE id = :id");
                $stmt->execute([
                    'titulo' => $titulo,
                    'descripcion' => $descripcion,
                    'imagen' => $imagen_filename,
                    'url_demo' => $url_demo,
                    'url_github' => $url_github,
                    'tecnologias' => $tecnologias_usadas,
                    'fecha' => $fecha_creacion,
                    'orden' => $orden,
                    'activo' => $activo,
                    'id' => $id
                ]);
            } else {
                $stmt = $conn->prepare("UPDATE proyectos SET titulo = :titulo, descripcion = :descripcion, 
                                        url_demo = :url_demo, url_github = :url_github, 
                                        tecnologias_usadas = :tecnologias, fecha_creacion = :fecha, 
                                        orden = :orden, activo = :activo WHERE id = :id");
                $stmt->execute([
                    'titulo' => $titulo,
                    'descripcion' => $descripcion,
                    'url_demo' => $url_demo,
                    'url_github' => $url_github,
                    'tecnologias' => $tecnologias_usadas,
                    'fecha' => $fecha_creacion,
                    'orden' => $orden,
                    'activo' => $activo,
                    'id' => $id
                ]);
            }
            
            echo json_encode(['success' => true, 'message' => 'Proyecto actualizado correctamente']);
            break;
            
        case 'delete':
            // Eliminar proyecto
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                break;
            }
            
            $id = intval($_POST['id'] ?? 0);
            
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID inválido']);
                break;
            }
            
            // Obtener info de la imagen antes de eliminar
            $stmt = $conn->prepare("SELECT imagen FROM proyectos WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $proyecto = $stmt->fetch();
            
            if ($proyecto) {
                // Eliminar imagen si no es la default
                if ($proyecto['imagen'] !== 'default-project.jpg') {
                    $image_path = __DIR__ . '/../../assets/images/proyectos/' . $proyecto['imagen'];
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
                
                $stmt = $conn->prepare("DELETE FROM proyectos WHERE id = :id");
                $stmt->execute(['id' => $id]);
                
                echo json_encode(['success' => true, 'message' => 'Proyecto eliminado correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Proyecto no encontrado']);
            }
            break;
            
        case 'toggle':
            // Activar/Desactivar proyecto
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                break;
            }
            
            $id = intval($_POST['id'] ?? 0);
            $activo = intval($_POST['activo'] ?? 0);
            
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID inválido']);
                break;
            }
            
            $stmt = $conn->prepare("UPDATE proyectos SET activo = :activo WHERE id = :id");
            $stmt->execute(['activo' => $activo, 'id' => $id]);
            
            $estado = $activo ? 'activado' : 'desactivado';
            echo json_encode(['success' => true, 'message' => "Proyecto {$estado} correctamente"]);
            break;
            
        case 'statistics':
            // Estadísticas de proyectos
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM proyectos");
            $stmt->execute();
            $total = $stmt->fetch();
            
            $stmt = $conn->prepare("SELECT COUNT(*) as activos FROM proyectos WHERE activo = 1");
            $stmt->execute();
            $activos = $stmt->fetch();
            
            $stmt = $conn->prepare("SELECT COUNT(*) as con_demo FROM proyectos WHERE url_demo IS NOT NULL AND url_demo != '' AND url_demo != '#'");
            $stmt->execute();
            $con_demo = $stmt->fetch();
            
            echo json_encode([
                'success' => true,
                'statistics' => [
                    'total' => $total['total'],
                    'activos' => $activos['activos'],
                    'con_demo' => $con_demo['con_demo']
                ]
            ]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Acción no reconocida']);
    }
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>