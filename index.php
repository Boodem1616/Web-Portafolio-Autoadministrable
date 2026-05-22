<?php
require_once __DIR__ . '/config/database.php';

// Obtener datos
$conn = getConnection();

// Biografía
$stmt = $conn->prepare("SELECT * FROM biografia ORDER BY id DESC LIMIT 1");
$stmt->execute();
$biografia = $stmt->fetch();

// Habilidades activas
$stmt = $conn->prepare("SELECT * FROM habilidades WHERE activo = 1 ORDER BY orden ASC");
$stmt->execute();
$habilidades = $stmt->fetchAll();

// Tecnologías activas
$stmt = $conn->prepare("SELECT * FROM tecnologias WHERE activo = 1 ORDER BY orden ASC");
$stmt->execute();
$tecnologias = $stmt->fetchAll();

// Proyectos activos
$stmt = $conn->prepare("SELECT * FROM proyectos WHERE activo = 1 ORDER BY orden ASC");
$stmt->execute();
$proyectos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portafolio - <?php echo isset($biografia['nombre_completo']) ? sanitize($biografia['nombre_completo']) : 'Mi Portafolio'; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body>
    <!-- NAVBAR -->
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <!-- SECCIÓN BIOGRAFÍA -->
    <section id="biografia" class="bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5 text-center mb-4 mb-lg-0">
                    <?php 
                    $avatar = isset($biografia['avatar']) && !empty($biografia['avatar']) 
                              ? 'assets/images/' . sanitize($biografia['avatar']) 
                              : 'assets/images/avatar.jpg';
                    ?>
                    <img src="<?php echo $avatar; ?>" 
                         alt="Avatar de <?php echo isset($biografia['nombre_completo']) ? sanitize($biografia['nombre_completo']) : 'Estudiante'; ?>" 
                         class="avatar-circle shadow">
                </div>
                <div class="col-lg-7">
                    <h1 class="display-4 fw-bold mb-3">
                        <?php echo isset($biografia['nombre_completo']) ? sanitize($biografia['nombre_completo']) : 'Sebastian Flores Araya'; ?>
                    </h1>
                    <h3 class="text-primary mb-4">
                        <?php echo isset($biografia['titulo_profesional']) ? sanitize($biografia['titulo_profesional']) : 'Desarrollador Web Full Stack'; ?>
                    </h3>
                    <p class="lead mb-4">
                        <?php echo isset($biografia['descripcion_corta']) ? sanitize($biografia['descripcion_corta']) : ''; ?>
                    </p>
                    <p class="mb-4">
                        <?php echo isset($biografia['descripcion_larga']) ? sanitize($biografia['descripcion_larga']) : ''; ?>
                    </p>
                    <div>
                        <a href="#contacto" class="btn btn-dark btn-lg me-3">
                            <i class="fas fa-envelope me-2"></i>Contáctame
                        </a>
                        <a href="#proyectos" class="btn btn-outline-dark btn-lg">
                            <i class="fas fa-briefcase me-2"></i>Ver Proyectos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN HABILIDADES -->
    <section id="habilidades">
        <div class="container">
            <h2 class="text-center fw-bold mb-3">Habilidades y Herramientas</h2>
            <p class="text-center text-muted mb-5">Tecnologías y herramientas que utilizo en el desarrollo</p>
            <div class="row">
                <?php foreach ($habilidades as $habilidad): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card skill-card text-center p-4 h-100">
                        <div class="card-body">
                            <i class="fab <?php echo sanitize($habilidad['icono']); ?> skill-icon" 
                               style="color: <?php echo sanitize($habilidad['color']); ?>"></i>
                            <h5 class="mt-3"><?php echo sanitize($habilidad['nombre']); ?></h5>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- SECCIÓN TECNOLOGÍAS -->
    <section id="tecnologias" class="bg-light">
        <div class="container">
            <h2 class="text-center fw-bold mb-3">Tecnologías Dominadas</h2>
            <p class="text-center text-muted mb-5">Nivel de experiencia en diferentes tecnologías</p>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <?php foreach ($tecnologias as $tecnologia): ?>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold"><?php echo sanitize($tecnologia['nombre']); ?></span>
                            <span class="text-muted"><?php echo sanitize($tecnologia['nivel']); ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-dark progress-bar-animated" 
                                 role="progressbar" 
                                 style="width: 0%" 
                                 data-width="<?php echo sanitize($tecnologia['nivel']); ?>%"
                                 aria-valuenow="<?php echo sanitize($tecnologia['nivel']); ?>" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- SECCIÓN PROYECTOS -->
    <section id="proyectos">
        <div class="container">
            <h2 class="text-center fw-bold mb-3">Proyectos Realizados</h2>
            <p class="text-center text-muted mb-5">Algunos de los proyectos en los que he trabajado</p>
            <div class="row">
                <?php foreach ($proyectos as $proyecto): ?>
                <div class="col-lg-6 mb-4">
                    <div class="card project-card h-100">
                        <?php 
                        $imagen = isset($proyecto['imagen']) && !empty($proyecto['imagen']) 
                                  ? 'assets/images/proyectos/' . sanitize($proyecto['imagen']) 
                                  : 'https://via.placeholder.com/600x400/e9ecef/333333?text=' . urlencode($proyecto['titulo']);
                        ?>
                        <img src="<?php echo $imagen; ?>" 
                             class="card-img-top project-image" 
                             alt="<?php echo sanitize($proyecto['titulo']); ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold"><?php echo sanitize($proyecto['titulo']); ?></h5>
                            <p class="card-text flex-grow-1"><?php echo sanitize($proyecto['descripcion']); ?></p>
                            <div class="mb-3">
                                <?php 
                                $tecnologias_array = explode(',', $proyecto['tecnologias_usadas']);
                                foreach ($tecnologias_array as $tech): 
                                ?>
                                <span class="badge bg-dark me-1 mb-1"><?php echo sanitize(trim($tech)); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <div class="d-flex">
                                <?php if (!empty($proyecto['url_demo']) && $proyecto['url_demo'] != '#'): ?>
                                <a href="<?php echo sanitize($proyecto['url_demo']); ?>" 
                                   class="btn btn-dark me-2" target="_blank">
                                    <i class="fas fa-external-link-alt me-1"></i>Demo
                                </a>
                                <?php endif; ?>
                                <?php if (!empty($proyecto['url_github']) && $proyecto['url_github'] != '#'): ?>
                                <a href="<?php echo sanitize($proyecto['url_github']); ?>" 
                                   class="btn btn-outline-dark" target="_blank">
                                    <i class="fab fa-github me-1"></i>GitHub
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- SECCIÓN CONTACTO -->
    <section id="contacto" class="bg-light">
        <div class="container">
            <h2 class="text-center fw-bold mb-3">Formulario de Contacto</h2>
            <p class="text-center text-muted mb-5">¿Tienes algún proyecto en mente? ¡Contáctame!</p>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div id="contactMessage"></div>
                    <form id="contactForm" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="100">
                            <div class="invalid-feedback">Por favor, ingresa tu nombre.</div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" required maxlength="100">
                            <div class="invalid-feedback">Por favor, ingresa un email válido.</div>
                        </div>
                        <div class="mb-3">
                            <label for="asunto" class="form-label">Asunto *</label>
                            <input type="text" class="form-control" id="asunto" name="asunto" required maxlength="150">
                            <div class="invalid-feedback">Por favor, ingresa el asunto.</div>
                        </div>
                        <div class="mb-4">
                            <label for="mensaje" class="form-label">Mensaje *</label>
                            <textarea class="form-control" id="mensaje" name="mensaje" rows="6" required></textarea>
                            <div class="invalid-feedback">Por favor, escribe tu mensaje.</div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-dark btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Enviar Mensaje
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <?php include __DIR__ . '/includes/footer.php'; ?>

    <!-- Modal de Login -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="loginModalLabel">
                        <i class="fas fa-lock me-2"></i>Iniciar Sesión
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-4">Ingresa tus credenciales para acceder al panel administrativo</p>
                    <div id="loginMessage"></div>
                    <form id="loginForm">
                        <div class="mb-3">
                            <label for="username" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-dark w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Acceder al Dashboard
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>