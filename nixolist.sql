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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `amigos`
--

LOCK TABLES `amigos` WRITE;
/*!40000 ALTER TABLE `amigos` DISABLE KEYS */;
INSERT INTO `amigos` VALUES (1,4,3,'aceptado','2026-04-25 16:10:25');
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
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
INSERT INTO `media` VALUES (20,'Naruto','anime','Naruto Uzumaki, a mischievous adolescent ninja...',NULL,'https://cdn.myanimelist.net/images/anime/13/17405.jpg',NULL,NULL,NULL,NULL,'2026-04-08 13:43:57',NULL),(21,'Serial Experiments Lain','anime',NULL,NULL,'https://myanimelist.net/images/anime/1718/91550l.webp',NULL,339,NULL,NULL,'2026-04-08 14:12:16',NULL),(22,'The Boys','tv',NULL,NULL,'https://image.tmdb.org/t/p/w500/5kgY14oisiHcJ4zq0Xgq1e97PHm.jpg',76479,NULL,NULL,NULL,'2026-04-08 14:21:44',NULL),(23,'Ore dake Level Up na Ken Season 2: Arise from the Shadow','anime',NULL,NULL,'https://myanimelist.net/images/anime/1448/147351l.webp',NULL,58567,NULL,NULL,'2026-04-25 12:54:44',NULL),(24,'Fullmetal Alchemist: Brotherhood','anime',NULL,NULL,'https://myanimelist.net/images/anime/1208/94745l.webp',NULL,5114,NULL,NULL,'2026-04-25 13:21:14',NULL),(25,'Boku no Hero Academia','anime',NULL,NULL,'https://myanimelist.net/images/anime/10/78745l.webp',NULL,31964,NULL,NULL,'2026-04-25 13:21:41',NULL),(26,'Steins;Gate','anime',NULL,NULL,'https://myanimelist.net/images/anime/1935/127974l.webp',NULL,9253,NULL,NULL,'2026-04-25 14:14:14',NULL),(27,'Summertime Render','anime',NULL,NULL,'https://myanimelist.net/images/anime/1120/120796l.webp',NULL,47194,NULL,NULL,'2026-04-25 14:32:53',NULL),(28,'Ikoku Nikki','anime',NULL,NULL,'https://myanimelist.net/images/anime/1791/154233l.webp',NULL,58788,NULL,NULL,'2026-04-25 14:32:57',NULL),(29,'Fate/stay night','anime',NULL,NULL,'https://myanimelist.net/images/anime/4/30327l.webp',NULL,356,NULL,NULL,'2026-04-25 14:33:02',NULL),(30,'Gyakkyou Burai Kaiji: Ultimate Survivor','anime',NULL,NULL,'https://myanimelist.net/images/anime/12/80032l.webp',NULL,3002,NULL,NULL,'2026-04-25 14:33:18',NULL),(31,'Sword Art Online','anime',NULL,NULL,'https://myanimelist.net/images/anime/11/39717l.webp',NULL,11757,NULL,NULL,'2026-04-25 14:33:25',NULL),(32,'Chainsaw Man Movie: Reze-hen','anime',NULL,NULL,'https://myanimelist.net/images/anime/1763/150638l.webp',NULL,57555,NULL,NULL,'2026-04-25 14:33:37',NULL),(33,'Sousou no Frieren','anime',NULL,NULL,'https://myanimelist.net/images/anime/1015/138006l.webp',NULL,52991,NULL,NULL,'2026-04-25 14:33:46',NULL),(34,'Mushoku Tensei III: Isekai Ittara Honki Dasu','anime',NULL,NULL,'https://myanimelist.net/images/anime/1723/154941l.webp',NULL,59193,NULL,NULL,'2026-04-25 14:34:11',NULL),(35,'Tokyo Ghoul','anime',NULL,NULL,'https://myanimelist.net/images/anime/1498/134443l.webp',NULL,22319,NULL,NULL,'2026-04-25 15:26:00',NULL),(36,'Koe no Katachi','anime',NULL,NULL,'https://myanimelist.net/images/anime/1122/96435l.webp',NULL,28851,NULL,NULL,'2026-04-25 16:26:12',NULL),(37,'Michael','pelicula',NULL,NULL,'https://image.tmdb.org/t/p/w500/2uK36ujoDXOfNiJ5Yp3raVprB51.jpg',936075,NULL,NULL,NULL,'2026-04-25 16:31:20',NULL),(38,'INVENCIBLE','tv',NULL,NULL,'https://image.tmdb.org/t/p/w500/AdcfiT5FsjUooyP7CrKzEGmP9K1.jpg',95557,NULL,NULL,NULL,'2026-04-26 14:08:39',NULL);
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
  `personaje_favorito_id` int(11) DEFAULT NULL,
  `personaje_favorito_nombre` varchar(255) DEFAULT NULL,
  `personaje_favorito_imagen` varchar(255) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_usuario`
--

LOCK TABLES `media_usuario` WRITE;
/*!40000 ALTER TABLE `media_usuario` DISABLE KEYS */;
INSERT INTO `media_usuario` VALUES (1,5,34,'planned',NULL,0,NULL,NULL,NULL,0,0,0,NULL,NULL,'2026-04-25 14:39:49','2026-04-25 14:47:46'),(6,5,33,'planned',NULL,0,NULL,NULL,NULL,0,0,0,NULL,NULL,'2026-04-25 14:48:12','2026-04-25 15:20:23'),(22,5,23,'paused',10,0,NULL,NULL,NULL,0,0,0,NULL,NULL,'2026-04-25 15:24:16','2026-04-25 15:30:41'),(27,5,35,'completed',7,0,NULL,NULL,NULL,0,0,0,NULL,NULL,'2026-04-25 15:26:00','2026-04-25 15:26:13'),(33,5,29,'watching',6,0,NULL,NULL,NULL,0,0,0,NULL,NULL,'2026-04-25 15:31:06','2026-04-25 15:31:17'),(37,3,36,'completed',10,1,NULL,NULL,NULL,0,0,0,NULL,NULL,'2026-04-25 16:26:12','2026-04-25 16:26:57'),(42,3,37,'completed',9,1,NULL,NULL,NULL,0,0,0,NULL,NULL,'2026-04-25 16:31:20','2026-04-25 16:53:02'),(46,3,23,'watching',5,0,NULL,NULL,NULL,0,0,0,NULL,NULL,'2026-04-25 16:56:39','2026-04-25 16:57:00'),(47,3,34,NULL,NULL,0,NULL,NULL,NULL,0,0,0,NULL,NULL,'2026-04-26 13:29:25','2026-04-26 13:29:28'),(48,4,38,'completed',5,1,NULL,NULL,NULL,0,0,0,NULL,NULL,'2026-04-26 14:08:39','2026-04-26 14:08:54'),(54,4,33,'completed',10,1,NULL,NULL,NULL,0,0,0,NULL,NULL,'2026-04-26 14:55:38','2026-04-26 14:55:47');
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
-- Table structure for table `personajes_usuario`
--

DROP TABLE IF EXISTS `personajes_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personajes_usuario` (
  `id_usuario` int(11) NOT NULL,
  `id_media` int(11) NOT NULL,
  `personaje_id` int(11) NOT NULL,
  `personaje_nombre` varchar(255) DEFAULT NULL,
  `personaje_imagen` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_usuario`,`personaje_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personajes_usuario`
--

LOCK TABLES `personajes_usuario` WRITE;
/*!40000 ALTER TABLE `personajes_usuario` DISABLE KEYS */;
INSERT INTO `personajes_usuario` VALUES (3,34,111335,'Greyrat, Eris Boreas','https://cdn.myanimelist.net/images/characters/14/324594.jpg?s=3648ce18134882a5f934130607f69548'),(3,34,111341,'Migurdia, Roxy','https://cdn.myanimelist.net/images/characters/16/552605.jpg?s=77b5b2dfd526c9ec5563c372a4dac111'),(5,33,184947,'Frieren','https://cdn.myanimelist.net/images/characters/7/525105.jpg?s=1706604ec2ca141a172526b8dedf3177'),(5,33,188176,'Fern','https://cdn.myanimelist.net/images/characters/12/619183.jpg?s=15f45c66440c0e9843e2f0109f0c1aef'),(5,33,188177,'Stark','https://cdn.myanimelist.net/images/characters/7/621924.jpg?s=ff623ff40dde15a769f879d87d6e7dcd');
/*!40000 ALTER TABLE `personajes_usuario` ENABLE KEYS */;
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
  `banner` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Cosmin','cosmin@gmail.com','$2y$10$HEcKCfoAnDDw24sWlJ1M7eecjQY6HSJbaE8AoWcxx40FO2vk8dGFO',NULL,NULL,'2026-03-23 09:45:14',NULL),(3,'Groom','groom@gmail.co,','$2y$10$XokoTpFhEJ0rRKhnF8IYQ.wWK/c5gVAaGz4aI4VvgVnQOgIpAjgRC',NULL,'/Recursos/fotos_perfil/user_3_1777137758.jpg','2026-04-15 09:29:52','/Recursos/Banners/banner_3_1777137758.png'),(4,'Kiki','kiki@gmail.com','$2y$10$T.WXjXJ56MbcWy3X7zyqpO0OZW7jOFRGOf8PtH7nIyGxAGmxxhOuW',NULL,'/Recursos/fotos_perfil/user_4_1777211414.jpg','2026-04-15 13:07:44','/Recursos/Banners/banner_4_1777211414.png'),(5,'Diddy','diddy@gmail.com','$2y$10$9hpfBFOVdTgKCZKNiglRQ.c8Jt8Reak5DydAsnHlmCJ4BLIkb0FVu',NULL,'/Recursos/fotos_perfil/user_5_1777136600.png','2026-04-25 14:13:57','/Recursos/Banners/banner_5_1777136823.png'),(6,'Klik','klik@gmail.com','$2y$10$gBkOH9eUvkJfPFkR2W3t7.t1m1LCHjEJrI/A1Tnukz2FTohGaMLru',NULL,'/Recursos/fotos_perfil/user_6_1777137008.png','2026-04-25 17:09:50','/Recursos/Banners/banner_6_1777137396.png'),(7,'Hola','hola@gmail.com','$2y$10$ITToSyh6NUiYGbBI0apPXu4V5D.Ks0h86yjS84Rzn8y6w6a9.Ysie',NULL,'/Recursos/fotos_perfil/user_7_1777137614.png','2026-04-25 17:19:55','/Recursos/Banners/banner_7_1777138091.png');
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

-- Dump completed on 2026-04-26 17:04:56
