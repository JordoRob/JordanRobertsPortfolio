-- MySQL dump 10.13  Distrib 8.0.33, for Linux (x86_64)
--
-- Host: mysql-server    Database: HE_Schedule
-- ------------------------------------------------------
-- Server version	8.0.33

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
-- Table structure for table `assignments`
--

DROP TABLE IF EXISTS `assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assignments` (
  `assignment_id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int DEFAULT NULL,
  `job_id` int DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `assigner` int DEFAULT NULL,
  PRIMARY KEY (`assignment_id`),
  KEY `employee_id` (`employee_id`),
  KEY `job_id` (`job_id`),
  KEY `assigner` (`assigner`),
  CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assignments_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `job` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assignments_ibfk_3` FOREIGN KEY (`assigner`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assignments`
--

LOCK TABLES `assignments` WRITE;
/*!40000 ALTER TABLE `assignments` DISABLE KEYS */;
INSERT INTO `assignments` VALUES (1,1,1,'2023-01-01','2023-02-01',NULL),(2,1,1,'2023-03-01','2023-05-01',NULL),(3,2,1,'2023-01-01','2023-02-01',NULL),(4,2,1,'2023-02-01','2023-03-01',NULL),(5,2,1,'2023-03-01','2023-04-01',NULL),(6,3,2,'2023-02-01','2023-03-01',NULL),(7,3,2,'2023-03-01','2023-04-01',NULL),(8,3,2,'2023-04-01','2023-05-01',NULL),(9,1,3,'2023-02-01','2023-03-01',NULL),(10,2,3,'2023-04-01','2022-05-01',NULL),(11,3,3,'2023-05-01','2023-06-01',NULL),(12,4,3,'2023-01-01','2023-05-01',NULL);
/*!40000 ALTER TABLE `assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee`
--

DROP TABLE IF EXISTS `employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role` int DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `active` int DEFAULT '0',
  `archived` date DEFAULT NULL,
  `img` varchar(100) DEFAULT 'img/emp/default.png',
  `birthday` date DEFAULT NULL,
  `phoneNum` varchar(15) DEFAULT '(000) 000-0000',
  `phoneNumSecondary` varchar(15) DEFAULT '(000) 000-0000',
  `notes` text,
  `email` varchar(200) DEFAULT 'Empty',
  `hired` date DEFAULT NULL,
  `redseal` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee`
--

LOCK TABLES `employee` WRITE;
/*!40000 ALTER TABLE `employee` DISABLE KEYS */;
INSERT INTO `employee` VALUES (1,0,'John Smith',0,NULL,'img/emp/default.png','1975-06-12','(123) 555-1234','(000) 000-0000',NULL,'Empty','2010-03-15',0),(2,1,'Michael Johnson',0,NULL,'img/emp/default.png','1982-09-25','(456) 555-5678','(000) 000-0000',NULL,'Empty','2015-11-02',0),(3,2,'David Williams',0,NULL,'img/emp/default.png','1988-02-07','(789) 555-9876','(000) 000-0000',NULL,'Empty','2018-07-10',0),(4,2,'James Brown',0,NULL,'img/emp/default.png','1991-11-18','(012) 555-2468','(000) 000-0000',NULL,'Empty','2016-05-20',0),(5,1,'Sarah Davis',1,NULL,'img/emp/default.png','1980-05-03','(345) 555-7890','(000) 000-0000',NULL,'Empty',NULL,0),(6,2,'Emily Wilson',0,NULL,'img/emp/default.png','1985-08-22','(678) 555-1357','(000) 000-0000',NULL,'Empty','2019-09-05',0),(7,3,'Daniel Anderson',0,NULL,'img/emp/default.png','1993-03-31','(901) 555-8024','(000) 000-0000',NULL,'Empty','2020-02-18',0),(8,4,'Jessica Martinez',0,NULL,'img/emp/default.png','1979-07-16','(234) 555-3690','(000) 000-0000',NULL,'Empty','2017-08-12',0),(9,5,'Christopher Taylor',0,NULL,'img/emp/default.png','1987-12-09','(567) 555-6743','(000) 000-0000',NULL,'Empty','2014-12-01',0),(10,0,'Matthew Clark',-1,'2023-01-01','img/emp/default.png','1972-04-28','(890) 555-2468','(000) 000-0000',NULL,'Empty','2008-09-30',0),(11,1,'Jennifer Rodriguez',0,NULL,'img/emp/default.png','1995-08-10','(123) 555-9876','(000) 000-0000',NULL,'Empty','2021-03-05',0),(12,2,'Robert Hernandez',0,NULL,'img/emp/default.png','1984-02-15','(456) 555-2345','(000) 000-0000',NULL,'Empty','2019-11-20',0),(13,4,'Karen Lee',0,NULL,'img/emp/default.png','1990-05-21','(789) 555-7890','(000) 000-0000',NULL,'Empty','2017-07-10',0),(14,1,'Joshua Thomas',0,NULL,'img/emp/default.png','1989-09-08','(012) 555-3456','(000) 000-0000',NULL,'Empty','2014-06-15',0),(15,3,'Michelle Scott',0,NULL,'img/emp/default.png','1983-03-12','(345) 555-6789','(000) 000-0000',NULL,'Empty','2012-09-30',0),(16,0,'Andrew Green',0,NULL,'img/emp/default.png','1977-07-29','(678) 555-9012','(000) 000-0000',NULL,'Empty','2011-04-18',0),(17,1,'Emily Baker',0,NULL,'img/emp/default.png','1992-12-01','(901) 555-3456','(000) 000-0000',NULL,'Empty','2018-08-22',0),(18,2,'David Reed',0,NULL,'img/emp/default.png','1986-06-19','(234) 555-7890','(000) 000-0000',NULL,'Empty','2020-02-05',0),(19,4,'Amy Turner',0,NULL,'img/emp/default.png','1994-01-07','(567) 555-1234','(000) 000-0000',NULL,'Empty','2016-09-10',0),(20,0,'William Cooper',0,NULL,'img/emp/default.png','1981-04-25','(890) 555-6789','(000) 000-0000',NULL,'Empty','2013-07-28',0),(21,1,'Olivia Morgan',0,NULL,'img/emp/default.png','1988-10-11','(432) 555-9012','(000) 000-0000',NULL,'Empty','2017-03-15',0),(22,1,'Daniel Allen',0,NULL,'img/emp/default.png','1991-11-27','(765) 555-2345','(000) 000-0000',NULL,'Empty','2019-01-20',0),(23,1,'Sophia Ward',0,NULL,'img/emp/default.png','1996-05-18','(321) 555-5678','(000) 000-0000',NULL,'Empty','2021-08-05',0),(24,2,'Matthew Evans',0,NULL,'img/emp/default.png','1987-09-03','(654) 555-7890','(000) 000-0000',NULL,'Empty','2015-06-10',0),(25,1,'Ava Turner',0,NULL,'img/emp/default.png','1993-02-22','(987) 555-1234','(000) 000-0000',NULL,'Empty','2018-09-25',0),(26,1,'Alexander Collins',0,NULL,'img/emp/default.png','1990-07-14','(876) 555-4567','(000) 000-0000',NULL,'Empty','2016-05-20',0),(27,1,'Abigail Brooks',0,NULL,'img/emp/default.png','1985-12-17','(543) 555-7890','(000) 000-0000',NULL,'Empty','2012-08-12',0),(28,0,'Michael Edwards',2,NULL,'img/emp/default.png','1979-05-06','(210) 555-1234','(000) 000-0000',NULL,'Empty','2010-11-01',0),(29,1,'Samantha Morris',2,NULL,'img/emp/default.png','1994-11-30','(109) 555-4567','(000) 000-0000',NULL,'Empty','2019-03-05',0),(30,2,'Benjamin Bennett',0,NULL,'img/emp/default.png','1983-06-27','(876) 555-8901','(000) 000-0000',NULL,'Empty','2017-09-10',0),(31,2,'Charlotte Gray',0,NULL,'img/emp/default.png','1989-09-13','(543) 555-2345','(000) 000-0000',NULL,'Empty','2015-05-15',0),(32,3,'David Hill',0,NULL,'img/emp/default.png','1992-01-25','(210) 555-5678','(000) 000-0000',NULL,'Empty','2018-02-20',0),(33,1,'Ella Phillips',0,NULL,'img/emp/default.png','1996-04-08','(109) 555-8901','(000) 000-0000',NULL,'Empty','2021-07-10',0),(34,0,'Joseph Turner',0,NULL,'img/emp/default.png','1976-08-11','(876) 555-2345','(000) 000-0000',NULL,'Empty','2011-03-05',0),(35,2,'Mia Nelson',0,NULL,'img/emp/default.png','1993-10-29','(543) 555-5678','(000) 000-0000',NULL,'Empty','2017-11-20',0),(36,3,'Jacob White',0,NULL,'img/emp/default.png','1988-03-07','(210) 555-9012','(000) 000-0000',NULL,'Empty','2013-09-15',0),(37,3,'Avery Russell',0,NULL,'img/emp/default.png','1991-04-14','(109) 555-2345','(000) 000-0000',NULL,'Empty','2019-06-10',0),(38,2,'Grace Jenkins',0,NULL,'img/emp/default.png','1984-11-09','(876) 555-5678','(000) 000-0000',NULL,'Empty','2015-09-25',0),(39,2,'Daniel Adams',0,NULL,'img/emp/default.png','1989-12-23','(543) 555-9012','(000) 000-0000',NULL,'Empty','2016-02-20',0),(40,3,'Sophia Clark',0,NULL,'img/emp/default.png','1994-03-05','(210) 555-2345','(000) 000-0000',NULL,'Empty','2020-07-15',0),(41,4,'James Bailey',0,NULL,'img/emp/default.png','1997-06-16','(109) 555-5678','(000) 000-0000',NULL,'Empty','2022-01-10',0);
/*!40000 ALTER TABLE `employee` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `set_active_before_insert` BEFORE INSERT ON `employee` FOR EACH ROW BEGIN
    IF NEW.archived IS NOT NULL THEN
        SET NEW.active = -1;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `emp_insert` AFTER INSERT ON `employee` FOR EACH ROW UPDATE update_time 
SET last_update=CURRENT_TIMESTAMP() 
WHERE table_name LIKE 'employee' */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `set_active_before_update` BEFORE UPDATE ON `employee` FOR EACH ROW BEGIN
    IF NEW.active = -1 AND OLD.archived IS NULL THEN
        SET NEW.archived = CURRENT_DATE();
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `emp_update` AFTER UPDATE ON `employee` FOR EACH ROW UPDATE update_time 
SET last_update=CURRENT_TIMESTAMP() 
WHERE table_name LIKE 'employee' */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `job`
--

DROP TABLE IF EXISTS `job`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `archived` date DEFAULT NULL,
  `manager_name` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job`
--

LOCK TABLES `job` WRITE;
/*!40000 ALTER TABLE `job` DISABLE KEYS */;
INSERT INTO `job` VALUES (1,'McDonald\'s Building 311 Vernon St.',NULL,NULL,'John Smith','2023-01-01','2024-04-30',NULL),(2,'Starbucks Building 123 Main St.',NULL,NULL,'Jane Doe','2023-02-01','2024-05-31',NULL),(3,'Walmart Supercenter Construction Project',NULL,NULL,'Michael Johnson','2023-03-01','2024-06-30',NULL),(4,'Bank of America Tower Construction',NULL,NULL,'Emily Williams','2023-04-01','2024-07-31',NULL),(5,'Residential Complex on Park Avenue',NULL,NULL,'Robert Brown','2023-05-01','2024-08-31',NULL),(6,'School Renovation Project at Smith High',NULL,NULL,'Jennifer Lee','2023-06-01','2024-09-30',NULL),(7,'Office Building Construction on 5th Avenue',NULL,NULL,'David Miller','2023-07-01','2024-10-31',NULL),(8,'Shopping Mall Expansion Project',NULL,NULL,'Sarah Davis','2023-08-01','2024-11-30',NULL),(9,'Hospital Construction at City Medical Center',NULL,NULL,'James Anderson','2023-09-01','2024-12-31',NULL),(10,'Resort Construction on Paradise Island',NULL,NULL,'Linda Martinez','2023-10-01','2025-01-31',NULL),(11,'Sports Stadium Redevelopment Project',NULL,NULL,'William Wilson','2023-11-01','2025-02-28',NULL),(12,'Airport Terminal Expansion at International Airport',NULL,NULL,'Karen Taylor','2023-12-01','2025-03-31',NULL),(13,'Convention Center Construction on Ocean Boulevard',NULL,NULL,'Richard Johnson','2024-01-01','2025-04-30',NULL),(14,'Apartment Complex Construction on Elm Street',NULL,NULL,'Elizabeth Davis','2024-02-01','2025-05-31',NULL),(15,'Highway Bridge Rehabilitation Project',NULL,NULL,'Michael Brown','2024-03-01','2025-06-30',NULL),(16,'Theme Park Construction at Adventureland',NULL,NULL,'Jennifer Miller','2024-04-01','2025-07-31',NULL),(17,'University Building Renovation at State University',NULL,NULL,'Robert Lee','2024-05-01','2025-08-31',NULL),(18,'Retail Store Remodeling on Market Street',NULL,NULL,'Karen Williams','2024-06-01','2025-09-30',NULL),(19,'Hotel Construction on Sunset Boulevard',NULL,NULL,'David Davis','2024-07-01','2025-10-31',NULL),(20,'Archived Job Test',NULL,'2023-04-30','Michael Johnson','2022-03-01','2023-04-30',NULL);
/*!40000 ALTER TABLE `job` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `job_insert` AFTER INSERT ON `job` FOR EACH ROW UPDATE update_time 
SET last_update=CURRENT_TIMESTAMP() 
WHERE table_name LIKE 'job' */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `job_update` AFTER UPDATE ON `job` FOR EACH ROW UPDATE update_time 
SET last_update=CURRENT_TIMESTAMP() 
WHERE table_name LIKE 'job' */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `outlook`
--

DROP TABLE IF EXISTS `outlook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `outlook` (
  `job_id` int NOT NULL,
  `date` date NOT NULL,
  `count` int DEFAULT NULL,
  PRIMARY KEY (`job_id`,`date`),
  CONSTRAINT `outlook_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `job` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outlook`
--

LOCK TABLES `outlook` WRITE;
/*!40000 ALTER TABLE `outlook` DISABLE KEYS */;
INSERT INTO `outlook` VALUES (1,'2023-07-01',5),(1,'2023-08-01',4),(1,'2023-09-01',4),(1,'2023-10-01',3),(2,'2023-06-01',3),(2,'2023-07-01',3),(2,'2023-08-01',2),(2,'2023-09-01',2),(3,'2023-07-01',4),(3,'2023-08-01',3),(3,'2023-09-01',3),(3,'2023-10-01',2);
/*!40000 ALTER TABLE `outlook` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `outlook_insert` AFTER INSERT ON `outlook` FOR EACH ROW UPDATE update_time 
SET last_update=CURRENT_TIMESTAMP() 
WHERE table_name LIKE 'outlook' */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `outlook_update` AFTER UPDATE ON `outlook` FOR EACH ROW UPDATE update_time 
SET last_update=CURRENT_TIMESTAMP() 
WHERE table_name LIKE 'outlook' */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `update_time`
--

DROP TABLE IF EXISTS `update_time`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `update_time` (
  `table_name` varchar(50) NOT NULL,
  `last_update` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`table_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `update_time`
--

LOCK TABLES `update_time` WRITE;
/*!40000 ALTER TABLE `update_time` DISABLE KEYS */;
INSERT INTO `update_time` VALUES ('employee',NULL),('job',NULL),('outlook',NULL),('user',NULL),('worksOn',NULL);
/*!40000 ALTER TABLE `update_time` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(64) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `recent_password_reset` tinyint(1) DEFAULT '1',
  `is_admin` tinyint(1) DEFAULT '0',
  `custom_order` mediumtext,
  `use_background` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'admin','e3274be5c857fb42ab72d786e281b4b8',1,1,NULL,0,'2023-08-04 14:52:55'),(2,'projector','9bee40e2e8f8e555ef7b6274d9376e09',0,2,NULL,0,'2023-08-04 14:52:55'),(3,'johnsmith','482c811da5d5b4bc6d497ffa98491e38',1,0,NULL,0,'2023-08-04 14:52:55'),(4,'testadmin','e3274be5c857fb42ab72d786e281b4b8',0,1,NULL,0,'2023-08-04 14:52:55'),(5,'testuser1','5f4dcc3b5aa765d61d8327deb882cf99',0,0,NULL,0,'2023-08-04 14:52:55'),(6,'testuser2','5f4dcc3b5aa765d61d8327deb882cf99',0,0,NULL,0,'2023-08-04 14:52:55'),(7,'testuser3','5f4dcc3b5aa765d61d8327deb882cf99',0,0,NULL,0,'2023-08-04 14:52:55');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `custom_order_update` AFTER UPDATE ON `user` FOR EACH ROW UPDATE update_time
SET last_update=CURRENT_TIMESTAMP()
WHERE table_name LIKE 'user' */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `worksOn`
--

DROP TABLE IF EXISTS `worksOn`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `worksOn` (
  `employee_id` int NOT NULL,
  `job_id` int NOT NULL,
  PRIMARY KEY (`employee_id`,`job_id`),
  UNIQUE KEY `employee_id` (`employee_id`,`job_id`),
  KEY `job_id` (`job_id`),
  CONSTRAINT `worksOn_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`) ON DELETE CASCADE,
  CONSTRAINT `worksOn_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `job` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `worksOn`
--

LOCK TABLES `worksOn` WRITE;
/*!40000 ALTER TABLE `worksOn` DISABLE KEYS */;
INSERT INTO `worksOn` VALUES (1,1),(2,1),(3,2),(4,2),(6,3),(7,3),(8,3);
/*!40000 ALTER TABLE `worksOn` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `worksOn_insert` AFTER INSERT ON `worksOn` FOR EACH ROW UPDATE update_time 
SET last_update=CURRENT_TIMESTAMP() 
WHERE table_name LIKE 'worksOn' */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `worksOn_update` AFTER UPDATE ON `worksOn` FOR EACH ROW UPDATE update_time 
SET last_update=CURRENT_TIMESTAMP() 
WHERE table_name LIKE 'worksOn' */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-08-04  7:53:06
