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

$nombre = trim($_POST['nombre'] ?? '');
$icono = trim($_POST['icono'] ?? '');
$color = trim($_POST['color'] ?? '#000000');
$orden = intval($_POST['orden'] ?? 0);

// Validaciones
$errors = [];
if (empty($nombre)) $errors[] = 'El nombre es requerido';
if (empty($icono)) $errors[] = 'El icono es requerido';
if (empty($color)) $errors[] = 'El color es requerido';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

try {
    $conn = getConnection();
    $stmt = $conn->prepare("INSERT INTO habilidades (nombre, icono, color, orden) VALUES (:nombre, :icono, :color, :orden)");
    $stmt->execute([
        'nombre' => $nombre,
        'icono' => $icono,
        'color' => $color,
        'orden' => $orden
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Habilidad agregada correctamente', 'id' => $conn->lastInsertId()]);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al agregar habilidad']);
}
?>