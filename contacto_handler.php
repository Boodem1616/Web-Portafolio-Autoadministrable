<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config/database.php';

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Obtener y sanitizar datos
$nombre = trim(filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING) ?? '');
$email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '');
$asunto = trim(filter_input(INPUT_POST, 'asunto', FILTER_SANITIZE_STRING) ?? '');
$mensaje = trim(filter_input(INPUT_POST, 'mensaje', FILTER_SANITIZE_STRING) ?? '');

// Validar campos
$errors = [];

if (empty($nombre)) {
    $errors[] = 'El nombre es requerido';
} elseif (strlen($nombre) > 100) {
    $errors[] = 'El nombre no debe exceder los 100 caracteres';
}

if (empty($email)) {
    $errors[] = 'El email es requerido';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'El email no es válido';
} elseif (strlen($email) > 100) {
    $errors[] = 'El email no debe exceder los 100 caracteres';
}

if (empty($asunto)) {
    $errors[] = 'El asunto es requerido';
} elseif (strlen($asunto) > 150) {
    $errors[] = 'El asunto no debe exceder los 150 caracteres';
}

if (empty($mensaje)) {
    $errors[] = 'El mensaje es requerido';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

try {
    $conn = getConnection();
    
    // Insertar en la base de datos
    $stmt = $conn->prepare("INSERT INTO contactos (nombre, email, asunto, mensaje) VALUES (:nombre, :email, :asunto, :mensaje)");
    $stmt->execute([
        'nombre' => $nombre,
        'email' => $email,
        'asunto' => $asunto,
        'mensaje' => $mensaje
    ]);
    
    // Enviar email de notificación 
    $to = 'correo@example.com'; // Cambia esto por tu correo real
    $subject = "Nuevo mensaje de contacto: $asunto";
    $email_body = "Nombre: $nombre\n";
    $email_body .= "Email: $email\n";
    $email_body .= "Asunto: $asunto\n\n";
    $email_body .= "Mensaje:\n$mensaje\n";
    
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Intentar enviar email, pero no fallar si no se puede
    @mail($to, $subject, $email_body, $headers);
    
    echo json_encode([
        'success' => true, 
        'message' => '¡Mensaje enviado correctamente! Me pondré en contacto contigo pronto.'
    ]);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al guardar el mensaje. Por favor, intenta de nuevo.']);
}
?>