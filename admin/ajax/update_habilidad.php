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
$nombre = trim($_POST['nombre'] ?? '');
$icono = trim($_POST['icono'] ?? '');
$color = trim($_POST['color'] ?? '#000000');
$orden = intval($_POST['orden'] ?? 0);

$errors = [];
if ($id <= 0) $errors[] = 'ID inválido';
if (empty($nombre)) $errors[] = 'El nombre es requerido';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

try {
    $conn = getConnection();
    $stmt = $conn->prepare("UPDATE habilidades SET nombre = :nombre, icono = :icono, color = :color, orden = :orden WHERE id = :id");
    $stmt->execute([
        'nombre' => $nombre,
        'icono' => $icono,
        'color' => $color,
        'orden' => $orden,
        'id' => $id
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Habilidad actualizada correctamente']);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar habilidad']);
}
?>