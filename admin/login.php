<?php
// Activar visualización de errores para debugging
ini_set('display_errors', 0); // Cambiar a 1 para ver errores
error_reporting(E_ALL);

require_once __DIR__ . '/../config/database.php';

// Si ya hay sesión activa, redireccionar al dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Procesar solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Debug - Puedes comentar esto en producción
    // var_dump($username, $password);
    
    // Validar campos
    $errors = [];
    if (empty($username)) {
        $errors[] = 'El usuario es requerido';
    }
    if (empty($password)) {
        $errors[] = 'La contraseña es requerida';
    }
    
    if (!empty($errors)) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
            exit;
        }
        $_SESSION['error'] = implode(', ', $errors);
        header('Location: login.php');
        exit;
    }
    
    try {
        $conn = getConnection();
        
        // Buscar usuario
        $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();
        
        // Debug
        if (!$user) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Usuario no encontrado en la base de datos']);
                exit;
            }
            $_SESSION['error'] = 'Usuario no encontrado';
            header('Location: login.php');
            exit;
        }
        
        // Verificar contraseña
        $password_verified = password_verify($password, $user['password']);
        
        // Debug -
        // var_dump($password_verified, $user['password']);
        
        if ($password_verified) {
            // Login exitoso
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            
            // Regenerar ID de sesión por seguridad
            session_regenerate_id(true);
            
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Inicio de sesión exitoso']);
                exit;
            }
            
            header('Location: dashboard.php');
            exit;
        } else {
            // Contraseña incorrecta
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false, 
                    'message' => 'Contraseña incorrecta. Por favor, verifica tus credenciales.',
                    'debug' => [
                        'user_found' => true,
                        'password_verified' => false
                    ]
                ]);
                exit;
            }
            
            $_SESSION['error'] = 'Usuario o contraseña incorrectos';
            header('Location: login.php');
            exit;
        }
        
    } catch(PDOException $e) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
            exit;
        }
        
        $_SESSION['error'] = 'Error en el servidor';
        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Panel Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-5 col-lg-4">
                <div class="login-card p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-lock fa-3x text-primary mb-3"></i>
                        <h3>Iniciar Sesión</h3>
                        <p class="text-muted">Panel Administrativo</p>
                    </div>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo htmlspecialchars($_SESSION['error']); ?>
                        <?php unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" id="loginForm">
                        <div class="mb-3">
                            <label for="username" class="form-label">Usuario</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="username" name="username" 
                                       placeholder="Ingresa tu usuario" required autocomplete="username">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Ingresa tu contraseña" required autocomplete="current-password">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe">
                            <label class="form-check-label" for="rememberMe">Recordarme</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Acceder
                        </button>
                        <a href="../index.php" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Portafolio
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Auto-focus en el campo usuario
        document.getElementById('username').focus();
    </script>
</body>
</html>