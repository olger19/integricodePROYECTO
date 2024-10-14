-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 01-10-2024 a las 01:26:27
-- Versión del servidor: 8.0.30
-- Versión de PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `plagio`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades`
--

CREATE TABLE `actividades` (
  `id` int NOT NULL,
  `titulo` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `fechaf` datetime NOT NULL,
  `fechai` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `curso` int NOT NULL,
  `estado` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `actividades`
--

INSERT INTO `actividades` (`id`, `titulo`, `descripcion`, `fechaf`, `fechai`, `curso`, `estado`) VALUES
(1, 'Subir el codigo', 'Crear un programa para sumar 2 números', '2024-10-07 15:28:00', '2024-09-30', 3, 1),
(2, 'Prueba', 'esta es una prueba de actividad', '2024-10-01 15:32:00', '2024-09-30', 3, 1),
(3, 'prueba 2', 'esta es una prueba 2', '2024-10-01 15:33:00', '2024-09-30', 3, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `id` int NOT NULL,
  `nombre` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `aula` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `usuario` int NOT NULL,
  `cod` varchar(6) COLLATE utf8mb4_general_ci NOT NULL,
  `estado` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id`, `nombre`, `aula`, `usuario`, `cod`, `estado`) VALUES
(3, 'Desarrollo de videojuegos', '306a', 7, '923BA0', 1),
(4, 'Algoritmos 1', '306a', 7, 'B8FF6E', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalleact`
--

CREATE TABLE `detalleact` (
  `id` int NOT NULL,
  `alumno` int NOT NULL,
  `url` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `similitud` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `actividad` int NOT NULL,
  `rutatxt` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `estado` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalleact`
--

INSERT INTO `detalleact` (`id`, `alumno`, `url`, `similitud`, `actividad`, `rutatxt`, `estado`) VALUES
(1, 5, 'https://www.online-java.com/eX7GMClJRs', '10', 1, '../server/codigos/54ACE01120240930191311.txt', 1),
(2, 6, 'https://www.online-java.com/Sn6VaZ80Yv', '20', 1, '../server/codigos/6D0442D120240930193939.txt', 1),
(3, 8, 'https://www.online-java.com/5kXHIKSbFA', '10', 1, '../server/codigos/87E5AF7120240930202027.txt', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallecurso`
--

CREATE TABLE `detallecurso` (
  `curso` int NOT NULL,
  `alumno` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detallecurso`
--

INSERT INTO `detallecurso` (`curso`, `alumno`) VALUES
(3, 5),
(4, 5),
(3, 6),
(3, 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `correo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `clave` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `apellidos` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `rol` int NOT NULL,
  `estado` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `correo`, `clave`, `nombre`, `apellidos`, `rol`, `estado`) VALUES
(5, 'prueba@gmail.com', '$2y$10$OO5BwCQOuuxmL40k6Nbwxe1ImYr3d2hOzBwTp9hqcGuRFhLsHQe5G', 'Alumno', 'Prueba', 2, 1),
(6, 'alumno@gmail.com', '$2y$10$351QniN3M9Yqy2lOG8dFpu8Wu/bh3cJ7H8RuiGa60bCdhs0Q7BUBm', 'Prueba2', 'prueba', 2, 1),
(7, 'profesor@gmail.com', '$2y$10$l5RgUkv4wgrOOQdYZwH6jeoAOZpd/4yA3m/lpxUqO9XBaIZbbx9XO', 'Profesor', 'Prueba', 1, 1),
(8, 'julian@gmail.com', '$2y$10$WKbcngzQR3qvekyXu4.Z8e3AQmYCxOjKTe7wy/TDBGLlwVAnlIGeK', 'Julian', 'Mora', 2, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_curso` (`curso`);

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_usuario` (`usuario`);

--
-- Indices de la tabla `detalleact`
--
ALTER TABLE `detalleact`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_actividades` (`actividad`),
  ADD KEY `fk_alumno` (`alumno`);

--
-- Indices de la tabla `detallecurso`
--
ALTER TABLE `detallecurso`
  ADD KEY `fk_alumnos` (`alumno`),
  ADD KEY `fk_cursos` (`curso`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividades`
--
ALTER TABLE `actividades`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `detalleact`
--
ALTER TABLE `detalleact`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD CONSTRAINT `fk_curso` FOREIGN KEY (`curso`) REFERENCES `cursos` (`id`);

--
-- Filtros para la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD CONSTRAINT `fk_usuario` FOREIGN KEY (`usuario`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `detalleact`
--
ALTER TABLE `detalleact`
  ADD CONSTRAINT `fk_actividades` FOREIGN KEY (`actividad`) REFERENCES `actividades` (`id`),
  ADD CONSTRAINT `fk_alumno` FOREIGN KEY (`alumno`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `detallecurso`
--
ALTER TABLE `detallecurso`
  ADD CONSTRAINT `fk_alumnos` FOREIGN KEY (`alumno`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_cursos` FOREIGN KEY (`curso`) REFERENCES `cursos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
