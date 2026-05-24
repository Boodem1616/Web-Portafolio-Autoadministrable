<?php
require_once __DIR__ . '/../config/database.php';
//===============================
//ARCHIVO SOLO EN CASO DE QUE SE TE OLVIDE LA CONTRASEÑA
//BORRAR ANTES DE PUBLICARLO EN TU PAGINA WEB O HACERLO
//INACCESIBLE DESDE EL NAVEGADOR
//===============================


// Activar visualización de errores para debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>Resetear Contraseña de Admin</h2>";

try {
    $conn = getConnection();
    
    // La contraseña que quieres establecer
    $password = 'admin123';
    
    // Generar el hash
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    echo "<p>Contraseña original: <strong>" . htmlspecialchars($password) . "</strong></p>";
    echo "<p>Hash generado: <strong>" . htmlspecialchars($hashed_password) . "</strong></p>";
    
    // Verificar si existe el usuario admin
    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = 'admin'");
    $stmt->execute();
    $user = $stmt->fetch();
    
    if ($user) {
        // Actualizar la contraseña
        $stmt = $conn->prepare("UPDATE admin_users SET password = :password WHERE username = 'admin'");
        $stmt->execute(['password' => $hashed_password]);
        echo "<p style='color: green;'>✅ Contraseña actualizada correctamente para el usuario <strong>admin</strong></p>";
    } else {
        // Crear el usuario si no existe
        $stmt = $conn->prepare("INSERT INTO admin_users (username, password, email) VALUES ('admin', :password, 'admin@portafolio.com')");
        $stmt->execute(['password' => $hashed_password]);
        echo "<p style='color: green;'>✅ Usuario admin creado correctamente</p>";
    }
    
    // Verificar que el hash funciona
    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = 'admin'");
    $stmt->execute();
    $user = $stmt->fetch();
    
    echo "<h3>Verificación:</h3>";
    echo "<p>Usuario: " . htmlspecialchars($user['username']) . "</p>";
    echo "<p>Email: " . htmlspecialchars($user['email']) . "</p>";
    echo "<p>Hash almacenado: " . htmlspecialchars($user['password']) . "</p>";
    
    if (password_verify($password, $user['password'])) {
        echo "<p style='color: green; font-weight: bold;'>✅ Verificación exitosa: password_verify() funciona correctamente</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ Error: password_verify() falló</p>";
    }
    
    echo "<br><a href='login.php' class='btn btn-dark'>Ir al Login</a>";
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>