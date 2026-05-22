// Smooth scroll para enlaces del navbar
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Animación de barras de progreso con Intersection Observer
    const progressBars = document.querySelectorAll('.progress-bar');
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px'
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const progressBar = entry.target;
                const width = progressBar.getAttribute('data-width');
                progressBar.style.width = width;
                observer.unobserve(progressBar);
            }
        });
    }, observerOptions);

    progressBars.forEach(bar => {
        observer.observe(bar);
    });

    // Validación del formulario de contacto
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Validación Bootstrap
            if (!this.checkValidity()) {
                e.stopPropagation();
                this.classList.add('was-validated');
                return;
            }

            // Recopilar datos del formulario
            const formData = new FormData(this);
            
            try {
                const response = await fetch('contacto_handler.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                const messageDiv = document.getElementById('contactMessage');
                
                if (data.success) {
                    messageDiv.innerHTML = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    this.reset();
                    this.classList.remove('was-validated');
                } else {
                    messageDiv.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                }
                
                // Scroll al mensaje
                messageDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('contactMessage').innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>Error al enviar el mensaje. Por favor, intenta de nuevo.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
            }
        });
    }

    // Validación del formulario de login
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('admin/login.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                const messageDiv = document.getElementById('loginMessage');
                
                if (data.success) {
                    messageDiv.innerHTML = `
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i>${data.message}
                        </div>
                    `;
                    setTimeout(() => {
                        window.location.href = 'admin/dashboard.php';
                    }, 1000);
                } else {
                    messageDiv.innerHTML = `
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>${data.message}
                        </div>
                    `;
                }
                
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('loginMessage').innerHTML = `
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>Error al iniciar sesión. Por favor, intenta de nuevo.
                    </div>
                `;
            }
        });
    }

    // Cerrar menú móvil al hacer clic en un enlace
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    const menuToggle = document.getElementById('navbarNav');
    const bsCollapse = new bootstrap.Collapse(menuToggle, {toggle: false});
    
    navLinks.forEach((l) => {
        l.addEventListener('click', () => {
            if (window.innerWidth < 992) {
                bsCollapse.toggle();
            }
        });
    });
});