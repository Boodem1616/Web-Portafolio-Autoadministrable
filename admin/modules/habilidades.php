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
            // Listar todas las habilidades
            $stmt = $conn->prepare("SELECT * FROM habilidades ORDER BY orden ASC");
            $stmt->execute();
            $habilidades = $stmt->fetchAll();
            echo json_encode(['success' => true, 'habilidades' => $habilidades]);
            break;
            
        case 'get':
            // Obtener una habilidad específica
            $id = intval($_GET['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID inválido']);
                break;
            }
            
            $stmt = $conn->prepare("SELECT * FROM habilidades WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $habilidad = $stmt->fetch();
            
            if ($habilidad) {
                echo json_encode(['success' => true, 'habilidad' => $habilidad]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Habilidad no encontrada']);
            }
            break;
            
        case 'add':
            // Agregar nueva habilidad
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                break;
            }
            
            $nombre = trim($_POST['nombre'] ?? '');
            $icono = trim($_POST['icono'] ?? '');
            $color = trim($_POST['color'] ?? '#000000');
            $orden = intval($_POST['orden'] ?? 0);
            
            $errors = [];
            if (empty($nombre)) $errors[] = 'El nombre es requerido';
            if (empty($icono)) $errors[] = 'El icono es requerido';
            
            if (!empty($errors)) {
                echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
                break;
            }
            
            $stmt = $conn->prepare("INSERT INTO habilidades (nombre, icono, color, orden) VALUES (:nombre, :icono, :color, :orden)");
            $stmt->execute([
                'nombre' => $nombre,
                'icono' => $icono,
                'color' => $color,
                'orden' => $orden
            ]);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Habilidad agregada correctamente',
                'id' => $conn->lastInsertId()
            ]);
            break;
            
        case 'update':
            // Actualizar habilidad existente
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                break;
            }
            
            $id = intval($_POST['id'] ?? 0);
            $nombre = trim($_POST['nombre'] ?? '');
            $icono = trim($_POST['icono'] ?? '');
            $color = trim($_POST['color'] ?? '#000000');
            $orden = intval($_POST['orden'] ?? 0);
            $activo = isset($_POST['activo']) ? 1 : 0;
            
            $errors = [];
            if ($id <= 0) $errors[] = 'ID inválido';
            if (empty($nombre)) $errors[] = 'El nombre es requerido';
            
            if (!empty($errors)) {
                echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
                break;
            }
            
            $stmt = $conn->prepare("UPDATE habilidades SET nombre = :nombre, icono = :icono, color = :color, 
                                   orden = :orden, activo = :activo WHERE id = :id");
            $stmt->execute([
                'nombre' => $nombre,
                'icono' => $icono,
                'color' => $color,
                'orden' => $orden,
                'activo' => $activo,
                'id' => $id
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Habilidad actualizada correctamente']);
            break;
            
        case 'delete':
            // Eliminar habilidad
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                break;
            }
            
            $id = intval($_POST['id'] ?? 0);
            
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID inválido']);
                break;
            }
            
            $stmt = $conn->prepare("DELETE FROM habilidades WHERE id = :id");
            $stmt->execute(['id' => $id]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Habilidad eliminada correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Habilidad no encontrada']);
            }
            break;
            
        case 'toggle':
            // Activar/Desactivar habilidad
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
            
            $stmt = $conn->prepare("UPDATE habilidades SET activo = :activo WHERE id = :id");
            $stmt->execute(['activo' => $activo, 'id' => $id]);
            
            $estado = $activo ? 'activada' : 'desactivada';
            echo json_encode(['success' => true, 'message' => "Habilidad {$estado} correctamente"]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Acción no reconocida']);
    }
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>