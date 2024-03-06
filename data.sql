-- MySQL dump 10.13  Distrib 8.0.36, for Linux (x86_64)
--
-- Host: localhost    Database: text2bib
-- ------------------------------------------------------
-- Server version	8.0.36-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `von_names`
--

DROP TABLE IF EXISTS `von_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `von_names` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `von_names`
--

LOCK TABLES `von_names` WRITE;
/*!40000 ALTER TABLE `von_names` DISABLE KEYS */;
INSERT INTO `von_names` VALUES (1,'de','2023-11-26 07:56:57','2023-11-26 07:56:57'),(2,'De','2023-11-26 07:56:57','2023-11-26 07:56:57'),(3,'der','2023-11-26 07:56:57','2023-11-26 07:56:57'),(4,'da','2023-11-26 07:56:57','2023-11-26 07:56:57'),(5,'das','2023-11-26 07:56:57','2023-11-26 07:56:57'),(6,'della','2023-11-26 07:56:57','2023-11-26 07:56:57'),(7,'la','2023-11-26 07:56:57','2023-11-26 07:56:57'),(8,'van','2023-11-26 07:56:57','2023-11-26 07:56:57'),(9,'Van','2023-11-26 07:56:57','2023-11-26 07:56:57'),(10,'von','2023-11-26 07:56:57','2023-11-26 07:56:57'),(11,'tom','2023-12-29 04:07:32','2023-12-29 04:07:32'),(12,'St.','2024-01-10 08:32:34','2024-01-10 08:32:34'),(13,'La','2024-02-24 23:42:14','2024-02-24 23:42:14'),(14,'dan','2024-03-02 21:49:40','2024-03-02 21:49:40'),(15,'do','2024-03-02 22:19:33','2024-03-02 22:19:33');
/*!40000 ALTER TABLE `von_names` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `publishers`
--

DROP TABLE IF EXISTS `publishers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `publishers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `distinctive` tinyint NOT NULL DEFAULT '1',
  `checked` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publishers`
--

LOCK TABLES `publishers` WRITE;
/*!40000 ALTER TABLE `publishers` DISABLE KEYS */;
INSERT INTO `publishers` VALUES (1,'Academic Press',1,1,'2023-11-26 07:56:57','2024-02-18 13:27:01'),(2,'Cambridge University Press',1,1,'2023-11-26 07:56:57','2024-02-18 13:27:09'),(3,'Chapman & Hall',1,1,'2023-11-26 07:56:57','2024-02-18 13:27:11'),(4,'Edward Elgar',1,1,'2023-11-26 07:56:57','2024-02-18 13:27:14'),(5,'Elsevier',1,1,'2023-11-26 07:56:57','2024-02-18 13:27:15'),(6,'Harvard University Press',1,1,'2023-11-26 07:56:57','2024-02-18 13:27:17'),(7,'JAI Press',1,1,'2023-11-26 07:56:57','2024-02-18 13:27:19'),(8,'McGraw-Hill',1,1,'2023-11-26 07:56:57','2024-02-18 13:27:20'),(9,'MIT Press',1,1,'2023-11-26 07:56:57','2024-02-18 13:27:21'),(10,'Norton',1,1,'2023-11-26 07:56:57','2024-02-18 13:27:24'),(11,'Oxford University Press',1,1,'2023-11-26 07:56:57','2024-02-18 13:27:25'),(12,'Prentice Hall',1,1,'2023-11-26 07:56:57','2024-02-18 13:27:27'),(13,'Princeton University Press',1,1,'2023-11-26 07:56:57','2024-02-18 13:27:32'),(14,'Princeton Univ. Press',1,1,'2023-11-26 07:56:57','2024-02-18 13:27:30'),(15,'Routledge',1,1,'2023-11-26 07:56:57','2024-02-18 13:27:33'),(16,'Springer-Verlag',1,1,'2023-11-26 07:56:57','2024-02-18 13:27:36'),(17,'Springer',1,1,'2023-11-26 07:56:57','2024-02-18 13:27:34'),(18,'University of Pennsylvania Press',1,1,'2023-11-26 07:56:57','2024-02-18 13:27:39'),(19,'University of Pittsburgh Press',1,1,'2023-11-26 07:56:57','2024-02-18 13:27:41'),(20,'Van Nostrand Reinhold',1,1,'2023-11-26 07:56:57','2024-02-18 13:27:42'),(21,'Wiley',1,1,'2023-11-26 07:56:57','2024-02-18 13:27:43'),(22,'Yale University Press',1,1,'2023-11-26 07:56:57','2024-02-18 13:27:45'),(23,'University of Minnesota Press',1,1,'2023-12-12 21:52:22','2024-02-18 13:27:38'),(24,'North-Holland',1,1,'2023-12-24 21:27:35','2024-02-18 13:27:22'),(25,'Cambridge Univ. Press',1,1,'2024-01-01 02:07:35','2024-02-18 13:27:07'),(26,'Addison-Wesley',1,1,'2024-01-11 07:49:15','2024-02-18 13:27:05'),(27,'Dover Publications',1,1,'2024-01-13 05:39:36','2024-02-18 13:27:12'),(28,'Prentice-Hall',1,1,'2024-02-07 21:21:39','2024-02-18 13:27:28'),(29,'University of Hawaii Press',1,1,'2024-02-18 14:49:39','2024-02-18 14:50:47'),(30,'Kegan Paul',1,1,'2024-02-18 14:51:54','2024-02-18 14:52:04'),(31,'Weidenfeld & Nicolson',1,1,'2024-02-18 14:57:15','2024-02-18 15:24:11'),(32,'Brill',1,1,'2024-02-18 14:57:22','2024-02-18 15:23:54'),(33,'W. van Hoeve Publishers',1,1,'2024-02-18 14:58:09','2024-02-18 15:24:10'),(34,'KITLV Press',1,1,'2024-02-18 14:58:57','2024-02-18 15:23:59'),(35,'University of Chicago Press',1,1,'2024-02-18 15:02:01','2024-02-18 15:24:07'),(36,'Univ. of British Columbia Press',1,1,'2024-02-18 15:05:29','2024-02-18 15:24:05'),(37,'University of Massachusetts Press',1,1,'2024-02-18 15:05:35','2024-02-18 15:24:09'),(38,'Harper & Row',1,1,'2024-02-18 15:05:41','2024-02-18 15:23:57'),(39,'Thames and Hudson',1,1,'2024-02-18 15:05:45','2024-02-18 15:24:01'),(40,'SUNY Press',1,1,'2024-02-18 15:07:31','2024-02-18 15:24:00'),(41,'Columbia University Press',1,1,'2024-02-18 15:07:45','2024-02-18 15:23:56'),(42,'U. Chicago Press',1,1,'2024-02-18 15:14:29','2024-02-18 15:24:03'),(43,'Dover',1,1,'2024-02-18 15:28:38','2024-02-18 16:16:22'),(44,'Hermann',1,1,'2024-02-18 15:29:02','2024-02-18 16:16:26'),(45,'CRC Press',1,1,'2024-02-18 15:30:21','2024-02-18 16:16:18'),(46,'Arnold',0,1,'2024-02-18 15:44:58','2024-02-18 16:16:17'),(47,'Freeman and Company',1,1,'2024-02-18 15:45:50','2024-02-18 16:16:24'),(48,'AMS',1,1,'2024-02-18 15:46:51','2024-02-18 16:16:15'),(49,'Kluwer',1,1,'2024-02-18 16:10:39','2024-02-18 16:16:27'),(50,'Kluwer Academic/Plenum Publishers',1,1,'2024-02-21 08:46:18','2024-02-21 08:46:18'),(51,'Pergamon Press',1,1,'2024-03-02 22:17:18','2024-03-02 22:17:33'),(52,'Oxford Univ. Press',1,1,'2024-03-02 22:20:23','2024-03-02 22:20:23'),(53,'Birkh\\\"auser',1,1,'2024-03-02 22:24:40','2024-03-02 22:24:48'),(54,'Wileyy',1,0,'2024-03-03 00:04:13','2024-03-03 00:04:13');
/*!40000 ALTER TABLE `publishers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `distinctive` tinyint NOT NULL DEFAULT '1',
  `checked` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cities`
--

LOCK TABLES `cities` WRITE;
/*!40000 ALTER TABLE `cities` DISABLE KEYS */;
INSERT INTO `cities` VALUES (1,'Berlin',1,1,'2023-11-26 07:56:57','2024-02-18 14:50:03'),(2,'Boston',1,1,'2023-11-26 07:56:57','2024-02-18 14:50:05'),(3,'Cambridge',1,1,'2023-11-26 07:56:57','2024-02-18 14:50:06'),(4,'Chicago',1,1,'2023-11-26 07:56:57','2024-02-18 14:50:07'),(5,'Greenwich',1,1,'2023-11-26 07:56:57','2024-02-18 14:50:10'),(6,'Heidelberg',1,1,'2023-11-26 07:56:57','2024-02-18 14:50:11'),(7,'London',1,1,'2023-11-26 07:56:57','2024-02-18 14:50:14'),(8,'New York',1,1,'2023-11-26 07:56:57','2024-02-18 14:50:17'),(9,'Northampton',1,1,'2023-11-26 07:56:57','2024-02-18 14:50:18'),(10,'Oxford',1,1,'2023-11-26 07:56:57','2024-02-18 14:50:21'),(11,'Philadelphia',1,1,'2023-11-26 07:56:57','2024-02-18 14:50:23'),(12,'Princeton',1,1,'2023-11-26 07:56:57','2024-02-18 14:50:26'),(13,'San Diego',1,1,'2023-11-26 07:56:57','2024-02-18 14:50:27'),(14,'Upper Saddle River',1,1,'2023-11-26 07:56:57','2024-02-18 14:50:32'),(15,'Washington',1,1,'2023-11-26 07:56:57','2024-02-18 14:50:33'),(16,'Minneapolis',1,1,'2023-12-12 21:52:40','2024-02-18 14:50:15'),(17,'Paris',1,1,'2023-12-15 07:54:52','2024-02-18 14:50:22'),(20,'Monterey',1,1,'2024-01-31 02:12:48','2024-02-18 14:50:16'),(21,'Orlando',1,1,'2024-01-31 04:13:55','2024-02-18 14:50:20'),(22,'Pittsburgh',1,1,'2024-01-31 21:04:59','2024-02-18 14:50:24'),(23,'Tampa',1,1,'2024-01-31 21:06:25','2024-02-18 14:50:30'),(24,'Portland',1,1,'2024-01-31 21:10:51','2024-02-18 14:50:25'),(25,'Stockholm',1,1,'2024-02-04 15:48:29','2024-02-18 14:50:28'),(26,'Sydney',1,1,'2024-02-08 00:12:09','2024-02-18 14:50:29'),(27,'Englewood Cliffs',1,1,'2024-02-08 17:25:07','2024-02-18 14:50:09'),(28,'Honolulu',1,1,'2024-02-18 14:49:39','2024-02-18 14:50:12'),(29,'Leiden',1,1,'2024-02-18 14:57:22','2024-02-18 15:24:22'),(30,'The Hague',1,1,'2024-02-18 14:58:09','2024-02-18 15:24:23'),(31,'Delhi',1,1,'2024-02-18 14:59:28','2024-02-18 15:24:21'),(32,'Vancouver',1,1,'2024-02-18 15:05:29','2024-02-18 15:24:24'),(33,'Amherst',1,1,'2024-02-18 15:05:35','2024-02-18 15:24:20'),(34,'Albany',1,1,'2024-02-18 15:07:31','2024-02-18 15:24:18'),(35,'Cambridge, UK',1,1,'2024-02-18 15:29:17','2024-02-18 15:37:50'),(36,'Boca Raton, FL',1,1,'2024-02-18 15:30:21','2024-02-18 15:37:49'),(37,'Palo Alto',1,1,'2024-02-18 15:38:23','2024-02-18 15:38:25'),(38,'Providence, RI',1,1,'2024-02-18 15:46:51','2024-02-18 16:14:12'),(39,'Dordrecht',1,1,'2024-02-18 16:10:39','2024-02-18 16:14:10'),(40,'Ithaca',1,1,'2024-02-22 01:32:40','2024-02-22 01:32:40'),(41,'Madras',1,1,'2024-02-23 08:19:06','2024-02-23 08:19:06'),(42,'New Haven',1,1,'2024-03-01 07:58:58','2024-03-01 07:58:58');
/*!40000 ALTER TABLE `cities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `excluded_words`
--

DROP TABLE IF EXISTS `excluded_words`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `excluded_words` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `word` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `excluded_words`
--

LOCK TABLES `excluded_words` WRITE;
/*!40000 ALTER TABLE `excluded_words` DISABLE KEYS */;
INSERT INTO `excluded_words` VALUES (1,'Trans','2023-11-26 07:56:57','2023-11-26 07:56:57'),(2,'Ind','2023-11-26 07:56:57','2023-11-26 07:56:57'),(3,'Int','2023-11-26 07:56:57','2023-11-26 07:56:57'),(4,'Soc','2023-11-26 07:56:57','2023-11-26 07:56:57'),(5,'Proc','2023-11-26 07:56:57','2023-11-26 07:56:57'),(6,'Phys','2023-11-26 07:56:57','2023-11-26 07:56:57'),(7,'Rev','2023-11-26 07:56:57','2023-11-26 07:56:57'),(8,'Amer','2023-11-26 07:56:57','2023-11-26 07:56:57'),(9,'Math','2023-11-26 07:56:57','2023-11-26 07:56:57'),(10,'Meth','2023-11-26 07:56:57','2023-11-26 07:56:57'),(11,'Geom','2023-11-26 07:56:57','2023-11-26 07:56:57'),(12,'Univ','2023-11-26 07:56:57','2023-11-26 07:56:57'),(13,'Nat','2023-11-26 07:56:57','2023-11-26 07:56:57'),(14,'Sci','2023-11-26 07:56:57','2023-11-26 07:56:57'),(15,'Austral','2023-11-26 07:56:57','2023-11-26 07:56:57');
/*!40000 ALTER TABLE `excluded_words` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `names`
--

DROP TABLE IF EXISTS `names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `names` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `names`
--

LOCK TABLES `names` WRITE;
/*!40000 ALTER TABLE `names` DISABLE KEYS */;
INSERT INTO `names` VALUES (1,'American','2023-11-26 07:56:57','2023-11-26 07:56:57'),(2,'Arrovian','2023-11-26 07:56:57','2023-11-26 07:56:57'),(3,'Aumann','2023-11-26 07:56:57','2023-11-26 07:56:57'),(4,'Bayes','2023-11-26 07:56:57','2023-11-26 07:56:57'),(5,'Bayesian','2023-11-26 07:56:57','2023-11-26 07:56:57'),(6,'Cournot','2023-11-26 07:56:57','2023-11-26 07:56:57'),(7,'Gauss','2023-11-26 07:56:57','2023-11-26 07:56:57'),(8,'Gaussian','2023-11-26 07:56:57','2023-11-26 07:56:57'),(9,'German','2023-11-26 07:56:57','2023-11-26 07:56:57'),(10,'Groves','2023-11-26 07:56:57','2023-11-26 07:56:57'),(11,'Indian','2023-11-26 07:56:57','2023-11-26 07:56:57'),(12,'Ledyard','2023-11-26 07:56:57','2023-11-26 07:56:57'),(13,'Lindahl','2023-11-26 07:56:57','2023-11-26 07:56:57'),(14,'Markov','2023-11-26 07:56:57','2023-11-26 07:56:57'),(15,'Markovian','2023-11-26 07:56:57','2023-11-26 07:56:57'),(16,'Nash','2023-11-26 07:56:57','2023-11-26 07:56:57'),(17,'Savage','2023-11-26 07:56:57','2023-11-26 07:56:57'),(18,'U.S.','2023-11-26 07:56:57','2023-11-26 07:56:57'),(19,'Walras','2023-11-26 07:56:57','2023-11-26 07:56:57'),(20,'Walrasian','2023-11-26 07:56:57','2023-11-26 07:56:57'),(21,'Schur','2023-12-12 22:36:12','2023-12-12 22:36:12'),(22,'Elizabeth','2023-12-12 23:46:47','2023-12-12 23:46:47'),(23,'Elisabeth','2023-12-12 23:46:54','2023-12-12 23:46:54'),(24,'Kleinian','2023-12-27 16:33:05','2023-12-27 16:33:05'),(25,'Riemann','2023-12-27 16:33:56','2023-12-27 16:33:56'),(26,'Morse','2023-12-28 21:43:58','2023-12-28 21:43:58'),(27,'Smale','2023-12-28 21:44:03','2023-12-28 21:44:03'),(28,'Euler','2023-12-29 04:01:42','2023-12-29 04:01:42'),(29,'Riccati','2024-02-07 23:34:58','2024-02-07 23:34:58'),(30,'Banach','2024-02-09 08:45:49','2024-02-09 08:45:49'),(31,'Sylvester','2024-02-12 00:22:50','2024-02-12 00:22:50');
/*!40000 ALTER TABLE `names` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `journals`
--

DROP TABLE IF EXISTS `journals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `journals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `distinctive` tinyint NOT NULL DEFAULT '1',
  `checked` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=307 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journals`
--

LOCK TABLES `journals` WRITE;
/*!40000 ALTER TABLE `journals` DISABLE KEYS */;
INSERT INTO `journals` VALUES (2,'Econometrica',1,1,'2024-02-17 22:20:11','2024-02-17 22:20:11'),(3,'Review of Economic Studies',1,1,'2024-02-17 22:20:11','2024-02-17 22:20:11'),(4,'Journal of Cultural Economics',1,1,'2024-02-17 22:20:11','2024-02-17 22:20:11'),(5,'Journal of Experimental Social Psychology',1,1,'2024-02-17 22:20:11','2024-02-17 22:20:11'),(6,'The Quarterly Journal of Economics',1,1,'2024-02-17 22:20:11','2024-02-17 22:20:11'),(7,'Journal of Political Economy',1,1,'2024-02-17 22:20:11','2024-02-17 22:20:11'),(8,'Quarterly Journal of Economics',1,1,'2024-02-17 22:20:11','2024-02-17 22:20:11'),(9,'Tohoku Math. J.',1,1,'2024-02-17 22:20:11','2024-02-18 07:06:41'),(10,'Journal of Economic Theory',1,1,'2024-02-17 22:20:11','2024-02-17 22:20:11'),(11,'American Political Science Review',1,1,'2024-02-17 22:20:11','2024-02-17 22:20:11'),(12,'Economic Theory',0,1,'2024-02-17 22:20:11','2024-02-18 06:48:06'),(13,'Operations Research',0,1,'2024-02-17 22:20:11','2024-02-18 06:30:00'),(14,'Financial Counseling and Planning',1,1,'2024-02-17 22:20:11','2024-02-17 22:20:11'),(15,'Mathematics of Operations Research',1,1,'2024-02-17 22:20:11','2024-02-17 22:20:11'),(16,'Psychological Review',1,1,'2024-02-17 22:20:11','2024-02-17 22:20:11'),(17,'Journal of Finance',1,1,'2024-02-17 22:20:11','2024-02-17 22:20:11'),(18,'Games and Economic Behavior',1,1,'2024-02-17 22:20:46','2024-02-17 22:20:46'),(19,'Theoretical Economics',0,1,'2024-02-17 22:20:46','2024-02-18 06:49:46'),(20,'Management Science',0,1,'2024-02-17 22:20:46','2024-02-18 06:38:18'),(21,'The International Journal of Game Theory',1,1,'2024-02-17 22:20:46','2024-02-17 22:20:46'),(22,'International Journal of Game Theory',1,1,'2024-02-17 22:20:46','2024-02-17 22:20:46'),(23,'American Economic Review',1,1,'2024-02-17 22:20:46','2024-02-17 22:20:46'),(24,'Journal of Business',1,1,'2024-02-17 22:20:59','2024-02-17 22:20:59'),(25,'Journal of the European Economic Association',1,1,'2024-02-17 22:20:59','2024-02-17 22:20:59'),(26,'Journal of European Economic Association',1,1,'2024-02-17 22:20:59','2024-02-17 22:20:59'),(27,'The Review of Economic Studies',1,1,'2024-02-17 22:20:59','2024-02-17 22:20:59'),(28,'The American Economic Review',1,1,'2024-02-17 22:20:59','2024-02-17 22:20:59'),(29,'RAND Journal of Economics',1,1,'2024-02-17 22:21:09','2024-02-17 22:21:09'),(31,'Economics Letters',1,1,'2024-02-17 22:21:09','2024-02-17 22:21:09'),(32,'European Economic Review',1,1,'2024-02-17 22:21:09','2024-02-17 22:21:09'),(33,'American Economic Review Papers and Proceedings',1,1,'2024-02-17 22:21:09','2024-02-17 22:21:09'),(34,'Journal of Economic Perspectives',1,1,'2024-02-17 22:21:09','2024-02-17 22:21:09'),(35,'Journal of Economic Behavior and Organization',1,1,'2024-02-17 22:21:09','2024-02-17 22:21:09'),(36,'Theory and Decision',1,1,'2024-02-17 22:21:09','2024-02-17 22:21:09'),(37,'Journal of Educational Measurement',1,1,'2024-02-17 22:21:17','2024-02-17 22:21:17'),(38,'Psychological Science',1,1,'2024-02-17 22:21:17','2024-02-17 22:21:17'),(39,'NAJ Economics',1,1,'2024-02-17 22:21:18','2024-02-17 22:21:18'),(40,'Journal of Marketing Research',1,1,'2024-02-17 22:21:18','2024-02-17 22:21:18'),(41,'Perceptual and Motor Skills',1,1,'2024-02-17 22:21:18','2024-02-17 22:21:18'),(43,'Journal of Financial Economics',1,1,'2024-02-17 22:21:25','2024-02-17 22:21:25'),(44,'Journal of Institutional and Theoretical Economics',1,1,'2024-02-17 22:21:25','2024-02-17 22:21:25'),(45,'Journal of Law and Economics',1,1,'2024-02-17 22:21:25','2024-02-17 22:21:25'),(46,'Brookings Papers on Economic Activity',1,1,'2024-02-17 22:21:26','2024-02-17 22:21:26'),(47,'Journal of Mathematical Economics',1,1,'2024-02-17 22:21:34','2024-02-17 22:21:34'),(48,'Journal of Statistical Physics',1,1,'2024-02-17 22:21:34','2024-02-17 22:21:34'),(49,'Journal of Public Economics',1,1,'2024-02-17 22:21:42','2024-02-17 22:21:42'),(50,'JASA',1,1,'2024-02-17 22:21:50','2024-02-17 22:21:50'),(51,'International Economic Review',1,1,'2024-02-17 22:21:50','2024-02-17 22:21:50'),(52,'Proceedings of the National Academy of Sciences of the USA',1,1,'2024-02-17 22:21:50','2024-02-17 22:21:50'),(53,'Journal of Economic Dynamics and Control',1,1,'2024-02-17 22:21:50','2024-02-17 22:21:50'),(54,'Review of Financial Studies',1,1,'2024-02-17 22:21:59','2024-02-17 22:21:59'),(55,'Economic Journal',0,1,'2024-02-17 22:21:59','2024-02-28 01:22:36'),(56,'The Journal of Finance',1,1,'2024-02-17 22:21:59','2024-02-17 22:21:59'),(57,'Review of Economic Dynamics',1,1,'2024-02-17 22:21:59','2024-02-17 22:21:59'),(58,'Public Choice',0,1,'2024-02-17 22:22:18','2024-02-18 06:49:27'),(59,'American Journal of Political Science',1,1,'2024-02-17 22:22:18','2024-02-17 22:22:18'),(60,'Proceedings of the National Academy of Science U. S. A.',1,1,'2024-02-17 22:22:27','2024-02-17 22:22:27'),(61,'Biometrika',1,1,'2024-02-17 22:22:27','2024-02-17 22:22:27'),(62,'Journal of the Royal Statistical Society Series B',1,1,'2024-02-17 22:22:27','2024-02-17 22:22:27'),(64,'J. Econ. Theory',1,1,'2024-02-17 22:22:34','2024-02-17 22:22:34'),(67,'Computational Economics',0,1,'2024-02-17 22:22:45','2024-02-18 06:48:14'),(70,'Bell Journal of Economics',1,1,'2024-02-17 22:22:55','2024-02-17 22:22:55'),(71,'Journal of Industrial Economics',1,1,'2024-02-17 22:22:55','2024-02-17 22:22:55'),(72,'Journal of Economics and Management Strategy',1,1,'2024-02-17 22:22:55','2024-02-17 22:22:55'),(73,'Advances in Theoretical Economics',1,1,'2024-02-17 22:23:02','2024-02-17 22:23:02'),(74,'Federal Reserve Bank of Minneapolis Quarterly Review',1,1,'2024-02-17 22:23:11','2024-02-17 22:23:11'),(75,'Oxford Economic Papers',1,1,'2024-02-17 22:23:11','2024-02-17 22:23:11'),(76,'Psychological Bulletin',1,1,'2024-02-17 22:23:19','2024-02-17 22:25:57'),(77,'American Psychologist',1,1,'2024-02-17 22:23:19','2024-02-17 22:23:19'),(78,'Annals of Mathematical Statistics',1,1,'2024-02-17 22:23:19','2024-02-17 22:23:19'),(79,'Evolution and Human Behavior',1,1,'2024-02-17 22:23:19','2024-02-17 22:23:19'),(80,'Child Development',0,1,'2024-02-17 22:23:19','2024-02-18 06:47:47'),(81,'Journal of Economic Literature',1,1,'2024-02-17 22:23:19','2024-02-17 22:23:19'),(82,'Economic Inquiry',1,1,'2024-02-17 22:23:19','2024-02-17 22:23:19'),(83,'Journal of Monetary Economics',1,1,'2024-02-17 22:32:25','2024-02-17 22:32:25'),(84,'IEEE Trans. Aero. Elec. Syst.',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(85,'J. Guid. Cont. Dyn.',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(87,'Int. J. Cont.',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(88,'Guid. Cont. Dyn.',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(89,'J. Opt. Cont., Appl. Meth.',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(90,'IEEE Trans. Auto. Cont.',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(91,'JOTA',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(93,'IEEE Trans. Automat. Control',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(94,'J. Optim. Theory Appl.',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(95,'Internat. J. Control',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(96,'Systems Control Lett.',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(97,'Automatica',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(99,'IEEE Trans. On Autom. Control',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(100,'IEEE AC',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(101,'Int. J. Control',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(102,'Int. J. of Control',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(103,'J. of Information and Control',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(105,'Autmoatica',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(106,'SIAM J. Sci. Statist. Comput.',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(107,'Automat. Remote Contr.',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(108,'S. I. C. E.',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(109,'IEEE Trans. Automat. Contr.',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(110,'J. Opt. Theory Appl.',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(111,'Syst. Contr. Lett.',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(112,'SIAM Contr. Opt.',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(113,'Int. J. Contr.',1,1,'2024-02-17 22:32:38','2024-02-17 22:32:38'),(114,'Celest. Mech. Dyn. Astron.',1,1,'2024-02-17 22:32:49','2024-02-17 22:32:49'),(115,'Psicologia: Reflex{\\=a}o e Critica',1,1,'2024-02-17 22:33:08','2024-02-17 22:33:08'),(116,'Southeast Review of Asian Studies',1,1,'2024-02-17 22:33:24','2024-02-17 22:33:24'),(117,'International Journal of Cultural Studies',1,1,'2024-02-17 22:33:24','2024-02-17 22:33:24'),(118,'Popular Music and Society',1,1,'2024-02-17 22:33:24','2024-02-17 22:33:24'),(119,'New Media & Society',1,1,'2024-02-17 22:33:24','2024-02-17 22:33:24'),(121,'Foreign Affairs',0,1,'2024-02-17 22:33:24','2024-02-18 06:48:24'),(122,'Mechademia',1,1,'2024-02-17 22:33:24','2024-02-17 22:33:24'),(124,'Public Culture',1,1,'2024-02-17 22:33:24','2024-02-17 22:33:24'),(126,'Communication, Culture & Critique',1,1,'2024-02-17 22:33:24','2024-02-17 22:33:24'),(127,'Journal of Tourism & Travel Marketing',1,1,'2024-02-17 22:33:24','2024-02-17 22:33:24'),(128,'Eur Phys J Spec Topics',1,1,'2024-02-17 22:33:33','2024-02-17 22:33:33'),(129,'Bulletin of the Seismological Society of America',1,1,'2024-02-17 22:33:45','2024-02-17 22:33:45'),(130,'International Journal of Astrobiology',1,1,'2024-02-17 22:33:45','2024-02-17 22:37:46'),(131,'Proceedings of the National Academy of Sciences of the United States of America',1,1,'2024-02-17 22:33:45','2024-02-17 22:33:45'),(132,'Geophys. Geosyst.',1,1,'2024-02-17 22:33:45','2024-02-17 22:37:15'),(133,'Geochem. Geophys. Geosyst.',1,1,'2024-02-17 22:33:45','2024-02-17 22:33:45'),(134,'Naval Research Logistics Quarterly',1,1,'2024-02-17 22:34:05','2024-02-17 22:34:05'),(135,'J. Geophys. Res.',1,1,'2024-02-17 22:34:05','2024-02-17 22:34:05'),(136,'Bull. Seismol. Soc. Am.',1,1,'2024-02-17 22:34:05','2024-02-17 22:34:05'),(137,'Tectonophysics',1,1,'2024-02-17 22:34:05','2024-02-17 22:34:05'),(138,'Seismol. Res. Lett.',1,1,'2024-02-17 22:34:05','2024-02-17 22:34:05'),(139,'Science',0,1,'2024-02-17 22:34:05','2024-02-18 00:35:43'),(140,'Nature Geosciences',1,1,'2024-02-17 22:34:05','2024-02-17 22:34:05'),(141,'Biodiversity Data Journal',1,1,'2024-02-17 22:34:44','2024-02-17 22:34:44'),(142,'Cognition',0,1,'2024-02-17 22:34:54','2024-02-18 06:47:43'),(143,'Tutorials in Quantitative Methods for Psychology',1,1,'2024-02-17 22:34:54','2024-02-17 22:34:54'),(144,'Journal of Memory and Language',1,1,'2024-02-17 22:34:54','2024-02-17 22:34:54'),(145,'Journal of Experimental Psychology: Human Perception and Performance',1,1,'2024-02-17 22:34:54','2024-02-17 22:38:53'),(146,'Ecology',0,1,'2024-02-17 22:35:05','2024-02-25 00:42:50'),(155,'Amer. Econ. Rev.',1,1,'2024-02-18 00:18:30','2024-02-18 00:40:17'),(156,'Nature',0,1,'2024-02-18 00:40:56','2024-02-18 00:41:04'),(157,'Geophys. Res. Lett.',1,1,'2024-02-18 01:26:17','2024-02-18 06:26:38'),(159,'Annu. Rev. Earth Planet. Sci.',1,1,'2024-02-18 01:26:17','2024-02-18 06:26:10'),(161,'Seismological Research Letters',1,1,'2024-02-18 01:26:17','2024-02-18 06:27:53'),(168,'Sesimol. Res. Lett.',1,1,'2024-02-18 01:26:17','2024-02-18 06:27:55'),(169,'Geophys.',0,1,'2024-02-18 01:26:17','2024-03-03 11:29:26'),(175,'Geology',0,1,'2024-02-18 01:26:17','2024-02-18 06:48:31'),(176,'Annali di Geofisica',1,1,'2024-02-18 01:26:17','2024-02-18 06:25:55'),(181,'Geophys. Res.',1,1,'2024-02-18 01:26:17','2024-02-18 06:26:33'),(182,'Geophys. Res. Lett.',1,1,'2024-02-18 01:26:17','2024-02-18 07:39:22'),(183,'Language',0,1,'2024-02-18 09:00:19','2024-02-18 09:00:34'),(184,'Studies in Second Language Acquisition',1,1,'2024-02-18 09:11:21','2024-02-18 09:20:18'),(185,'Journal of the Acoustical Society of America',1,1,'2024-02-18 09:11:33','2024-02-18 09:20:07'),(186,'Memory and Cognition',0,1,'2024-02-18 09:11:57','2024-02-18 09:20:13'),(187,'Cognitive Science',1,1,'2024-02-18 09:12:11','2024-02-18 09:19:55'),(188,'Word',0,1,'2024-02-18 09:12:36','2024-02-18 09:20:33'),(189,'Journal of Phonetics',1,1,'2024-02-18 09:12:41','2024-02-18 09:19:56'),(190,'Theoretical Linguistics',1,1,'2024-02-18 09:15:07','2024-02-18 09:20:31'),(191,'Journal of Pidgin and Creole Languages',1,1,'2024-02-18 09:17:03','2024-02-18 09:20:02'),(192,'Journal of Southeast Asian Studies',1,1,'2024-02-18 14:58:23','2024-02-18 16:15:30'),(193,'Journal of the Siam Society',1,1,'2024-02-18 14:58:34','2024-02-18 16:15:32'),(194,'Social Scientist',1,1,'2024-02-18 14:59:10','2024-02-18 16:15:55'),(195,'Economic Weekly',1,1,'2024-02-18 15:05:56','2024-02-18 16:14:43'),(196,'Journal for the Scientific Study of Religion',1,1,'2024-02-18 15:07:22','2024-02-18 16:15:26'),(197,'Journal of Social Research',1,1,'2024-02-18 15:09:40','2024-02-18 16:15:29'),(198,'Ethos',0,1,'2024-02-18 15:09:46','2024-02-18 16:14:46'),(199,'Daedalus',1,1,'2024-02-18 15:13:15','2024-02-18 16:14:39'),(200,'Far Eastern Quarterly',1,1,'2024-02-18 15:14:18','2024-02-18 16:14:52'),(201,'American Anthropologist',1,1,'2024-02-18 15:17:32','2024-02-18 16:14:25'),(202,'The Journal of Asian Studies',1,1,'2024-02-18 15:19:54','2024-02-18 16:15:59'),(203,'Journal of Asian Studies',1,1,'2024-02-18 15:19:58','2024-02-18 16:15:28'),(204,'Comput. Optim. Appl.',1,1,'2024-02-18 15:27:26','2024-02-18 16:14:36'),(205,'Wireless Networks',0,1,'2024-02-18 15:27:42','2024-02-18 16:16:06'),(206,'Psychometrika',1,1,'2024-02-18 15:27:47','2024-02-18 16:15:51'),(207,'Trans. Amer. Math. Soc.',1,1,'2024-02-18 15:27:55','2024-02-18 16:16:01'),(208,'J. Math. Anal. Appl.',1,1,'2024-02-18 15:27:59','2024-02-18 16:15:20'),(209,'Mobile Comput.',1,1,'2024-02-18 15:28:06','2024-02-18 16:15:43'),(210,'J. Robotics Res.',1,1,'2024-02-18 15:28:42','2024-02-18 16:15:24'),(211,'Discrete Comput. Geom.',1,1,'2024-02-18 15:28:45','2024-02-18 16:14:41'),(212,'Neural Comput.',1,1,'2024-02-18 15:28:49','2024-02-18 16:15:45'),(213,'Optim. Methods Softw.',1,1,'2024-02-18 15:28:54','2024-02-18 16:15:49'),(214,'J. Aust. Math. Soc. Ser. B',1,1,'2024-02-18 15:28:58','2024-02-18 16:15:05'),(215,'Nucleic Acid Res.',1,1,'2024-02-18 15:29:11','2024-02-18 16:15:46'),(216,'Sensor Networks',1,1,'2024-02-18 15:29:33','2024-02-18 16:15:54'),(217,'Automation Sci. Engrg.',1,1,'2024-02-18 15:29:40','2024-02-18 16:14:29'),(218,'Sci. Comput.',1,1,'2024-02-18 15:29:45','2024-02-18 16:15:52'),(219,'Comput. Geom.',1,1,'2024-02-18 15:30:36','2024-02-18 16:14:34'),(220,'Inform. Process. Lett.',1,1,'2024-02-18 15:30:42','2024-02-18 16:15:03'),(221,'Cambridge Math. J.',1,1,'2024-02-18 15:30:53','2024-02-18 16:14:31'),(222,'Discrete Math.',1,1,'2024-02-18 15:33:20','2024-02-18 16:14:42'),(223,'J. Global Optim.',1,1,'2024-02-18 15:35:11','2024-02-18 16:15:18'),(224,'Trans. Roy. Soc. Edinburgh',1,1,'2024-02-18 15:36:57','2024-02-18 16:16:03'),(225,'ACM Trans. Sensor Networks',1,1,'2024-02-18 15:37:02','2024-02-25 15:35:35'),(226,'Inform. Inference',1,1,'2024-02-18 15:37:08','2024-02-18 16:15:01'),(227,'Linear Algebra Appl.',1,1,'2024-02-18 15:39:55','2024-02-18 16:15:35'),(228,'Found. Phys.',1,1,'2024-02-18 15:44:22','2024-02-18 16:14:53'),(229,'Algorithmica',1,1,'2024-02-18 15:44:38','2024-02-18 16:14:20'),(230,'J. Comput. Chem.',1,1,'2024-02-18 15:45:54','2024-02-18 16:15:14'),(231,'Amer. J. Math.',1,1,'2024-02-18 15:46:28','2024-02-18 16:14:22'),(232,'Oper. Res. Lett.',1,1,'2024-02-18 15:46:54','2024-02-18 16:15:48'),(233,'Magnetic Resonance Chem.',1,1,'2024-02-18 15:47:11','2024-02-18 16:15:36'),(234,'Bull. Math. Biol.',1,1,'2024-02-18 15:47:14','2024-02-18 16:14:30'),(235,'SIAM J. Comput.',1,1,'2024-02-18 15:48:07','2024-02-18 15:48:07'),(236,'SIAM J. Optim.',1,1,'2024-02-18 15:48:32','2024-02-18 15:48:32'),(237,'J. Combin. Theory Ser. B',1,1,'2024-02-18 15:49:22','2024-02-18 16:15:11'),(238,'Combinatorica',1,1,'2024-02-18 15:49:27','2024-02-18 16:14:33'),(239,'Math. Program.',1,1,'2024-02-18 15:53:13','2024-02-18 16:15:39'),(240,'SIAM J. Matrix Anal. Appl.',1,1,'2024-02-18 15:53:39','2024-02-18 15:53:39'),(241,'European J. Oper. Res.',1,1,'2024-02-18 15:57:00','2024-02-18 16:14:51'),(242,'J. Comput. Biosci.',1,1,'2024-02-18 15:57:24','2024-02-18 16:15:13'),(243,'SIAM J. Algebraic Discrete Methods',1,1,'2024-02-18 15:59:40','2024-02-18 15:59:40'),(244,'IEEE Trans. Robotics Automation',1,1,'2024-02-18 16:00:53','2024-02-18 16:00:53'),(245,'Math. Ann.',1,1,'2024-02-18 16:01:14','2024-02-18 16:15:38'),(246,'J. Bioinform. Comput. Biol.',1,1,'2024-02-18 16:01:56','2024-02-18 16:15:08'),(247,'J. Molecular Biol.',1,1,'2024-02-18 16:04:28','2024-02-18 16:15:23'),(248,'Math. Program. Ser. A',1,1,'2024-02-18 16:04:47','2024-02-18 16:15:41'),(249,'IEEE Trans. Robotics',1,1,'2024-02-18 16:04:59','2024-02-18 16:15:00'),(250,'Computers and Chemistry',1,1,'2024-02-18 16:05:11','2024-02-18 16:14:37'),(251,'Amer. Math. Monthly',1,1,'2024-02-18 16:05:33','2024-02-18 16:14:23'),(252,'J. Engrg. Industry',1,1,'2024-02-18 16:05:38','2024-02-18 16:15:17'),(253,'J. Bioinform. Res. Appl.',1,1,'2024-02-18 16:05:42','2024-02-18 16:15:09'),(254,'J. Math. Psych.',1,1,'2024-02-18 16:07:03','2024-02-18 16:15:21'),(255,'Appl. Comput. Harmon. Anal.',1,1,'2024-02-18 16:07:09','2024-02-18 16:14:27'),(256,'SIAM J. Imaging Sci.',1,1,'2024-02-18 16:07:50','2024-02-18 16:07:50'),(257,'Proc. Natl. Acad. Sci. USA',1,1,'2024-02-18 16:08:11','2024-02-18 16:08:11'),(258,'Struct. Topology',1,1,'2024-02-18 16:10:26','2024-02-18 16:15:57'),(259,'J. Algorithms',1,1,'2024-02-18 16:10:43','2024-02-18 16:15:04'),(260,'Graphical Models',0,1,'2024-02-18 16:10:48','2024-02-18 16:14:58'),(261,'J. Mech. Design',1,1,'2024-02-18 16:11:33','2024-02-18 16:15:22'),(262,'J. Chem. Inform. Comput. Sci.',1,1,'2024-02-18 16:12:13','2024-02-18 16:15:10'),(263,'J. Comput. Geom. Appl.',1,1,'2024-02-18 16:12:16','2024-02-18 16:15:15'),(264,'Molecular Ecology',1,1,'2024-02-25 00:41:25','2024-02-25 00:41:25'),(265,'Molecular Phylogenetics and Evolution',1,1,'2024-02-25 00:41:40','2024-02-25 00:43:15'),(266,'Quercus',1,1,'2024-02-25 00:42:39','2024-02-25 00:43:13'),(267,'Systematics and Biodiversity',1,1,'2024-02-25 00:44:56','2024-02-25 15:11:53'),(268,'Zoology',0,1,'2024-02-25 00:45:16','2024-02-25 15:11:56'),(269,'Zootaxa',1,1,'2024-02-25 00:45:26','2024-02-25 15:11:59'),(270,'Stanford Law Review',1,1,'2024-02-28 01:21:01','2024-02-28 01:22:07'),(271,'American Economic Journal: Microeconomics',1,1,'2024-02-28 01:22:11','2024-02-28 01:22:11'),(272,'International Journal of Industrial Organization',1,1,'2024-02-28 01:27:28','2024-02-28 01:29:15'),(273,'B. E. Journal of Theoretical Economics',1,1,'2024-02-28 01:28:24','2024-02-28 01:29:18'),(274,'Frontiers in Microbiology',1,1,'2024-03-01 07:43:52','2024-03-02 18:50:20'),(275,'Journal of Arid Environments',1,1,'2024-03-01 07:56:31','2024-03-02 18:50:23'),(276,'Biogeochemistry',1,1,'2024-03-02 18:56:35','2024-03-02 19:21:50'),(277,'Economica',1,1,'2024-03-02 19:13:01','2024-03-02 19:21:52'),(278,'Review of Economics and Statistics',1,1,'2024-03-02 19:13:11','2024-03-02 19:21:58'),(279,'Journal of Neurophysiology',1,1,'2024-03-02 19:16:30','2024-03-02 19:21:56'),(280,'J. Neurosci.',1,1,'2024-03-02 19:21:39','2024-03-02 19:21:54'),(281,'Ecological Indicators',1,1,'2024-03-02 21:48:06','2024-03-02 21:52:55'),(282,'Proc. Amer. Math. Soc.',1,1,'2024-03-02 21:53:16','2024-03-02 21:53:16'),(283,'J. London Math. Soc.',1,1,'2024-03-02 21:53:32','2024-03-02 21:55:30'),(284,'J. Differential Geom.',1,1,'2024-03-02 21:53:33','2024-03-02 21:55:29'),(285,'Indiana Univ. Math. J.',1,1,'2024-03-02 21:54:01','2024-03-02 21:55:31'),(286,'Proc. Nat. Acad. Sci. USA',1,1,'2024-03-02 21:55:25','2024-03-02 21:55:25'),(287,'Michigan Math. J.',1,1,'2024-03-02 21:58:13','2024-03-02 22:02:00'),(289,'Pacific J. Math.',1,1,'2024-03-02 21:59:46','2024-03-02 22:01:58'),(290,'Math. Proc. Cambridge Philos. Soc.',1,1,'2024-03-02 22:01:56','2024-03-02 22:01:56'),(291,'J. Math. Phys.',1,1,'2024-03-02 22:09:16','2024-03-02 22:16:09'),(292,'Canad. J. Math.',1,1,'2024-03-02 22:11:12','2024-03-02 22:16:07'),(293,'Duke Math. J.',1,1,'2024-03-02 22:15:54','2024-03-02 22:15:54'),(294,'Acta Math.',1,1,'2024-03-02 22:20:37','2024-03-02 22:20:49'),(295,'J. Algebraic Geom.',1,1,'2024-03-02 22:21:06','2024-03-02 22:21:47'),(296,'Topology Appl.',1,1,'2024-03-02 22:21:11','2024-03-02 22:21:46'),(297,'Bull. London Math. Soc.',1,1,'2024-03-02 22:21:20','2024-03-02 22:21:43'),(298,'Invent. Math.',1,1,'2024-03-02 22:21:25','2024-03-02 22:21:44'),(299,'Notices Amer. Math. Soc.',1,1,'2024-03-02 22:23:37','2024-03-02 22:23:37'),(300,'Internat. J. Math.',1,1,'2024-03-02 22:24:13','2024-03-02 22:44:07'),(301,'Math. Scand.',1,1,'2024-03-02 22:29:42','2024-03-02 22:44:09'),(302,'Math. Zeit.',1,1,'2024-03-02 22:32:23','2024-03-02 22:44:10'),(303,'Proc. Nat. Acad. Sci. U. S. A.',1,1,'2024-03-02 22:44:04','2024-03-02 22:44:04'),(304,'J. Comp. Phys.',1,1,'2024-03-02 23:10:49','2024-03-03 11:17:12'),(305,'Microwave and Optical Tech. Lett.',1,1,'2024-03-02 23:56:52','2024-03-03 11:17:15'),(306,'SIAM J. Sci. Comput.',1,1,'2024-03-03 16:51:08','2024-03-03 16:51:08');
/*!40000 ALTER TABLE `journals` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-03-06 12:26:56
