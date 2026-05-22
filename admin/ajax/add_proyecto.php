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

// Validar campos
$titulo = trim($_POST['titulo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$url_demo = trim($_POST['url_demo'] ?? '');
$url_github = trim($_POST['url_github'] ?? '');
$tecnologias_usadas = trim($_POST['tecnologias_usadas'] ?? '');
$fecha_creacion = trim($_POST['fecha_creacion'] ?? '');
$orden = intval($_POST['orden'] ?? 0);

$errors = [];
if (empty($titulo)) $errors[] = 'El título es requerido';
if (empty($descripcion)) $errors[] = 'La descripción es requerida';
if (empty($tecnologias_usadas)) $errors[] = 'Las tecnologías usadas son requeridas';
if (empty($fecha_creacion)) $errors[] = 'La fecha de creación es requerida';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Manejar upload de imagen
$imagen_filename = 'default-project.jpg';
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    $file_type = $_FILES['imagen']['type'];
    $file_size = $_FILES['imagen']['size'];
    
    if (!in_array($file_type, $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido']);
        exit;
    }
    
    if ($file_size > $max_size) {
        echo json_encode(['success' => false, 'message' => 'El archivo excede el tamaño máximo de 2MB']);
        exit;
    }
    
    $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    $imagen_filename = 'proyecto_' . uniqid() . '.' . $ext;
    $upload_path = __DIR__ . '/../../assets/images/proyectos/' . $imagen_filename;
    
    if (!is_dir(dirname($upload_path))) {
        mkdir(dirname($upload_path), 0755, true);
    }
    
    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
        echo json_encode(['success' => false, 'message' => 'Error al subir la imagen']);
        exit;
    }
}

try {
    $conn = getConnection();
    $stmt = $conn->prepare("INSERT INTO proyectos (titulo, descripcion, imagen, url_demo, url_github, 
                            tecnologias_usadas, fecha_creacion, orden) 
                            VALUES (:titulo, :descripcion, :imagen, :url_demo, :url_github, 
                            :tecnologias_usadas, :fecha_creacion, :orden)");
    
    $stmt->execute([
        'titulo' => $titulo,
        'descripcion' => $descripcion,
        'imagen' => $imagen_filename,
        'url_demo' => $url_demo,
        'url_github' => $url_github,
        'tecnologias_usadas' => $tecnologias_usadas,
        'fecha_creacion' => $fecha_creacion,
        'orden' => $orden
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Proyecto agregado correctamente', 'id' => $conn->lastInsertId()]);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al agregar proyecto']);
}
?>