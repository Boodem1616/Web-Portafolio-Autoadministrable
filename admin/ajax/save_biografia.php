<?php
session_start();
header('Content-Type: application/json');

// Verificar sesión
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

require_once __DIR__ . '/../../config/database.php';

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Validar campos
$nombre_completo = trim($_POST['nombre_completo'] ?? '');
$titulo_profesional = trim($_POST['titulo_profesional'] ?? '');
$descripcion_corta = trim($_POST['descripcion_corta'] ?? '');
$descripcion_larga = trim($_POST['descripcion_larga'] ?? '');

$errors = [];
if (empty($nombre_completo)) $errors[] = 'El nombre es requerido';
if (empty($titulo_profesional)) $errors[] = 'El título profesional es requerido';
if (empty($descripcion_corta)) $errors[] = 'La descripción corta es requerida';
if (empty($descripcion_larga)) $errors[] = 'La descripción larga es requerida';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Manejar upload de avatar
$avatar_filename = null;
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    $file_type = $_FILES['avatar']['type'];
    $file_size = $_FILES['avatar']['size'];
    
    if (!in_array($file_type, $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido. Solo JPG, PNG y GIF.']);
        exit;
    }
    
    if ($file_size > $max_size) {
        echo json_encode(['success' => false, 'message' => 'El archivo excede el tamaño máximo de 2MB.']);
        exit;
    }
    
    $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
    $avatar_filename = 'avatar_' . uniqid() . '.' . $ext;
    $upload_path = __DIR__ . '/../../assets/images/' . $avatar_filename;
    
    if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_path)) {
        echo json_encode(['success' => false, 'message' => 'Error al subir el archivo.']);
        exit;
    }
}

try {
    $conn = getConnection();
    
    // Verificar si ya existe un registro de biografía
    $stmt = $conn->prepare("SELECT id FROM biografia ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Actualizar
        if ($avatar_filename) {
            $stmt = $conn->prepare("UPDATE biografia SET nombre_completo = :nombre, titulo_profesional = :titulo, 
                                   descripcion_corta = :desc_corta, descripcion_larga = :desc_larga, 
                                   avatar = :avatar WHERE id = :id");
            $stmt->execute([
                'nombre' => $nombre_completo,
                'titulo' => $titulo_profesional,
                'desc_corta' => $descripcion_corta,
                'desc_larga' => $descripcion_larga,
                'avatar' => $avatar_filename,
                'id' => $existing['id']
            ]);
        } else {
            $stmt = $conn->prepare("UPDATE biografia SET nombre_completo = :nombre, titulo_profesional = :titulo, 
                                   descripcion_corta = :desc_corta, descripcion_larga = :desc_larga 
                                   WHERE id = :id");
            $stmt->execute([
                'nombre' => $nombre_completo,
                'titulo' => $titulo_profesional,
                'desc_corta' => $descripcion_corta,
                'desc_larga' => $descripcion_larga,
                'id' => $existing['id']
            ]);
        }
    } else {
        // Insertar
        $stmt = $conn->prepare("INSERT INTO biografia (nombre_completo, titulo_profesional, descripcion_corta, 
                                descripcion_larga, avatar) VALUES (:nombre, :titulo, :desc_corta, :desc_larga, :avatar)");
        $stmt->execute([
            'nombre' => $nombre_completo,
            'titulo' => $titulo_profesional,
            'desc_corta' => $descripcion_corta,
            'desc_larga' => $descripcion_larga,
            'avatar' => $avatar_filename ?? 'avatar.jpg'
        ]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Biografía actualizada correctamente']);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al guardar: ' . $e->getMessage()]);
}
?>