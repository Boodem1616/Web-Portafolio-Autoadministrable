<?php
require_once __DIR__ . '/../config/database.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Diagnóstico de Base de Datos</h1>";

try {
    $conn = getConnection();
    echo "<p style='color:green'>✅ Conexión a la base de datos exitosa</p>";
    
    // Verificar tablas
    $tables = ['admin_users', 'biografia', 'habilidades', 'tecnologias', 'proyectos', 'contactos'];
    echo "<h3>Tablas:</h3>";
    foreach ($tables as $table) {
        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color:green'>✅ Tabla '$table' existe</p>";
            
            // Contar registros
            $count = $conn->query("SELECT COUNT(*) as total FROM $table")->fetch();
            echo "<p style='margin-left:20px;'>Registros: {$count['total']}</p>";
        } else {
            echo "<p style='color:red'>❌ Tabla '$table' NO existe</p>";
        }
    }
    
    // Verificar usuario admin
    echo "<h3>Usuario Admin:</h3>";
    $stmt = $conn->query("SELECT * FROM admin_users");
    $users = $stmt->fetchAll();
    
    if (count($users) > 0) {
        foreach ($users as $user) {
            echo "<p>ID: {$user['id']}</p>";
            echo "<p>Usuario: " . htmlspecialchars($user['username']) . "</p>";
            echo "<p>Email: " . htmlspecialchars($user['email']) . "</p>";
            echo "<p>Hash: " . htmlspecialchars($user['password']) . "</p>";
            echo "<p>Longitud del hash: " . strlen($user['password']) . " caracteres</p>";
            
            // Probar password_verify
            $test_password = 'admin123';
            if (password_verify($test_password, $user['password'])) {
                echo "<p style='color:green; font-weight:bold;'>✅ password_verify() funciona con 'admin123'</p>";
            } else {
                echo "<p style='color:red; font-weight:bold;'>❌ password_verify() FALLA con 'admin123'</p>";
                echo "<p style='color:orange;'>El hash actual no corresponde a 'admin123'. Necesitas resetear la contraseña.</p>";
            }
            echo "<hr>";
        }
    } else {
        echo "<p style='color:red'>❌ No se encontraron usuarios admin</p>";
    }
    
    // Opción para resetear
    echo "<br>";
    echo "<a href='reset_password.php' class='btn btn-warning'>Resetear Contraseña a 'admin123'</a>";
    echo " ";
    echo "<a href='login.php' class='btn btn-primary'>Ir al Login</a>";
    
} catch(PDOException $e) {
    echo "<p style='color:red'>❌ Error de conexión: " . $e->getMessage() . "</p>";
}
?>