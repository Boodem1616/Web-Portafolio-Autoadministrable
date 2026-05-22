<?php
// Este módulo es llamado desde dashboard.php mediante AJAX
// No es necesario incluirlo directamente, pero se mantiene por estructura
require_once __DIR__ . '/../../config/database.php';

// Verificar sesión
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// Obtener datos de biografía
try {
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM biografia ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $biografia = $stmt->fetch();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Guardar cambios
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
        
        // Manejar avatar
        $avatar_filename = $biografia['avatar'] ?? 'avatar.jpg';
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024;
            
            if (!in_array($_FILES['avatar']['type'], $allowed_types)) {
                echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido']);
                exit;
            }
            
            if ($_FILES['avatar']['size'] > $max_size) {
                echo json_encode(['success' => false, 'message' => 'El archivo excede el tamaño máximo de 2MB']);
                exit;
            }
            
            $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $avatar_filename = 'avatar_' . uniqid() . '.' . $ext;
            $upload_path = __DIR__ . '/../../assets/images/' . $avatar_filename;
            
            if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_path)) {
                echo json_encode(['success' => false, 'message' => 'Error al subir el archivo']);
                exit;
            }
        }
        
        if ($biografia) {
            $stmt = $conn->prepare("UPDATE biografia SET nombre_completo = :nombre, titulo_profesional = :titulo, 
                                   descripcion_corta = :desc_corta, descripcion_larga = :desc_larga, 
                                   avatar = :avatar WHERE id = :id");
            $stmt->execute([
                'nombre' => $nombre_completo,
                'titulo' => $titulo_profesional,
                'desc_corta' => $descripcion_corta,
                'desc_larga' => $descripcion_larga,
                'avatar' => $avatar_filename,
                'id' => $biografia['id']
            ]);
        } else {
            $stmt = $conn->prepare("INSERT INTO biografia (nombre_completo, titulo_profesional, descripcion_corta, 
                                    descripcion_larga, avatar) VALUES (:nombre, :titulo, :desc_corta, :desc_larga, :avatar)");
            $stmt->execute([
                'nombre' => $nombre_completo,
                'titulo' => $titulo_profesional,
                'desc_corta' => $descripcion_corta,
                'desc_larga' => $descripcion_larga,
                'avatar' => $avatar_filename
            ]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Biografía actualizada correctamente']);
        exit;
    }
    
    // Si es GET, devolver datos para el formulario
    if (isset($_GET['action']) && $_GET['action'] === 'get') {
        echo json_encode(['success' => true, 'biografia' => $biografia]);
        exit;
    }
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>