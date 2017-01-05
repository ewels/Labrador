CREATE DATABASE `labrador-dropseq`;
CREATE USER 'labrador-dropseq'@'localhost';
GRANT ALL PRIVILEGES ON `labrador-dropseq`.* TO 'labrador-dropseq'@'localhost';
FLUSH PRIVILEGES;
USE `labrador-dropseq`;

-- MySQL dump 10.13  Distrib 5.5.29, for Linux (x86_64)
--
-- Host: localhost    Database: labrador
-- ------------------------------------------------------
-- Server version	5.5.29

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `datasets`
--

DROP TABLE IF EXISTS `datasets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `datasets` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `project_id` int(12) DEFAULT NULL,
  `name` text NOT NULL,
  `species` varchar(250) NOT NULL,
  `cell_type` varchar(250) NOT NULL,
  `data_type` varchar(250) NOT NULL,
  `accession_geo` varchar(250) DEFAULT NULL,
  `accession_sra` varchar(250) DEFAULT NULL,
  `notes` text NOT NULL,
  `modified` int(12) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1805 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `history`
--

DROP TABLE IF EXISTS `history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `history` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `project_id` int(20) NOT NULL,
  `user_id` int(12) DEFAULT NULL,
  `note` text,
  `time` int(12) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=467 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `papers`
--

DROP TABLE IF EXISTS `papers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `papers` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `project_id` int(20) NOT NULL,
  `year` int(4) DEFAULT NULL,
  `journal` varchar(200) DEFAULT NULL,
  `title` text,
  `authors` text,
  `pmid` varchar(20) DEFAULT NULL,
  `doi` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=168 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `processing`
--

DROP TABLE IF EXISTS `processing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `processing` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `project_id` int(20) NOT NULL,
  `dataset_id` int(20) DEFAULT NULL,
  `filename` text,
  `commands` text,
  `created` int(12) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `dataset_id` (`dataset_id`)
) ENGINE=InnoDB AUTO_INCREMENT=862 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_contacts`
--

DROP TABLE IF EXISTS `project_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_contacts` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `project_id` int(20) NOT NULL,
  `user_id` int(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `title` text,
  `description` text,
  `status` varchar(250) DEFAULT NULL,
  `notes` text,
  `accession_geo` varchar(50) DEFAULT NULL,
  `accession_sra` varchar(50) DEFAULT NULL,
  `accession_ena` varchar(50) DEFAULT NULL,
  `accession_ddjb` varchar(50) DEFAULT NULL,
  `assigned_to` varchar(250) DEFAULT NULL,
  `created` int(12) DEFAULT NULL,
  `modified` int(12) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=140 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(250) NOT NULL,
  `firstname` varchar(250) DEFAULT NULL,
  `surname` varchar(250) DEFAULT NULL,
  `group` varchar(250) DEFAULT NULL,
  `password` text NOT NULL,
  `authstring` text NOT NULL,
  `verification` varchar(250) DEFAULT NULL,
  `registered` int(12) DEFAULT NULL,
  `last_login` int(12) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-01-08 18:52:49
