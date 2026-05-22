<!-- Footer -->
<footer class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h5 class="fw-bold mb-3">
                    <i class="fas fa-code me-2"></i>
                    <?php echo isset($biografia['nombre_completo']) ? sanitize($biografia['nombre_completo']) : 'Sebastian Flores Araya'; ?>
                </h5>
                <p class="text-muted">
                    <?php echo isset($biografia['descripcion_corta']) ? sanitize($biografia['descripcion_corta']) : ''; ?>
                </p>
            </div>
            <div class="col-lg-4 mb-4">
                <h5 class="fw-bold mb-3">Enlaces Rápidos</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#biografia" class="text-decoration-none text-muted">Biografía</a></li>
                    <li class="mb-2"><a href="#habilidades" class="text-decoration-none text-muted">Habilidades</a></li>
                    <li class="mb-2"><a href="#tecnologias" class="text-decoration-none text-muted">Tecnologías</a></li>
                    <li class="mb-2"><a href="#proyectos" class="text-decoration-none text-muted">Proyectos</a></li>
                    <li><a href="#contacto" class="text-decoration-none text-muted">Contacto</a></li>
                </ul>
            </div>
            <div class="col-lg-4 mb-4">
                <h5 class="fw-bold mb-3">Conecta Conmigo</h5>
                <div class="social-links">
                    <a href="https://github.com/sebastianflores" target="_blank" class="text-dark me-3 fs-4" title="GitHub">
                        <i class="fab fa-github"></i>
                    </a>
                    <a href="https://linkedin.com/in/sebastianflores" target="_blank" class="text-dark me-3 fs-4" title="LinkedIn">
                        <i class="fab fa-linkedin"></i>
                    </a>
                    <a href="https://twitter.com/sebastianflores" target="_blank" class="text-dark me-3 fs-4" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="mailto:sebastian@example.com" class="text-dark fs-4" title="Email">
                        <i class="fas fa-envelope"></i>
                    </a>
                </div>
            </div>
        </div>
        <hr class="my-4">
        <div class="text-center text-muted">
            <p class="mb-0">&copy; 2026 <?php echo isset($biografia['nombre_completo']) ? sanitize($biografia['nombre_completo']) : 'Sebastian Flores Araya'; ?>. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- JavaScript personalizado -->
<script src="assets/js/main.js"></script>
</body>
</html>