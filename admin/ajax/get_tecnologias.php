<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

require_once __DIR__ . '/../../config/database.php';

try {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM tecnologias ORDER BY orden ASC");
    $stmt->execute();
    $tecnologias = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'tecnologias' => $tecnologias]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al cargar tecnologías']);
}
?>