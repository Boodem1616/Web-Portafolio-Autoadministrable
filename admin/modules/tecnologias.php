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
            // Listar todas las tecnologías
            $stmt = $conn->prepare("SELECT * FROM tecnologias ORDER BY orden ASC");
            $stmt->execute();
            $tecnologias = $stmt->fetchAll();
            echo json_encode(['success' => true, 'tecnologias' => $tecnologias]);
            break;
            
        case 'get':
            // Obtener una tecnología específica
            $id = intval($_GET['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID inválido']);
                break;
            }
            
            $stmt = $conn->prepare("SELECT * FROM tecnologias WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $tecnologia = $stmt->fetch();
            
            if ($tecnologia) {
                echo json_encode(['success' => true, 'tecnologia' => $tecnologia]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Tecnología no encontrada']);
            }
            break;
            
        case 'add':
            // Agregar nueva tecnología
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                break;
            }
            
            $nombre = trim($_POST['nombre'] ?? '');
            $nivel = intval($_POST['nivel'] ?? 0);
            $orden = intval($_POST['orden'] ?? 0);
            
            $errors = [];
            if (empty($nombre)) $errors[] = 'El nombre es requerido';
            if ($nivel < 0 || $nivel > 100) $errors[] = 'El nivel debe estar entre 0 y 100';
            
            if (!empty($errors)) {
                echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
                break;
            }
            
            $stmt = $conn->prepare("INSERT INTO tecnologias (nombre, nivel, orden) VALUES (:nombre, :nivel, :orden)");
            $stmt->execute([
                'nombre' => $nombre,
                'nivel' => $nivel,
                'orden' => $orden
            ]);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Tecnología agregada correctamente',
                'id' => $conn->lastInsertId()
            ]);
            break;
            
        case 'update':
            // Actualizar tecnología existente
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                break;
            }
            
            $id = intval($_POST['id'] ?? 0);
            $nombre = trim($_POST['nombre'] ?? '');
            $nivel = intval($_POST['nivel'] ?? 0);
            $orden = intval($_POST['orden'] ?? 0);
            $activo = isset($_POST['activo']) ? 1 : 0;
            
            $errors = [];
            if ($id <= 0) $errors[] = 'ID inválido';
            if (empty($nombre)) $errors[] = 'El nombre es requerido';
            if ($nivel < 0 || $nivel > 100) $errors[] = 'El nivel debe estar entre 0 y 100';
            
            if (!empty($errors)) {
                echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
                break;
            }
            
            $stmt = $conn->prepare("UPDATE tecnologias SET nombre = :nombre, nivel = :nivel, orden = :orden, 
                                   activo = :activo WHERE id = :id");
            $stmt->execute([
                'nombre' => $nombre,
                'nivel' => $nivel,
                'orden' => $orden,
                'activo' => $activo,
                'id' => $id
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Tecnología actualizada correctamente']);
            break;
            
        case 'delete':
            // Eliminar tecnología
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Método no permitido']);
                break;
            }
            
            $id = intval($_POST['id'] ?? 0);
            
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID inválido']);
                break;
            }
            
            $stmt = $conn->prepare("DELETE FROM tecnologias WHERE id = :id");
            $stmt->execute(['id' => $id]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Tecnología eliminada correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Tecnología no encontrada']);
            }
            break;
            
        case 'toggle':
            // Activar/Desactivar tecnología
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
            
            $stmt = $conn->prepare("UPDATE tecnologias SET activo = :activo WHERE id = :id");
            $stmt->execute(['activo' => $activo, 'id' => $id]);
            
            $estado = $activo ? 'activada' : 'desactivada';
            echo json_encode(['success' => true, 'message' => "Tecnología {$estado} correctamente"]);
            break;
            
        case 'statistics':
            // Obtener estadísticas de tecnologías
            $stmt = $conn->prepare("SELECT COUNT(*) as total, AVG(nivel) as promedio, MAX(nivel) as maximo, MIN(nivel) as minimo FROM tecnologias");
            $stmt->execute();
            $stats = $stmt->fetch();
            
            $stmt = $conn->prepare("SELECT COUNT(*) as activas FROM tecnologias WHERE activo = 1");
            $stmt->execute();
            $activas = $stmt->fetch();
            
            echo json_encode([
                'success' => true, 
                'statistics' => [
                    'total' => $stats['total'],
                    'promedio' => round($stats['promedio'], 1),
                    'maximo' => $stats['maximo'],
                    'minimo' => $stats['minimo'],
                    'activas' => $activas['activas']
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