-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 22-05-2026 a las 21:45:29
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `portafolio_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `email`, `created_at`) VALUES
(3, 'Boodem', '$2y$10$oIOKjuqWOf3RvRuKc//8NeyuYdEYmCkcVTfdnzowaCa4crpVIaHBC', 'Bonny7939@gmail.com', '2026-05-22 18:57:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `biografia`
--

CREATE TABLE `biografia` (
  `id` int(11) NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `titulo_profesional` varchar(150) NOT NULL,
  `descripcion_corta` text NOT NULL,
  `descripcion_larga` text NOT NULL,
  `avatar` varchar(255) DEFAULT 'avatar.jpg',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `biografia`
--

INSERT INTO `biografia` (`id`, `nombre_completo`, `titulo_profesional`, `descripcion_corta`, `descripcion_larga`, `avatar`, `updated_at`) VALUES
(1, 'Sebastian Flores Araya', 'Desarrollador Web Full Stack', 'Soy un estudiante aprendiendo   el desarrollo web junto a la creación de aplicaciones modernas y responsivas. Me estoy especializando en tecnologías front-end y back-end, con un enfoque en crear experiencias de usuario excepcionales.', 'Actualmente me encuentro ampliando mis conocimientos en desarrollo full stack, trabajando con PHP, MySQL, JavaScript y frameworks modernos.', 'avatar.jpg', '2026-05-22 19:17:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contactos`
--

CREATE TABLE `contactos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `asunto` varchar(150) NOT NULL,
  `mensaje` text NOT NULL,
  `leido` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `contactos`
--

INSERT INTO `contactos` (`id`, `nombre`, `email`, `asunto`, `mensaje`, `leido`, `created_at`) VALUES
(1, 'Nombre test', 'Elheavypacifista@gmail.com', 'Test de asunto', 'Test de mensaje', 0, '2026-05-22 17:56:18'),
(2, 'Nombre test', 'Elheavypacifista@gmail.com', 'Test de asunto', 'Test de mensaje', 0, '2026-05-22 17:56:18'),
(3, 'Nombre test', 'Elheavypacifista@gmail.com', 'Test de asunto', 'test de mensaje', 0, '2026-05-22 19:18:01'),
(4, 'Nombre test', 'Elheavypacifista@gmail.com', 'Test de asunto', 'test de mensaje', 0, '2026-05-22 19:18:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `habilidades`
--

CREATE TABLE `habilidades` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `icono` varchar(50) NOT NULL,
  `color` varchar(20) NOT NULL,
  `orden` int(11) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `habilidades`
--

INSERT INTO `habilidades` (`id`, `nombre`, `icono`, `color`, `orden`, `activo`) VALUES
(1, 'HTML', 'fa-html5', '#E44D26', 1, 1),
(2, 'CSS', 'fa-css3-alt', '#1572B6', 2, 1),
(3, 'JavaScript', 'fa-js', '#F7DF1E', 3, 1),
(4, 'PHP', 'fa-php', '#777BB4', 4, 1),
(5, 'MySQL', 'fa-database', '#4479A1', 5, 1),
(6, 'Bootstrap', 'fa-bootstrap', '#7952B3', 6, 1),
(7, 'GitHub', 'fa-github', '#181717', 7, 1),
(8, 'IA en Desarrollo Web', 'fa-brain', '#FF6B6B', 8, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos`
--

CREATE TABLE `proyectos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `imagen` varchar(255) DEFAULT 'default-project.jpg',
  `url_demo` varchar(255) DEFAULT NULL,
  `url_github` varchar(255) DEFAULT NULL,
  `tecnologias_usadas` varchar(255) NOT NULL,
  `fecha_creacion` date DEFAULT NULL,
  `orden` int(11) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tecnologias`
--

CREATE TABLE `tecnologias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `nivel` int(11) NOT NULL CHECK (`nivel` >= 0 and `nivel` <= 100),
  `orden` int(11) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tecnologias`
--

INSERT INTO `tecnologias` (`id`, `nombre`, `nivel`, `orden`, `activo`) VALUES
(1, 'HTML/CSS', 90, 1, 1),
(2, 'JavaScript', 85, 2, 1),
(3, 'PHP', 75, 3, 1),
(4, 'MySQL', 70, 4, 1),
(5, 'Bootstrap', 80, 5, 1),
(6, 'CSS', 85, 6, 1),
(7, 'Git/GitHub', 75, 7, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indices de la tabla `biografia`
--
ALTER TABLE `biografia`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `habilidades`
--
ALTER TABLE `habilidades`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tecnologias`
--
ALTER TABLE `tecnologias`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `biografia`
--
ALTER TABLE `biografia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `contactos`
--
ALTER TABLE `contactos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `habilidades`
--
ALTER TABLE `habilidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tecnologias`
--
ALTER TABLE `tecnologias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
