-- MySQL dump 10.13  Distrib 5.7.19, for Linux (x86_64)
--
-- Host: localhost    Database: scotoobo
-- ------------------------------------------------------
-- Server version	5.7.19-0ubuntu0.16.04.1

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
-- Current Database: `scotoobo`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `scotoobo` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `scotoobo`;

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `price` int(10) unsigned NOT NULL,
  `date_from` datetime NOT NULL,
  `date_to` datetime NOT NULL,
  `place` longtext COLLATE utf8_unicode_ci NOT NULL,
  `invitation_link` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_3BAE0AA75E237E06` (`name`),
  UNIQUE KEY `UNIQ_3BAE0AA7FBAD55A5` (`invitation_link`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event`
--

LOCK TABLES `event` WRITE;
/*!40000 ALTER TABLE `event` DISABLE KEYS */;
INSERT INTO `event` VALUES (1,'TestEvent1','qwertzu qwertz qwertzu',100,'2017-01-01 00:00:00','2017-01-01 00:00:00','at home',NULL),(2,'TestEvent2','qwertzu qwertz qwertzu',177,'2010-01-01 00:00:00','2010-01-01 00:00:00','at home',NULL),(3,'TestEvent3','qwertzu qwertz qwertzu',38,'2011-01-01 00:00:00','2011-01-01 00:00:00','at home',NULL),(4,'TestEvent4','qwertzu qwertz qwertzu',125,'2003-01-01 00:00:00','2003-01-01 00:00:00','at home',NULL),(5,'TestEvent5','qwertzu qwertz qwertzu',82,'2004-01-01 00:00:00','2004-01-01 00:00:00','at home','gdNqaY3ntnUJUM3IsEOUJGGADIplmcZCqN2AL5XftSYoE177GV2pBJZfspBOCBq2otDayBp2toq8qxg6siw42wkuVWiyxIAVbd5KPvNiTdqjLHpe0Wi2sDwozOW7xw3JK8uzEhS'),(6,'TestEvent6','qwertzu qwertz qwertzu',38,'2014-01-01 00:00:00','2014-01-01 00:00:00','at home','km8eUQzuXVtWwB8JnSqoNuHs3v5MUIjf5ruZh4teZXbwyjgVbGkZa2sexx0sfjHkLck3hOhgLtNkM4gYKAXVDp9aXaCcukxgxRjOGB5r4SMQX2PHDN'),(7,'TestEvent7','qwertzu qwertz qwertzu',20,'2015-01-01 00:00:00','2015-01-01 00:00:00','at home','cBDyugamgKvvXaC1MVJCTxHlz6lYZB8bcLKG1U3iEzNCJpD'),(8,'TestEvent8','qwertzu qwertz qwertzu',97,'2005-01-01 00:00:00','2005-01-01 00:00:00','at home','wTmNDG2t42C2L77aVAEmx7cNiKzddVpJOLxssAVxCyzoFGyAhdWOl8'),(9,'TestEvent9','qwertzu qwertz qwertzu',159,'2015-01-01 00:00:00','2015-01-01 00:00:00','at home','cKckMn1UhVsq24jhfzuMJYaP5uAreLprvCMiZNchIEHLJ12ZBxMkvWaBrL2Fws714Tj4Gvmoa4aT6dSHKE1fBcR2XUItmPurIOwojTNuXYn3bfKVUMbvY3yWXgpk6UMOIjd2c1w9ZTdb9X63KizJl7Fin5DuZpjHIwKUyg3yahJkeQoZ9XJu4oNstqWtQgayMUsl'),(10,'TestEvent10','qwertzu qwertz qwertzu',120,'2003-01-01 00:00:00','2003-01-01 00:00:00','at home',NULL);
/*!40000 ALTER TABLE `event` ENABLE KEYS */;
/*!40000 ALTER TABLE `event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userfeedback`
--

DROP TABLE IF EXISTS `userfeedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userfeedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text` longtext COLLATE utf8_unicode_ci NOT NULL,
  `browser_data` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `html_content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `picture` longtext COLLATE utf8_unicode_ci NOT NULL,
  `user_uid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_roles` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_stamm` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userfeedback`
--

LOCK TABLES `userfeedback` WRITE;
/*!40000 ALTER TABLE `userfeedback` DISABLE KEYS */;
INSERT INTO `userfeedback` VALUES
	(22,'asdf','browser','2017-10-01 21:28:43','http://127.0.0.1:8000/','htmlText','picture','','','',''),
	(23,'asdf','browser','2017-10-01 21:28:43','http://127.0.0.1:8000/','htmlText','picture','testambrone1','[\"ROLE_ambronen\",\"ROLE_stavo\",\"ROLE_schulung\",\"ROLE_nordlichter\",\"ROLE_wiki\",\"ROLE_webmaster@schulung.pbnl.de\",\"ROLE_webmaster@ambronen.pbnl.de\",\"ROLE_groupWithMailingList\",\"ROLE_groupWithoutMailingList\",\"ROLE_elder\",\"ROLE_USER\"]','Ambronen','');
/*!40000 ALTER TABLE `userfeedback` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-10-09  0:07:01
