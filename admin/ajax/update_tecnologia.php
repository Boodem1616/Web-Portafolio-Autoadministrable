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
$nivel = intval($_POST['nivel'] ?? 0);
$orden = intval($_POST['orden'] ?? 0);

$errors = [];
if ($id <= 0) $errors[] = 'ID inválido';
if (empty($nombre)) $errors[] = 'El nombre es requerido';
if ($nivel < 0 || $nivel > 100) $errors[] = 'El nivel debe estar entre 0 y 100';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

try {
    $conn = getConnection();
    $stmt = $conn->prepare("UPDATE tecnologias SET nombre = :nombre, nivel = :nivel, orden = :orden WHERE id = :id");
    $stmt->execute([
        'nombre' => $nombre,
        'nivel' => $nivel,
        'orden' => $orden,
        'id' => $id
    ]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Tecnología actualizada correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se realizaron cambios']);
    }
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar tecnología']);
}
?>