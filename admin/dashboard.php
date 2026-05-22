<?php
require_once __DIR__ . '/../config/database.php';

// Verificar sesión
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../index.php');
    exit;
}

// Obtener datos para el dashboard
$conn = getConnection();

// Biografía
$stmt = $conn->prepare("SELECT * FROM biografia ORDER BY id DESC LIMIT 1");
$stmt->execute();
$biografia = $stmt->fetch();

// Habilidades
$stmt = $conn->prepare("SELECT * FROM habilidades ORDER BY orden ASC");
$stmt->execute();
$habilidades = $stmt->fetchAll();

// Tecnologías
$stmt = $conn->prepare("SELECT * FROM tecnologias ORDER BY orden ASC");
$stmt->execute();
$tecnologias = $stmt->fetchAll();

// Proyectos
$stmt = $conn->prepare("SELECT * FROM proyectos ORDER BY orden ASC");
$stmt->execute();
$proyectos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Panel Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
</head>
<body>
    <!-- Header -->
    <header class="bg-dark text-white py-3">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>Panel Administrativo
                    </h4>
                    <small class="text-muted">Gestiona el contenido de tu portafolio</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="me-3">
                        <i class="fas fa-user me-1"></i>
                        <?php echo sanitize($_SESSION['admin_username']); ?>
                    </span>
                    <a href="logout.php" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 dashboard-sidebar p-3">
                <ul class="nav flex-column nav-pills" id="dashboardTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="biografia-tab" data-bs-toggle="pill" 
                                data-bs-target="#biografia-panel" type="button" role="tab">
                            <i class="fas fa-user me-2"></i>Biografía
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="habilidades-tab" data-bs-toggle="pill" 
                                data-bs-target="#habilidades-panel" type="button" role="tab">
                            <i class="fas fa-code me-2"></i>Habilidades
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tecnologias-tab" data-bs-toggle="pill" 
                                data-bs-target="#tecnologias-panel" type="button" role="tab">
                            <i class="fas fa-chart-bar me-2"></i>Tecnologías
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="proyectos-tab" data-bs-toggle="pill" 
                                data-bs-target="#proyectos-panel" type="button" role="tab">
                            <i class="fas fa-briefcase me-2"></i>Proyectos
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Contenido Principal -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="tab-content" id="dashboardTabsContent">
                    
                    <!-- Tab Biografía -->
                    <div class="tab-pane fade show active" id="biografia-panel" role="tabpanel">
                        <h3 class="mb-4">Editar Biografía</h3>
                        <div id="biografiaMessage"></div>
                        <form id="biografiaForm">
                            <div class="mb-3">
                                <label for="bio_nombre" class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" id="bio_nombre" name="nombre_completo" 
                                       value="<?php echo isset($biografia['nombre_completo']) ? sanitize($biografia['nombre_completo']) : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="bio_titulo" class="form-label">Título Profesional</label>
                                <input type="text" class="form-control" id="bio_titulo" name="titulo_profesional" 
                                       value="<?php echo isset($biografia['titulo_profesional']) ? sanitize($biografia['titulo_profesional']) : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="bio_desc_corta" class="form-label">Descripción Corta</label>
                                <textarea class="form-control" id="bio_desc_corta" name="descripcion_corta" rows="3" required><?php echo isset($biografia['descripcion_corta']) ? sanitize($biografia['descripcion_corta']) : ''; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="bio_desc_larga" class="form-label">Descripción Larga</label>
                                <textarea class="form-control" id="bio_desc_larga" name="descripcion_larga" rows="6" required><?php echo isset($biografia['descripcion_larga']) ? sanitize($biografia['descripcion_larga']) : ''; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="bio_avatar" class="form-label">Avatar (opcional)</label>
                                <input type="file" class="form-control" id="bio_avatar" name="avatar" accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-dark">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                        </form>
                    </div>

                    <!-- Tab Habilidades -->
                    <div class="tab-pane fade" id="habilidades-panel" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="mb-0">Gestionar Habilidades</h3>
                            <button class="btn btn-dark" onclick="openHabilidadModal()">
                                <i class="fas fa-plus me-2"></i>Agregar Nueva Habilidad
                            </button>
                        </div>
                        <div id="habilidadesMessage"></div>
                        <div id="habilidadesList">
                            <!-- Se carga dinámicamente -->
                        </div>
                    </div>

                    <!-- Tab Tecnologías -->
                    <div class="tab-pane fade" id="tecnologias-panel" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="mb-0">Gestionar Tecnologías</h3>
                            <button class="btn btn-dark" onclick="openTecnologiaModal()">
                                <i class="fas fa-plus me-2"></i>Agregar Nueva Tecnología
                            </button>
                        </div>
                        <div id="tecnologiasMessage"></div>
                        <div id="tecnologiasList">
                            <!-- Se carga dinámicamente -->
                        </div>
                    </div>

                    <!-- Tab Proyectos -->
                    <div class="tab-pane fade" id="proyectos-panel" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="mb-0">Gestionar Proyectos</h3>
                            <button class="btn btn-dark" onclick="openProyectoModal()">
                                <i class="fas fa-plus me-2"></i>Agregar Nuevo Proyecto
                            </button>
                        </div>
                        <div id="proyectosMessage"></div>
                        <div id="proyectosList" class="row">
                            <!-- Se carga dinámicamente -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Habilidad -->
    <div class="modal fade" id="habilidadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="habilidadModalTitle">Agregar Habilidad</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="habilidadForm">
                        <input type="hidden" id="hab_id" name="id">
                        <div class="mb-3">
                            <label for="hab_nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="hab_nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="hab_icono" class="form-label">Icono Font Awesome</label>
                            <input type="text" class="form-control" id="hab_icono" name="icono" 
                                   placeholder="Ej: fa-html5" required>
                        </div>
                        <div class="mb-3">
                            <label for="hab_color" class="form-label">Color</label>
                            <input type="color" class="form-control form-control-color" id="hab_color" 
                                   name="color" value="#000000" required>
                        </div>
                        <div class="mb-3">
                            <label for="hab_orden" class="form-label">Orden</label>
                            <input type="number" class="form-control" id="hab_orden" name="orden" value="0" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-dark" onclick="saveHabilidad()">
                        <i class="fas fa-save me-2"></i>Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tecnología -->
    <div class="modal fade" id="tecnologiaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="tecnologiaModalTitle">Agregar Tecnología</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="tecnologiaForm">
                        <input type="hidden" id="tec_id" name="id">
                        <div class="mb-3">
                            <label for="tec_nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="tec_nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="tec_nivel" class="form-label">Nivel: <span id="nivelValue">50</span>%</label>
                            <input type="range" class="form-range" id="tec_nivel" name="nivel" 
                                   min="0" max="100" value="50" oninput="document.getElementById('nivelValue').textContent = this.value">
                        </div>
                        <div class="mb-3">
                            <label for="tec_orden" class="form-label">Orden</label>
                            <input type="number" class="form-control" id="tec_orden" name="orden" value="0" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-dark" onclick="saveTecnologia()">
                        <i class="fas fa-save me-2"></i>Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Proyecto -->
    <div class="modal fade" id="proyectoModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="proyectoModalTitle">Agregar Proyecto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="proyectoForm" enctype="multipart/form-data">
                        <input type="hidden" id="proy_id" name="id">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="proy_titulo" class="form-label">Título</label>
                                <input type="text" class="form-control" id="proy_titulo" name="titulo" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="proy_fecha" class="form-label">Fecha de Creación</label>
                                <input type="date" class="form-control" id="proy_fecha" name="fecha_creacion">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="proy_descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="proy_descripcion" name="descripcion" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="proy_imagen" class="form-label">Imagen (opcional)</label>
                            <input type="file" class="form-control" id="proy_imagen" name="imagen" accept="image/*">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="proy_url_demo" class="form-label">URL Demo</label>
                                <input type="url" class="form-control" id="proy_url_demo" name="url_demo">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="proy_url_github" class="form-label">URL GitHub</label>
                                <input type="url" class="form-control" id="proy_url_github" name="url_github">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="proy_tecnologias" class="form-label">Tecnologías Usadas</label>
                            <input type="text" class="form-control" id="proy_tecnologias" name="tecnologias_usadas" 
                                   placeholder="PHP,MySQL,Bootstrap" required>
                            <small class="text-muted">Separa las tecnologías con comas</small>
                        </div>
                        <div class="mb-3">
                            <label for="proy_orden" class="form-label">Orden</label>
                            <input type="number" class="form-control" id="proy_orden" name="orden" value="0" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-dark" onclick="saveProyecto()">
                        <i class="fas fa-save me-2"></i>Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin.js"></script>
</body>
</html>