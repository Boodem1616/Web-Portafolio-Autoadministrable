-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS portafolio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE portafolio_db;

-- Tabla de usuarios administradores
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar usuario admin (contraseña: Kkck12345#)
INSERT INTO admin_users (username, password, email) VALUES 
('admin', '$2y$10$ugFflx2/x0uOaHUEESmiUep8wgl1ugmI9by0bFhlPIXLB7X1gBZRG', 'admin@portafolio.com');

-- Tabla de biografía
CREATE TABLE biografia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    titulo_profesional VARCHAR(150) NOT NULL,
    descripcion_corta TEXT NOT NULL,
    descripcion_larga TEXT NOT NULL,
    avatar VARCHAR(255) DEFAULT 'avatar.jpg',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar datos iniciales de biografía
INSERT INTO biografia (nombre_completo, titulo_profesional, descripcion_corta, descripcion_larga) VALUES 
('Sebastian Flores Araya', 
'Desarrollador Web Full Stack', 
'Soy un estudiante apasionado por el desarrollo web con experiencia en la creación de aplicaciones modernas y responsivas. Me especializo en tecnologías front-end y back-end, con un enfoque en crear experiencias de usuario excepcionales.',
'Actualmente me encuentro ampliando mis conocimientos en desarrollo full stack, trabajando con PHP, MySQL, JavaScript y frameworks modernos. Me motiva resolver problemas complejos y aprender nuevas tecnologías constantemente.');

-- Tabla de habilidades
CREATE TABLE habilidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    icono VARCHAR(50) NOT NULL,
    color VARCHAR(20) NOT NULL,
    orden INT DEFAULT 0,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar habilidades iniciales
INSERT INTO habilidades (nombre, icono, color, orden) VALUES 
('HTML', 'fa-html5', '#E44D26', 1),
('CSS', 'fa-css3-alt', '#1572B6', 2),
('JavaScript', 'fa-js', '#F7DF1E', 3),
('PHP', 'fa-php', '#777BB4', 4),
('MySQL', 'fa-database', '#4479A1', 5),
('Bootstrap', 'fa-bootstrap', '#7952B3', 6),
('GitHub', 'fa-github', '#181717', 7),
('IA en Desarrollo Web', 'fa-brain', '#FF6B6B', 8);

-- Tabla de tecnologías
CREATE TABLE tecnologias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    nivel INT NOT NULL CHECK (nivel >= 0 AND nivel <= 100),
    orden INT DEFAULT 0,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar tecnologías iniciales
INSERT INTO tecnologias (nombre, nivel, orden) VALUES 
('HTML/CSS', 90, 1),
('JavaScript', 85, 2),
('PHP', 75, 3),
('MySQL', 70, 4),
('Bootstrap', 80, 5),
('CSS', 85, 6),
('Git/GitHub', 75, 7);

-- Tabla de proyectos
CREATE TABLE proyectos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    imagen VARCHAR(255) DEFAULT 'default-project.jpg',
    url_demo VARCHAR(255),
    url_github VARCHAR(255),
    tecnologias_usadas VARCHAR(255) NOT NULL,
    fecha_creacion DATE,
    orden INT DEFAULT 0,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar proyectos iniciales
INSERT INTO proyectos (titulo, descripcion, tecnologias_usadas, url_demo, url_github, fecha_creacion, orden) VALUES 
('Sistema de Gestión de Tareas', 'Aplicación web para gestionar tareas con autenticación de usuarios, creada con PHP y MySQL.', 'PHP,MySQL,Bootstrap,JavaScript', '#', '#', '2026-01-15', 1),
('E-commerce Responsive', 'Tienda en línea completamente responsive con carrito de compras y sistema de pago.', 'HTML,CSS,JavaScript,PHP', '#', '#', '2026-02-20', 2),
('Blog Personal', 'Blog con sistema de administración de contenido, comentarios y categorías.', 'PHP,MySQL,Bootstrap,AJAX', '#', '#', '2026-03-10', 3),
('Dashboard Analítico', 'Panel de control con gráficas y visualización de datos en tiempo real.', 'JavaScript,Chart.js,PHP,MySQL', '#', '#', '2026-04-05', 4);

-- Tabla de contactos
CREATE TABLE contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    asunto VARCHAR(150) NOT NULL,
    mensaje TEXT NOT NULL,
    leido TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;