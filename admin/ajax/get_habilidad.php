<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$id = intval($_GET['id'] ?? 0);

try {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM habilidades WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $habilidad = $stmt->fetch();
    
    if ($habilidad) {
        echo json_encode(['success' => true, 'habilidad' => $habilidad]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Habilidad no encontrada']);
    }
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al cargar habilidad']);
}
?>