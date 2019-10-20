-- MySQL dump 10.13  Distrib 5.7.22, for Linux (x86_64)
--
-- Host: localhost    Database: scotoobo
-- ------------------------------------------------------
-- Server version	5.7.22-0ubuntu0.16.04.1

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
  `invitation_date_from` datetime DEFAULT NULL,
  `invitation_date_to` datetime DEFAULT NULL,
  `participation_fields` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
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
INSERT INTO `event` VALUES (1,'TestEvent1','qwertzu qwertz qwertzu',100,'2017-01-01 00:00:00','2023-01-01 00:00:00','at home','JwUDSIZ9Icb39d7r5tlU5pDOiN8K0L3KhYo9HnjpAvsKJAbO3xJ9WnYfa70bS3Va2kjJHD8h9B1','2018-04-29 08:28:34','2200-01-01 00:00:00','[["name","Name",true,true],["email","E-Mail",true,false],["address","Adresse",false,false],["stamm","Stamm",false,false],["group","Gruppe",false,false],["eat","Essenswu00fcnsche",false,false],["comment","Kommentar",false,false]]'),(2,'TestEvent2','qwertzu qwertz qwertzu',177,'2010-01-01 00:00:00','2010-01-01 00:00:00','at home',NULL,NULL,NULL,NULL),(3,'TestEvent3','qwertzu qwertz qwertzu',38,'2011-01-01 00:00:00','2011-01-01 00:00:00','at home',NULL,NULL,NULL,NULL),(4,'TestEvent4','qwertzu qwertz qwertzu',125,'2003-01-01 00:00:00','2003-01-01 00:00:00','at home',NULL,NULL,NULL,NULL),(5,'TestEvent5','qwertzu qwertz qwertzu',82,'2004-01-01 00:00:00','2004-01-01 00:00:00','at home','vgp4fhAo1PXHSTl5BRPJrdfRa64FlLJR1','2017-01-01 00:00:00','2017-01-01 00:00:00','[["name","Name",true,true],["email","E-Mail",true,false],["address","Adresse",true,false],["stamm","Stamm",true,false],["group","Gruppe",true,false],["eat","Essenswu00fcnsche",true,false],["comment","Kommentar",true,false]]'),(6,'TestEvent6','qwertzu qwertz qwertzu',38,'2014-01-01 00:00:00','2014-01-01 00:00:00','at home',NULL,NULL,NULL,NULL),(7,'TestEvent7','qwertzu qwertz qwertzu',20,'2015-01-01 00:00:00','2015-01-01 00:00:00','at home','4aiScm83Z92FnqCrHhFvde37uC2vefQip9bCwjGvtIaQ8NhQ4XmicpqG2schH2A7cLJI5pdy8ophbG7f','2017-01-01 00:00:00','2017-01-01 00:00:00','[["name","Name",true,true],["email","E-Mail",true,true],["address","Adresse",true,true],["stamm","Stamm",true,true],["group","Gruppe",true,true],["eat","Essenswu00fcnsche",true,true],["comment","Kommentar",true,true]]'),(8,'TestEvent8','qwertzu qwertz qwertzu',97,'2005-01-01 00:00:00','2005-01-01 00:00:00','at home','uyQTYxWqJd8MNfYyZGEpUcxiBPtiXJXriNlglhH5uQRh5QQ5xvvrH3KjSdCPXzggnBwISeNn4FFavwf31KvINf2GtEvqeLHBnejgs7DxMiHiOXlPIQyw6BczgI0uuH5RVo7nvKVi3','2017-01-01 00:00:00','2017-01-01 00:00:00','[["name","Name",true,false],["email","E-Mail",true,false],["address","Adresse",true,false],["stamm","Stamm",true,false],["group","Gruppe",true,false],["eat","Essenswu00fcnsche",true,false],["comment","Kommentar",true,false]]'),(9,'TestEvent9','qwertzu qwertz qwertzu',159,'2015-01-01 00:00:00','2015-01-01 00:00:00','at home','ASAWHiNgPTR2t8KtCeaH555dtBYpT11uTCrBVfSK9KMCSx5uMgbRmh5QS3fM4ghYTJzOYrz7bmK3TPxF5Jxr0ChTFwGKNXIHGivFK4NWqxZkmxZsgxUh9caPJQAxOjevCKamO','2017-01-01 00:00:00','2017-01-01 00:00:00','[["name","Name",false,false],["email","E-Mail",true,false],["address","Adresse",true,false],["stamm","Stamm",true,false],["group","Gruppe",true,false],["eat","Essenswu00fcnsche",true,false],["comment","Kommentar",false,false]]'),(10,'TestEvent10','qwertzu qwertz qwertzu',120,'2003-01-01 00:00:00','2003-01-01 00:00:00','at home','VND5p99YztJoWIYi0ziH54IOd7RnSJ2NxGTWQ2VpvEOsnNLnm34r8NglV7JNRLBorulhwgH2VvuijfGFjL7rynNuuxiliTKKo52VmKXhgrAzHge11ltAJg4dNmz6gkRFpTAMEx3UZD','2017-01-01 00:00:00','2017-01-01 00:00:00','[["name","Name",true,true],["email","E-Mail",false,false],["address","Adresse",false,false],["stamm","Stamm",false,false],["group","Gruppe",false,false],["vegi","Vegetarier",false,false],["comment","Kommentar",true,false]]');
/*!40000 ALTER TABLE `event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `eventAttend`
--

DROP TABLE IF EXISTS `eventAttend`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eventAttend` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `datetime_registration` datetime NOT NULL,
  `firstname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_street` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_nr` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_plz` int(11) DEFAULT NULL,
  `address_city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `stamm` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `group` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` longtext COLLATE utf8_unicode_ci,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vegi` tinyint(1) DEFAULT NULL,
  `pig` tinyint(1) DEFAULT NULL,
  `vega` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eventAttend`
--

LOCK TABLES `eventAttend` WRITE;
/*!40000 ALTER TABLE `eventAttend` DISABLE KEYS */;
INSERT INTO `eventAttend` VALUES (1,5,'2017-10-09 21:25:14','testadmin','testadmin','123','45a',12345,'Hamburg','Ambronen','456','789',NULL,NULL,NULL,NULL),(2,7,'2017-10-12 22:51:40','testadmin','testadmin','123','4',12345,'567','Ambronen','890','Text','wqesbv@a.de',1,NULL,NULL),(3,7,'2017-10-15 22:24:44','testAdmin-Vorname','testAdmin-Nachname','bbb','1',13245,'c','Hagen von Tronje','d','e','a@a.de',1,1,NULL),(4,7,'2017-10-15 22:28:39','AAA','BBB','Ccc','456',78910,'Ddd','Anduril','Eee','Fff','a@a.de',0,1,1),(5,7,'2017-10-15 22:33:24','abc','def','jkl','456',45645,'mno','Ambronen','pqr','stuvw xyz','ghi@a.de',1,0,0),(6,5,'2017-10-19 21:57:50','testAdmin','testAdmin','','',NULL,'','Ambronen','','','',NULL,NULL,NULL),(7,1,'2018-04-29 00:41:48','testAdmin','testAdmin','','',NULL,'',NULL,NULL,'','vhjvj@gmx.de',NULL,NULL,NULL);
/*!40000 ALTER TABLE `eventAttend` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `material`
--

DROP TABLE IF EXISTS `material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `material` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_7CBE75955E237E06` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material`
--

LOCK TABLES `material` WRITE;
/*!40000 ALTER TABLE `material` DISABLE KEYS */;
/*!40000 ALTER TABLE `material` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `materialOffers`
--

DROP TABLE IF EXISTS `materialOffers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `materialOffers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `material_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `price` int(11) NOT NULL,
  `shop_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `materialOffers`
--

LOCK TABLES `materialOffers` WRITE;
/*!40000 ALTER TABLE `materialOffers` DISABLE KEYS */;
/*!40000 ALTER TABLE `materialOffers` ENABLE KEYS */;
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
INSERT INTO `userfeedback` VALUES (22,'asdf','browser','2017-10-01 21:28:43','http://127.0.0.1:8000/','htmlText','picture','','','',''),(23,'asdf','browser','2017-10-01 21:28:43','http://127.0.0.1:8000/','htmlText','picture','testambrone1','[\"ROLE_ambronen\",\"ROLE_stavo\",\"ROLE_schulung\",\"ROLE_nordlichter\",\"ROLE_wiki\",\"ROLE_webmaster@schulung.pbnl.de\",\"ROLE_webmaster@ambronen.pbnl.de\",\"ROLE_groupWithMailingList\",\"ROLE_groupWithoutMailingList\",\"ROLE_elder\",\"ROLE_USER\"]','Ambronen','');
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

-- Dump completed on 2018-04-29 14:32:49
