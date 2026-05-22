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
    $stmt = $conn->prepare("DELETE FROM tecnologias WHERE id = :id");
    $stmt->execute(['id' => $id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Tecnología eliminada correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Tecnología no encontrada']);
    }
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar tecnología']);
}
?>