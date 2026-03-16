-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: localhost    Database: nixolist
-- ------------------------------------------------------
-- Server version	5.7.43-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `actividad_feed`
--

DROP TABLE IF EXISTS `actividad_feed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `actividad_feed` (
  `id_actividad` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_media` int(11) DEFAULT NULL,
  `type` enum('puntuado','resenado','agregado','favorito') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_actividad`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_media` (`id_media`),
  CONSTRAINT `actividad_feed_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `actividad_feed_ibfk_2` FOREIGN KEY (`id_media`) REFERENCES `media` (`id_media`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actividad_feed`
--

LOCK TABLES `actividad_feed` WRITE;
/*!40000 ALTER TABLE `actividad_feed` DISABLE KEYS */;
/*!40000 ALTER TABLE `actividad_feed` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `amigos`
--

DROP TABLE IF EXISTS `amigos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amigos` (
  `id_amigo` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_amigo_usuario` int(11) NOT NULL,
  `status` enum('pendiente','aceptado','bloqueado') DEFAULT 'pendiente',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_amigo`),
  UNIQUE KEY `id_usuario` (`id_usuario`,`id_amigo_usuario`),
  KEY `id_amigo_usuario` (`id_amigo_usuario`),
  CONSTRAINT `amigos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `amigos_ibfk_2` FOREIGN KEY (`id_amigo_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `amigos`
--

LOCK TABLES `amigos` WRITE;
/*!40000 ALTER TABLE `amigos` DISABLE KEYS */;
/*!40000 ALTER TABLE `amigos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estadisticas_media`
--

DROP TABLE IF EXISTS `estadisticas_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estadisticas_media` (
  `id_media` int(11) NOT NULL,
  `puntuacion_media` decimal(3,2) DEFAULT '0.00',
  `total_puntuaciones` int(11) DEFAULT '0',
  `total_favoritos` int(11) DEFAULT '0',
  PRIMARY KEY (`id_media`),
  CONSTRAINT `estadisticas_media_ibfk_1` FOREIGN KEY (`id_media`) REFERENCES `media` (`id_media`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estadisticas_media`
--

LOCK TABLES `estadisticas_media` WRITE;
/*!40000 ALTER TABLE `estadisticas_media` DISABLE KEYS */;
/*!40000 ALTER TABLE `estadisticas_media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estdisticas_usuario`
--

DROP TABLE IF EXISTS `estdisticas_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estdisticas_usuario` (
  `id_usuario` int(11) NOT NULL,
  `animes_completados` int(11) DEFAULT '0',
  `peliculas_completadas` int(11) DEFAULT '0',
  `tv_completadas` int(11) DEFAULT '0',
  `libros_completados` int(11) DEFAULT '0',
  `puntuacion_media` decimal(3,2) DEFAULT '0.00',
  PRIMARY KEY (`id_usuario`),
  CONSTRAINT `estdisticas_usuario_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estdisticas_usuario`
--

LOCK TABLES `estdisticas_usuario` WRITE;
/*!40000 ALTER TABLE `estdisticas_usuario` DISABLE KEYS */;
/*!40000 ALTER TABLE `estdisticas_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `generos`
--

DROP TABLE IF EXISTS `generos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `generos` (
  `id_genero` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_genero`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `generos`
--

LOCK TABLES `generos` WRITE;
/*!40000 ALTER TABLE `generos` DISABLE KEYS */;
/*!40000 ALTER TABLE `generos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `juegosweb`
--

DROP TABLE IF EXISTS `juegosweb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `juegosweb` (
  `id_juegoweb` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_juegoweb`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `juegosweb`
--

LOCK TABLES `juegosweb` WRITE;
/*!40000 ALTER TABLE `juegosweb` DISABLE KEYS */;
/*!40000 ALTER TABLE `juegosweb` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `juegosweb_puntuaciones`
--

DROP TABLE IF EXISTS `juegosweb_puntuaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `juegosweb_puntuaciones` (
  `id_puntuacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_juegoweb` int(11) NOT NULL,
  `puntuacion` int(11) NOT NULL,
  `played_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_puntuacion`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_juegoweb` (`id_juegoweb`),
  CONSTRAINT `juegosweb_puntuaciones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `juegosweb_puntuaciones_ibfk_2` FOREIGN KEY (`id_juegoweb`) REFERENCES `juegosweb` (`id_juegoweb`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `juegosweb_puntuaciones`
--

LOCK TABLES `juegosweb_puntuaciones` WRITE;
/*!40000 ALTER TABLE `juegosweb_puntuaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `juegosweb_puntuaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media` (
  `id_media` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `type` enum('pelicula','tv','anime','libro') NOT NULL,
  `descripcion` text,
  `fecha_publicacion` date DEFAULT NULL,
  `portada` varchar(255) DEFAULT NULL,
  `tmdb_id` int(11) DEFAULT NULL,
  `mal_id` int(11) DEFAULT NULL,
  `episodios_totales` int(11) DEFAULT NULL,
  `temporadas_totales` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated_api` datetime DEFAULT NULL,
  PRIMARY KEY (`id_media`),
  KEY `idx_titulo` (`titulo`),
  KEY `idx_tmdb` (`tmdb_id`),
  KEY `idx_mal` (`mal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_generos`
--

DROP TABLE IF EXISTS `media_generos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_generos` (
  `id_media` int(11) NOT NULL,
  `id_genero` int(11) NOT NULL,
  PRIMARY KEY (`id_media`,`id_genero`),
  KEY `id_genero` (`id_genero`),
  CONSTRAINT `media_generos_ibfk_1` FOREIGN KEY (`id_media`) REFERENCES `media` (`id_media`) ON DELETE CASCADE,
  CONSTRAINT `media_generos_ibfk_2` FOREIGN KEY (`id_genero`) REFERENCES `generos` (`id_genero`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_generos`
--

LOCK TABLES `media_generos` WRITE;
/*!40000 ALTER TABLE `media_generos` DISABLE KEYS */;
/*!40000 ALTER TABLE `media_generos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_imagenes`
--

DROP TABLE IF EXISTS `media_imagenes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_imagenes` (
  `id_media_imagen` int(11) NOT NULL AUTO_INCREMENT,
  `id_media` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `type` enum('poster','banner','background') DEFAULT NULL,
  PRIMARY KEY (`id_media_imagen`),
  KEY `id_media` (`id_media`),
  CONSTRAINT `media_imagenes_ibfk_1` FOREIGN KEY (`id_media`) REFERENCES `media` (`id_media`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_imagenes`
--

LOCK TABLES `media_imagenes` WRITE;
/*!40000 ALTER TABLE `media_imagenes` DISABLE KEYS */;
/*!40000 ALTER TABLE `media_imagenes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_usuario`
--

DROP TABLE IF EXISTS `media_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_usuario` (
  `id_usuario_media` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_media` int(11) NOT NULL,
  `status` enum('watching','completed','planned','dropped','reading','paused') DEFAULT 'planned',
  `puntuacion` tinyint(4) DEFAULT NULL,
  `es_favorito` tinyint(1) DEFAULT '0',
  `progreso` int(11) DEFAULT '0',
  `episodios_vistos` int(11) DEFAULT '0',
  `progreso_temporadas` int(11) DEFAULT '0',
  `fecha_comienzo` date DEFAULT NULL,
  `fecha_completado` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario_media`),
  UNIQUE KEY `id_usuario` (`id_usuario`,`id_media`),
  KEY `id_media` (`id_media`),
  CONSTRAINT `media_usuario_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `media_usuario_ibfk_2` FOREIGN KEY (`id_media`) REFERENCES `media` (`id_media`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_usuario`
--

LOCK TABLES `media_usuario` WRITE;
/*!40000 ALTER TABLE `media_usuario` DISABLE KEYS */;
/*!40000 ALTER TABLE `media_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notificaciones` (
  `id_notificacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) DEFAULT NULL,
  `id_emisor` int(11) DEFAULT NULL,
  `type` enum('peticion_amistad','like_resena','actividad_amigos') DEFAULT NULL,
  `id_referencia` int(11) DEFAULT NULL,
  `leido` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_notificacion`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificaciones`
--

LOCK TABLES `notificaciones` WRITE;
/*!40000 ALTER TABLE `notificaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `notificaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resena_likes`
--

DROP TABLE IF EXISTS `resena_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resena_likes` (
  `id_usuario` int(11) NOT NULL,
  `id_resena` int(11) NOT NULL,
  PRIMARY KEY (`id_usuario`,`id_resena`),
  KEY `id_resena` (`id_resena`),
  CONSTRAINT `resena_likes_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `resena_likes_ibfk_2` FOREIGN KEY (`id_resena`) REFERENCES `resenas` (`id_resena`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resena_likes`
--

LOCK TABLES `resena_likes` WRITE;
/*!40000 ALTER TABLE `resena_likes` DISABLE KEYS */;
/*!40000 ALTER TABLE `resena_likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resenas`
--

DROP TABLE IF EXISTS `resenas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resenas` (
  `id_resena` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_media` int(11) NOT NULL,
  `texto_resena` text NOT NULL,
  `likes` int(11) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_resena`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_media` (`id_media`),
  CONSTRAINT `resenas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `resenas_ibfk_2` FOREIGN KEY (`id_media`) REFERENCES `media` (`id_media`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resenas`
--

LOCK TABLES `resenas` WRITE;
/*!40000 ALTER TABLE `resenas` DISABLE KEYS */;
/*!40000 ALTER TABLE `resenas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `bio` text,
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-16 12:43:44
