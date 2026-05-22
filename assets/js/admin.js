// Variables globales
let habilidadModal, tecnologiaModal, proyectoModal;

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar modales de Bootstrap
    habilidadModal = new bootstrap.Modal(document.getElementById('habilidadModal'));
    tecnologiaModal = new bootstrap.Modal(document.getElementById('tecnologiaModal'));
    proyectoModal = new bootstrap.Modal(document.getElementById('proyectoModal'));
    
    // Cargar datos iniciales
    loadHabilidades();
    loadTecnologias();
    loadProyectos();
    
    // Configurar formulario de biografía
    setupBiografiaForm();
    
    // Activar tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// ============ BIOGRAFÍA ============
function setupBiografiaForm() {
    const form = document.getElementById('biografiaForm');
    if (!form) return;
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch('ajax/save_biografia.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            showMessage('biografiaMessage', data.success, data.message);
            
        } catch (error) {
            showMessage('biografiaMessage', false, 'Error al guardar la biografía');
            console.error('Error:', error);
        }
    });
}

// ============ HABILIDADES ============
async function loadHabilidades() {
    try {
        const response = await fetch('../../config/database.php');
        const listDiv = document.getElementById('habilidadesList');
        
        const response2 = await fetch('ajax/get_habilidades.php');
        const data = await response2.json();
        
        if (data.success && data.habilidades) {
            let html = '<div class="list-group">';
            data.habilidades.forEach(hab => {
                html += `
                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fab ${hab.icono} me-2" style="color: ${hab.color}; font-size: 1.5rem;"></i>
                            <strong>${hab.nombre}</strong>
                            <span class="badge bg-secondary ms-2">Orden: ${hab.orden}</span>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-primary me-2" onclick="editHabilidad(${hab.id})">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteHabilidad(${hab.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            listDiv.innerHTML = html;
        }
    } catch (error) {
        console.error('Error loading habilidades:', error);
    }
}

function openHabilidadModal(id = null) {
    const form = document.getElementById('habilidadForm');
    form.reset();
    
    if (id) {
        document.getElementById('habilidadModalTitle').textContent = 'Editar Habilidad';
        document.getElementById('hab_id').value = id;
        // Cargar datos de la habilidad
        loadHabilidadData(id);
    } else {
        document.getElementById('habilidadModalTitle').textContent = 'Agregar Habilidad';
        document.getElementById('hab_id').value = '';
    }
    
    habilidadModal.show();
}

async function loadHabilidadData(id) {
    try {
        const response = await fetch(`ajax/get_habilidad.php?id=${id}`);
        const data = await response.json();
        
        if (data.success && data.habilidad) {
            document.getElementById('hab_nombre').value = data.habilidad.nombre;
            document.getElementById('hab_icono').value = data.habilidad.icono;
            document.getElementById('hab_color').value = data.habilidad.color;
            document.getElementById('hab_orden').value = data.habilidad.orden;
        }
    } catch (error) {
        console.error('Error loading habilidad:', error);
    }
}

function editHabilidad(id) {
    openHabilidadModal(id);
}

async function saveHabilidad() {
    const form = document.getElementById('habilidadForm');
    const formData = new FormData(form);
    const id = document.getElementById('hab_id').value;
    
    const url = id ? 'ajax/update_habilidad.php' : 'ajax/add_habilidad.php';
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            habilidadModal.hide();
            loadHabilidades();
            showMessage('habilidadesMessage', true, data.message);
        } else {
            showMessage('habilidadesMessage', false, data.message);
        }
    } catch (error) {
        console.error('Error saving habilidad:', error);
        showMessage('habilidadesMessage', false, 'Error al guardar la habilidad');
    }
}

async function deleteHabilidad(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar esta habilidad?')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('id', id);
        
        const response = await fetch('ajax/delete_habilidad.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            loadHabilidades();
            showMessage('habilidadesMessage', true, data.message);
        } else {
            showMessage('habilidadesMessage', false, data.message);
        }
    } catch (error) {
        console.error('Error deleting habilidad:', error);
        showMessage('habilidadesMessage', false, 'Error al eliminar la habilidad');
    }
}

// ============ TECNOLOGÍAS ============
async function loadTecnologias() {
    try {
        const response = await fetch('ajax/get_tecnologias.php');
        const data = await response.json();
        
        if (data.success && data.tecnologias) {
            let html = '<div class="list-group">';
            data.tecnologias.forEach(tec => {
                html += `
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>${tec.nombre}</strong>
                                <span class="badge bg-dark ms-2">${tec.nivel}%</span>
                                <span class="badge bg-secondary ms-1">Orden: ${tec.orden}</span>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-2" onclick="editTecnologia(${tec.id})">
                                    <i class="fas fa-pencil-alt"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteTecnologia(${tec.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-dark" role="progressbar" 
                                 style="width: ${tec.nivel}%" 
                                 aria-valuenow="${tec.nivel}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            document.getElementById('tecnologiasList').innerHTML = html;
        }
    } catch (error) {
        console.error('Error loading tecnologias:', error);
    }
}

function openTecnologiaModal(id = null) {
    const form = document.getElementById('tecnologiaForm');
    form.reset();
    document.getElementById('nivelValue').textContent = '50';
    
    if (id) {
        document.getElementById('tecnologiaModalTitle').textContent = 'Editar Tecnología';
        document.getElementById('tec_id').value = id;
        loadTecnologiaData(id);
    } else {
        document.getElementById('tecnologiaModalTitle').textContent = 'Agregar Tecnología';
        document.getElementById('tec_id').value = '';
    }
    
    tecnologiaModal.show();
}

async function loadTecnologiaData(id) {
    try {
        const response = await fetch(`ajax/get_tecnologia.php?id=${id}`);
        const data = await response.json();
        
        if (data.success && data.tecnologia) {
            document.getElementById('tec_nombre').value = data.tecnologia.nombre;
            document.getElementById('tec_nivel').value = data.tecnologia.nivel;
            document.getElementById('nivelValue').textContent = data.tecnologia.nivel;
            document.getElementById('tec_orden').value = data.tecnologia.orden;
        }
    } catch (error) {
        console.error('Error loading tecnologia:', error);
    }
}

function editTecnologia(id) {
    openTecnologiaModal(id);
}

async function saveTecnologia() {
    const form = document.getElementById('tecnologiaForm');
    const formData = new FormData(form);
    const id = document.getElementById('tec_id').value;
    
    const url = id ? 'ajax/update_tecnologia.php' : 'ajax/add_tecnologia.php';
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            tecnologiaModal.hide();
            loadTecnologias();
            showMessage('tecnologiasMessage', true, data.message);
        } else {
            showMessage('tecnologiasMessage', false, data.message);
        }
    } catch (error) {
        console.error('Error saving tecnologia:', error);
        showMessage('tecnologiasMessage', false, 'Error al guardar la tecnología');
    }
}

async function deleteTecnologia(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar esta tecnología?')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('id', id);
        
        const response = await fetch('ajax/delete_tecnologia.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            loadTecnologias();
            showMessage('tecnologiasMessage', true, data.message);
        } else {
            showMessage('tecnologiasMessage', false, data.message);
        }
    } catch (error) {
        console.error('Error deleting tecnologia:', error);
        showMessage('tecnologiasMessage', false, 'Error al eliminar la tecnología');
    }
}

// ============ PROYECTOS ============
async function loadProyectos() {
    try {
        const response = await fetch('ajax/get_proyectos.php');
        const data = await response.json();
        
        if (data.success && data.proyectos) {
            let html = '';
            data.proyectos.forEach(proy => {
                const techs = proy.tecnologias_usadas.split(',');
                let badgesHtml = techs.map(tech => 
                    `<span class="badge bg-dark me-1 mb-1">${tech.trim()}</span>`
                ).join('');
                
                html += `
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">${proy.titulo}</h5>
                                <p class="card-text">${proy.descripcion}</p>
                                <div class="mb-2">${badgesHtml}</div>
                                <p class="small text-muted">
                                    <i class="fas fa-calendar me-1"></i>${proy.fecha_creacion}
                                    <span class="ms-3">Orden: ${proy.orden}</span>
                                </p>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary" onclick="editProyecto(${proy.id})">
                                        <i class="fas fa-pencil-alt me-1"></i>Editar
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteProyecto(${proy.id})">
                                        <i class="fas fa-trash me-1"></i>Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            document.getElementById('proyectosList').innerHTML = html;
        }
    } catch (error) {
        console.error('Error loading proyectos:', error);
    }
}

function openProyectoModal(id = null) {
    const form = document.getElementById('proyectoForm');
    form.reset();
    
    if (id) {
        document.getElementById('proyectoModalTitle').textContent = 'Editar Proyecto';
        document.getElementById('proy_id').value = id;
        loadProyectoData(id);
    } else {
        document.getElementById('proyectoModalTitle').textContent = 'Agregar Proyecto';
        document.getElementById('proy_id').value = '';
        document.getElementById('proy_fecha').value = new Date().toISOString().split('T')[0];
    }
    
    proyectoModal.show();
}

async function loadProyectoData(id) {
    try {
        const response = await fetch(`ajax/get_proyecto.php?id=${id}`);
        const data = await response.json();
        
        if (data.success && data.proyecto) {
            document.getElementById('proy_titulo').value = data.proyecto.titulo;
            document.getElementById('proy_descripcion').value = data.proyecto.descripcion;
            document.getElementById('proy_url_demo').value = data.proyecto.url_demo || '';
            document.getElementById('proy_url_github').value = data.proyecto.url_github || '';
            document.getElementById('proy_tecnologias').value = data.proyecto.tecnologias_usadas;
            document.getElementById('proy_fecha').value = data.proyecto.fecha_creacion;
            document.getElementById('proy_orden').value = data.proyecto.orden;
        }
    } catch (error) {
        console.error('Error loading proyecto:', error);
    }
}

function editProyecto(id) {
    openProyectoModal(id);
}

async function saveProyecto() {
    const form = document.getElementById('proyectoForm');
    const formData = new FormData(form);
    const id = document.getElementById('proy_id').value;
    
    const url = id ? 'ajax/update_proyecto.php' : 'ajax/add_proyecto.php';
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            proyectoModal.hide();
            loadProyectos();
            showMessage('proyectosMessage', true, data.message);
        } else {
            showMessage('proyectosMessage', false, data.message);
        }
    } catch (error) {
        console.error('Error saving proyecto:', error);
        showMessage('proyectosMessage', false, 'Error al guardar el proyecto');
    }
}

async function deleteProyecto(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar este proyecto? Esta acción no se puede deshacer.')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('id', id);
        
        const response = await fetch('ajax/delete_proyecto.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            loadProyectos();
            showMessage('proyectosMessage', true, data.message);
        } else {
            showMessage('proyectosMessage', false, data.message);
        }
    } catch (error) {
        console.error('Error deleting proyecto:', error);
        showMessage('proyectosMessage', false, 'Error al eliminar el proyecto');
    }
}

// ============ FUNCIONES AUXILIARES ============
function showMessage(elementId, success, message) {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    const alertClass = success ? 'alert-success' : 'alert-danger';
    const icon = success ? 'check-circle' : 'exclamation-circle';
    
    element.innerHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas fa-${icon} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Scroll al mensaje
    element.scrollIntoView({ behavior: 'smooth', block: 'center' });
    
    // Auto-destruir después de 5 segundos
    setTimeout(() => {
        element.innerHTML = '';
    }, 5000);
}