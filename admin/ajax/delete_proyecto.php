<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}

try {
    $conn = getConnection();
    
    // Obtener información del proyecto para eliminar la imagen
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
        
        // Eliminar registro
        $stmt = $conn->prepare("DELETE FROM proyectos WHERE id = :id");
        $stmt->execute(['id' => $id]);
        
        echo json_encode(['success' => true, 'message' => 'Proyecto eliminado correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Proyecto no encontrado']);
    }
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar proyecto']);
}
?>