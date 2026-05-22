<?php
// Configuración de base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'portafolio_db');

// Crear conexión PDO
function getConnection() {
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $conn;
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
        exit;
    }
}

// Función para sanitizar output
function sanitize($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Función para validar sesión
function checkSession() {
    session_start();
    if (!isset($_SESSION['admin_id'])) {
        header('Location: ../../index.php');
        exit;
    }
}

// Iniciar sesión al cargar la configuración
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>