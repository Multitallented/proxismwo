-- MySQL dump 10.13  Distrib 5.5.23, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: cw
-- ------------------------------------------------------
-- Server version	5.5.23-2

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
-- Table structure for table `alliance`
--

DROP TABLE IF EXISTS `alliance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alliance` (
  `sender` varchar(25) DEFAULT NULL,
  `ally` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alliance`
--

LOCK TABLES `alliance` WRITE;
/*!40000 ALTER TABLE `alliance` DISABLE KEYS */;
INSERT INTO `alliance` VALUES ('cef command','jagdcrab'),('cef command','blackfire'),('cef command','multitallented'),('doktor totenkopf','mista_whizzard'),('doktor totenkopf','dhbna'),('mista_whizzard','doktor totenkopf'),('mista_whizzard','dhbna'),('mista_whizzard','wm command'),('mista_whizzard','samson'),('wm command','mista_whizzard'),('samson','mista_whizzard'),('samson','hrunting'),('vas79','themagician'),('themagician','vas79'),('hrunting','samson'),('hrunting','blackfire'),('cef command','samson'),('jerous','cef command'),('cef command','jerous'),('mista_whizzard','hrunting'),('iridin','themagician'),('themagician','iridin'),('mista_whizzard','blackfire'),('mista_whizzard','jagdcrab'),('doktor totenkopf','samson'),('doktor totenkopf','hrunting'),('doktor totenkopf','wm command'),('cef command','doktor totenkopf'),('doktor totenkopf','jagdcrab'),('iridin','vas79'),('vas79','iridin'),('greatzen','themagician'),('blackfire','hrunting'),('greatzen','iridin'),('iridin','greatzen'),('wm command','dhbna'),('cef command','mufasa'),('themagician','greatzen'),('wm command','doktor totenkopf'),('mufasa','cef command'),('greatzen','vas79'),('samson','doktor totenkopf'),('mista_whizzard','cef command'),('iridin','mausgmr'),('mausgmr','iridin'),('mech79','c0vvb3ll'),('c0vvb3ll','mech79'),('mech79','multitallented'),('themagician','cef command'),('cef command','themagician'),('murzao','hrunting'),('murzao','samson'),('samson','murzao'),('cef command','mech79'),('cef command','iridin');
/*!40000 ALTER TABLE `alliance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dropship`
--

DROP TABLE IF EXISTS `dropship`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dropship` (
  `dropship_id` int(11) NOT NULL AUTO_INCREMENT,
  `owner` varchar(25) NOT NULL,
  `planet_name` varchar(50) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `last_move` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`dropship_id`)
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dropship`
--

LOCK TABLES `dropship` WRITE;
/*!40000 ALTER TABLE `dropship` DISABLE KEYS */;
/*!40000 ALTER TABLE `dropship` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `market`
--

DROP TABLE IF EXISTS `market`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `market` (
  `mech` varchar(10) NOT NULL,
  `base_price` int(11) NOT NULL,
  `volatility` decimal(11,2) DEFAULT NULL,
  `buy` int(11) NOT NULL,
  `tons` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `market`
--

LOCK TABLES `market` WRITE;
/*!40000 ALTER TABLE `market` DISABLE KEYS */;
INSERT INTO `market` VALUES ('COM-1D',872263,1.30,0,25),('COM-1B',859392,1.25,0,25),('COM-3A',909476,1.25,0,25),('COM-DK',1499846,1.00,0,25),('COM-2D',1781542,0.75,0,25),('SDR-5V',1299270,1.40,0,30),('SDR-5K',2052540,1.10,0,30),('SDR-5D',2533540,0.50,0,30),('JR7-D',3284548,0.90,0,35),('JR7-F',2893066,0.45,0,35),('JR7-K',2863927,1.10,0,35),('RVN-2X',2389573,1.40,0,35),('RVN-4X',2520666,1.15,0,35),('RVN-3L',4862992,0.65,0,35),('CDA-2A',3194944,1.00,0,40),('CDA-2B',3182436,1.40,0,40),('CDA-3C',2885739,1.50,0,40),('CDA-3M',6097444,0.70,20,40),('CDA-X5',3174923,1.20,0,40),('BJ-1',3385436,1.00,0,45),('BJ-1DC',2347828,1.40,0,45),('BJ-1X',3144528,1.35,0,45),('BJ-3',4000461,1.25,0,45),('CN9-A',3697080,1.00,0,50),('CN9-AL',3552238,1.20,0,50),('CN9-D',6158590,0.80,0,50),('CN9-YLW',4829827,0.60,0,50),('HBK-4G',3694490,1.20,0,50),('HBK-4H',3646484,1.15,0,50),('HBK-4J',3778604,1.35,0,50),('HBK-4P',3594484,0.90,0,50),('HBK-4SP',3632484,0.85,0,50),('TBT-3C',4503816,0.60,0,50),('TBT-5J',4186745,1.00,22,50),('TBT-5N',4186745,1.20,0,50),('TBT-7K',3867177,1.35,0,50),('TBT-7M',5623797,0.80,0,50),('KTO-18',4474807,0.75,0,55),('KTO-19',4589395,1.00,0,55),('KTO-20',4697395,1.00,0,55),('KTO-BOY',4402988,1.10,0,55),('DGN-1C',4745200,1.40,0,60),('DGN-1N',4827559,1.45,0,60),('DGN-5N',5097559,1.35,0,60),('DGN-FA',5082843,1.00,0,60),('DGN-FL',5502987,0.75,0,60),('QKD-4G',5377467,1.25,0,60),('QKD-4H',5337467,1.00,0,60),('QKD-5K',5578867,1.20,0,60),('CPLT-A1',5564279,0.90,0,65),('CPLT-C1',5804127,0.95,0,65),('CPLT-C4',5869247,1.20,0,65),('CPLT-K2',5304023,0.65,0,65),('JM6-A',5473070,0.85,0,65),('JM6-DD',7513253,1.00,0,65),('JM6-FB',7028398,0.60,0,65),('JM6-S',5201918,0.70,0,65),('CTF-1X',5950963,1.10,0,70),('CTF-2X',5755995,1.40,0,70),('CTF-3D',9881953,0.50,0,70),('CTF-4X',5229289,0.70,0,70),('CTF-IM',6382984,0.65,0,70),('ON1-PR',7349298,1.30,0,75),('ON1-K',6510685,1.35,0,75),('ON1-M',7715293,1.25,0,75),('ON1-V',6628263,1.30,0,75),('ON1-VA',6247565,1.40,0,75),('AWS-8Q',6764270,1.45,0,80),('AWS-8R',6524390,1.00,0,80),('AWS-8T',6704390,1.35,0,80),('AWS-8V',6574390,1.50,0,80),('AWS-9M',8534582,1.50,0,80),('AWS-PB',8529038,1.50,0,80),('VTR-9B',7410423,1.15,0,80),('VTR-9K',7710430,1.25,0,80),('VTR-9S',7440407,1.00,0,80),('VTR-DS',7918378,0.80,0,80),('STK-3F',7618480,0.80,0,85),('STK-3H',7818480,1.40,0,85),('STK-4N',7412420,1.20,0,85),('STK-5M',8064616,1.00,0,85),('STK-5S',8921480,0.90,0,85),('STK-M',9281997,0.60,0,85),('HGN-732',9032840,0.70,0,90),('HGN-733P',8520422,1.30,0,90),('HGN-733',8504422,1.00,0,90),('HGN-733C',8655368,0.65,0,90),('HGN-HM',8621039,1.00,0,90),('AS7-BH',10495728,1.00,0,100),('AS7-D',9676072,1.20,0,100),('AS7-D-DC',10486012,0.75,0,100),('AS7-K',11691066,1.35,0,100),('AS7-RS',9358066,0.90,0,100);
/*!40000 ALTER TABLE `market` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `match`
--

DROP TABLE IF EXISTS `match`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `match` (
  `match_id` int(11) NOT NULL AUTO_INCREMENT,
  `attacker` varchar(25) NOT NULL,
  `defender` varchar(25) NOT NULL,
  `planet_name` varchar(50) DEFAULT NULL,
  `declared` timestamp NULL DEFAULT NULL,
  `responded` timestamp NULL DEFAULT NULL,
  `reported` timestamp NULL DEFAULT NULL,
  `report_response` timestamp NULL DEFAULT NULL,
  `resolved` timestamp NULL DEFAULT NULL,
  `winner` varchar(25) DEFAULT NULL,
  `last_action` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mercenary` varchar(25) DEFAULT NULL,
  `mercenary_qty` int(11) DEFAULT '0',
  `mercenary_rply` int(1) DEFAULT '0',
  `mercenary_time` timestamp NULL DEFAULT NULL,
  `defender_mercenary_rply` int(1) DEFAULT NULL,
  `defender_mercenary_qty` int(11) DEFAULT NULL,
  `defender_mercenary_time` timestamp NULL DEFAULT NULL,
  `defender_mercenary` varchar(25) DEFAULT NULL,
  `attacker_url` varchar(1000) DEFAULT NULL,
  `defender_url` varchar(1000) DEFAULT NULL,
  `attacker_lost_mechs` varchar(250) DEFAULT NULL,
  `defender_lost_mechs` varchar(250) DEFAULT NULL,
  `amerc_lost_mechs` varchar(200) DEFAULT NULL,
  `dmerc_lost_mechs` varchar(200) DEFAULT NULL,
  `aftermath_report` varchar(4000) DEFAULT NULL,
  `extension` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`match_id`)
) ENGINE=InnoDB AUTO_INCREMENT=703 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `match`
--

LOCK TABLES `match` WRITE;
/*!40000 ALTER TABLE `match` DISABLE KEYS */;
INSERT INTO `match` VALUES (702,'multitallented','tester01','test','2013-10-12 20:55:33','2013-10-12 21:01:12','2013-10-12 21:01:44','2013-10-12 21:01:52','2013-10-12 21:01:52','multitallented','2013-10-12 21:01:52',NULL,0,0,NULL,NULL,NULL,NULL,NULL,'testing.jpg',NULL,'5 CDA-3M, 5 TBT-5J','6 CDA-3M, 6 TBT-5J',NULL,NULL,'<span class=\"red\">multitallented lost 1 CDA-3M</span><br><span class=\"red\">multitallented lost 1 CDA-3M</span><br><span class=\"red\">multitallented lost 1 CDA-3M</span><br><span class=\"green\">tester01 salvaged CDA-3M</span><br><span class=\"red\">multitallented lost 1 CDA-3M</span><br><span class=\"green\">tester01 salvaged CDA-3M</span><br><span class=\"red\">multitallented lost 1 TBT-5J</span><br><span class=\"red\">multitallented lost 1 TBT-5J</span><br><span class=\"green\">tester01 salvaged TBT-5J</span><br><span class=\"red\">tester01 lost 1 CDA-3M</span><br><span class=\"red\">tester01 lost 1 CDA-3M</span><br><span class=\"red\">tester01 lost 1 CDA-3M</span><br><span class=\"red\">tester01 lost 1 CDA-3M</span><br><span class=\"red\">tester01 lost 1 CDA-3M</span><br><span class=\"green\">multitallented salvaged CDA-3M</span><br><span class=\"red\">tester01 lost 1 TBT-5J</span><br><span class=\"red\">tester01 lost 1 TBT-5J</span><br><span class=\"red\">tester01 lost 1 TBT-5J</span><br><span class=\"green\">1,000,000cbills was stolen from planet test</span><br><span class=\"green\">multitallented gained 60,628,930cbills in earnings and salvaged parts/data/supplies/etc.</span><br><span class=\"green\">tester01 gained 54,924,314cbills in earnings and salvaged parts/data/supplies/etc.</span><br><span class=\"green\">multitallented captured planet test and 9,000,000cbills and all mechs stationed there</span><br>',0);
/*!40000 ALTER TABLE `match` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `match_mech`
--

DROP TABLE IF EXISTS `match_mech`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `match_mech` (
  `mech` varchar(20) NOT NULL,
  `match_id` int(11) NOT NULL,
  `owner` varchar(25) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `dropship_id` int(11) DEFAULT NULL,
  `planet_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `match_mech`
--

LOCK TABLES `match_mech` WRITE;
/*!40000 ALTER TABLE `match_mech` DISABLE KEYS */;
INSERT INTO `match_mech` VALUES ('TBT-5J',1,'multitallented',6,NULL,'blackjack');
/*!40000 ALTER TABLE `match_mech` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mech`
--

DROP TABLE IF EXISTS `mech`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mech` (
  `mech` varchar(20) NOT NULL,
  `username` varchar(25) NOT NULL,
  `quantity` int(11) NOT NULL,
  `dropship_id` int(11) DEFAULT NULL,
  `planet_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mech`
--

LOCK TABLES `mech` WRITE;
/*!40000 ALTER TABLE `mech` DISABLE KEYS */;
INSERT INTO `mech` VALUES ('CDA-3M','multitallented',8,NULL,'blackjack'),('AS7-K','multitallented',3,NULL,'sommerset'),('AS7-K','multitallented',3,1,NULL);
/*!40000 ALTER TABLE `mech` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message` (
  `sent` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sender` varchar(25) NOT NULL,
  `reciever` varchar(25) NOT NULL,
  `message` varchar(5000) NOT NULL,
  `subject` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message`
--

LOCK TABLES `message` WRITE;
/*!40000 ALTER TABLE `message` DISABLE KEYS */;
/*!40000 ALTER TABLE `message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `notification_type` varchar(25) NOT NULL,
  `username` varchar(25) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sender` varchar(25) DEFAULT NULL,
  `value` int(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES ('attack','tester01','2013-10-12 20:55:33','multitallented',0),('attack declared','multitallented','2013-10-12 20:55:33','tester01',1),('defend','multitallented','2013-10-12 21:01:12','tester01',0),('defend','tester01','2013-10-12 21:01:12','multitallented',0),('report','multitallented','2013-10-12 21:01:44','tester01',0),('report declared','tester01','2013-10-12 21:01:44','multitallented',0),('win','multitallented','2013-10-12 21:01:52','tester01',702),('loss','tester01','2013-10-12 21:01:52','multitallented',702);
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `planet`
--

DROP TABLE IF EXISTS `planet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `planet` (
  `planet_name` varchar(50) NOT NULL,
  `owner_name` varchar(25) NOT NULL DEFAULT 'Unowned',
  `match_conditions` varchar(500) NOT NULL,
  `location_y` int(11) NOT NULL,
  `cbill_value` int(20) DEFAULT NULL,
  `production` varchar(1000) DEFAULT NULL,
  `invuln` int(1) DEFAULT NULL,
  `location_x` int(11) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `region` varchar(50) DEFAULT 'core',
  `capacity` int(11) DEFAULT NULL,
  PRIMARY KEY (`planet_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `planet`
--

LOCK TABLES `planet` WRITE;
/*!40000 ALTER TABLE `planet` DISABLE KEYS */;
INSERT INTO `planet` VALUES ('alcor','Unowned','',2910,12369223,'SDR-5D, SDR-5V, dropship20',0,1040,'lava.png','23',1),('alleghe','Unowned','',660,7906682,'SDR-5D, SDR-5V, dropship20',0,1740,'red.png','6',1),('alshain','Unowned','',1558,12766337,'SDR-5D, SDR-5V, dropship20',0,2410,'blue.png','0',1),('antares','Unowned','',1345,16038948,'SDR-5D, SDR-5V, dropship20',0,838,'gas.png','0',1),('arcturus','Unowned','',1920,11963216,'SDR-5D, SDR-5V, dropship20',0,1104,'blue.png','9',1),('ardoz','Unowned','',1670,10779371,'SDR-5D, SDR-5V, dropship20',0,2156,'lava.png','0',1),('arganda','Unowned','',2870,22375930,'SDR-5D, SDR-5V, dropship20',0,620,'red.png','0',1),('asgard','Unowned','',1650,11983020,'SDR-5D, SDR-5V, dropship20',0,2790,'earth.png','0',1),('baldur','Unowned','',1940,10116597,'SDR-5D, SDR-5V, dropship20',0,2260,'lava.png','9',1),('balsta','Unowned','',620,10610268,'SDR-5D, SDR-5V, dropship20',0,2060,'forest.png','16',1),('barstow','Unowned','',3450,19418367,'SDR-5D, SDR-5V, dropship20',0,2800,'forest.png','22',1),('basiliano','Unowned','',1080,11957634,'SDR-5D, SDR-5V, dropship20',0,1480,'forest.png','0',1),('benjamin','Unowned','',2320,7600000,'SDR-5D, SDR-5V, dropship20',1,2650,'gas.png','0',1),('berenson','Unowned','',3800,12257284,'SDR-5D, SDR-5V, dropship20',0,1430,'forest.png','0',1),('biham','Unowned','',2888,9229118,'SDR-5D, SDR-5V, dropship20',0,1808,'desert.png','0',1),('black earth','Unowned','no ecm',740,10549846,'SDR-5D, SDR-5V, dropship20',0,560,'dark.png','2',1),('blackjack','multitallented','',950,0,'SDR-5D, SDR-5V, dropship20',1,640,'dark.png','0',1),('blumenort','Unowned','',1359,11948570,'SDR-5D, SDR-5V, dropship20',0,408,'red.png','1',1),('brooloo','Unowned','',1428,12492798,'SDR-5D, SDR-5V, dropship20',0,128,'blue.png','0',1),('caripare','Unowned','',1420,9461083,'SDR-5D, SDR-5V, dropship20',0,2950,'dark.png','9',1),('carstairs','Unowned','',2070,10939216,'SDR-5D, SDR-5V, dropship20',0,1100,'desert.png','8',1),('chaffee','Unowned','',2390,7848932,'SDR-5D, SDR-5V, dropship20',0,1010,'red.png','8',1),('chateau','Unowned','',725,14392488,'SDR-5D, SDR-5V, dropship20',0,1093,'lava.png','8',1),('chatham','Unowned','',1794,28362370,'SDR-5D, SDR-5V, dropship20',0,3190,'gas.png','27',1),('clermont','Unowned','',975,8200000,'SDR-5D, SDR-5V, dropship20',0,508,'forest.png','1',1),('clovis','Unowned','',3110,11629304,'SDR-5D, SDR-5V, dropship20',0,2660,'dark.png','8',1),('coventry','Unowned','',1720,8200000,'SDR-5D, SDR-5V, dropship20',1,174,'earth.png','0',1),('crimond','Unowned','',1700,9203842,'SDR-5D, SDR-5V, dropship20',0,1260,'dark.png','1',1),('damian','Unowned','',390,3200000,'SDR-5D, SDR-5V, dropship20',1,2450,'forest.png','3',1),('donegal','Unowned','',1980,2600000,'SDR-5D, SDR-5V, dropship20',1,442,'forest.png','0',1),('dukambia','Unowned','',1690,4439470,'SDR-5D, SDR-5V, dropship20',0,569,'gas.png','0',1),('dustball','Unowned','',1520,10228005,'SDR-5D, SDR-5V, dropship20',0,946,'desert.png','0',1),('elbar','Unowned','',3175,12808402,'SDR-5D, SDR-5V, dropship20',0,2315,'gas.png','0',1),('elissa','Unowned','',360,4200000,'SDR-5D, SDR-5V, dropship20',1,1440,'blue.png','18',1),('engadin','Unowned','',1120,6902768,'SDR-5D, SDR-5V, dropship20',0,2050,'earth.png','4',1),('feltre','Unowned','',940,13382902,'SDR-5D, SDR-5V, dropship20',0,1510,'desert.png','2',1),('ferleiten','Unowned','',980,8957634,'SDR-5D, SDR-5V, dropship20',0,2030,'blue.png','0',1),('franklin','Unowned','',3230,10102988,'SDR-5D, SDR-5V, dropship20',0,3120,'blue.png','0',1),('genoa','Unowned','',3670,21519428,'SDR-5D, SDR-5V, dropship20',0,1800,'earth.png','8',1),('gotterdammerung','Unowned','',306,0,'SDR-5D, SDR-5V, dropship20',1,548,'desert.png','8',1),('gram','Unowned','',2180,12095553,'SDR-5D, SDR-5V, dropship20',0,1950,'red.png','11',1),('gustrell','Unowned','',340,1600000,'SDR-5D, SDR-5V, dropship20',1,1220,'forest.png','16',1),('hainfield','Unowned','',1580,11050313,'SDR-5D, SDR-5V, dropship20',0,1610,'red.png','10',1),('harvest','Unowned','',864,12429836,'SDR-5D, SDR-5V, dropship20',0,1149,'forest.png','2',1),('hesperus ii','Unowned','',2710,13021547,'SDR-5D, SDR-5V, dropship20',0,780,'lava.png','13',1),('hoff','Unowned','',3040,12930379,'SDR-5D, SDR-5V, dropship20',0,3290,'earth.png','6',1),('hot springs','Unowned','',868,12438948,'SDR-5D, SDR-5V, dropship20',0,597,'earth.png','1',1),('irurzun','Unowned','',2600,6013689,'SDR-5D, SDR-5V, dropship20',0,2800,'desert.png','7',1),('itabaiana','Unowned','',1280,8392058,'SDR-5D, SDR-5V, dropship20',0,2800,'forest.png','0',1),('jarett','Unowned','',660,9260120,'SDR-5D, SDR-5V, dropship20',0,2800,'desert.png','7',1),('jezersko','Unowned','',965,7938294,'SDR-5D, SDR-5V, dropship20',0,2515,'forest.png','0',1),('kempten','Unowned','',1378,13473764,'SDR-5D, SDR-5V, dropship20',0,2189,'desert.png','0',1),('kentares iv','Unowned','',3260,10400000,'SDR-5D, SDR-5V, dropship20',1,2400,'earth.png','5',1),('kirchbach','Unowned','',825,12392488,'SDR-5D, SDR-5V, dropship20',0,1293,'desert.png','2',1),('kolovraty','Unowned','',680,9092390,'SDR-5D, SDR-5V, dropship20',0,200,'dark.png','4',1),('kookens pleasure pit','Unowned','',1040,14943760,'SDR-5D, SDR-5V, dropship20',0,670,'lava.png','8',1),('koumi','Unowned','',2110,8930284,'SDR-5D, SDR-5V, dropship20',0,3200,'red.png','0',1),('kufstein','Unowned','',1100,13019445,'SDR-5D, SDR-5V, dropship20',0,1730,'red.png','0',1),('lackland','Unowned','',3470,4930382,'SDR-5D, SDR-5V, dropship20',0,3480,'blue.png','0',1),('last chance','Unowned','',250,400000,'SDR-5D, SDR-5V, dropship20',1,680,'dark.png','4',1),('leoben','Unowned','',700,8619371,'SDR-5D, SDR-5V, dropship20',0,2206,'lava.png','0',1),('ludwig','Unowned','Max Tonnage 600',2800,19239286,'SDR-5D, SDR-5V, dropship20',0,2605,'forest.png','8',1),('mararn','Unowned','',3500,6977344,'SDR-5D, SDR-5V, dropship20',0,3200,'earth.png','1',1),('marlowes rift','Unowned','',2790,8484158,'SDR-5D, SDR-5V, dropship20',0,3170,'dark.png','9',1),('matsuida','Unowned','',2580,3000000,'SDR-5D, SDR-5V, dropship20',1,3590,'gas.png','0',1),('maule','Unowned','',1738,8418394,'SDR-5D, SDR-5V, dropship20',0,2049,'red.png','0',1),('medellin','Unowned','',1206,11980290,'SDR-5D, SDR-5V, dropship20',0,320,'forest.png','0',1),('memmingen','Unowned','',1350,9126502,'SDR-5D, SDR-5V, dropship20',0,1820,'blue.png','9',1),('menkar','Unowned','',3800,15623458,'SDR-5D, SDR-5V, dropship20',0,2200,'forest.png','0',1),('midway','Unowned','',1930,17392764,'SDR-5D, SDR-5V, dropship20',0,3590,'blue.png','4',1),('milton','Unowned','',3248,13392662,'SDR-5D, SDR-5V, dropship20',0,1235,'earth.png','12',1),('mira','Unowned','',3490,14685930,'SDR-5D, SDR-5V, dropship20',0,2310,'desert.png','0',1),('montmarault','Unowned','',1400,19308258,'SDR-5D, SDR-5V, dropship20',0,1220,'forest.png','15',1),('moritz','Unowned','',1200,13093016,'SDR-5D, SDR-5V, dropship20',0,1600,'gas.png','9',1),('nestor','Unowned','',3314,11937440,'SDR-5D, SDR-5V, dropship20',0,416,'dark.png','0',1),('new caledonia','Unowned','',630,6429368,'SDR-5D, SDR-5V, dropship20',0,1250,'red.png','0',1),('new kyoto','Unowned','',3063,7982222,'SDR-5D, SDR-5V, dropship20',0,740,'lava.png','0',1),('nyserta','Unowned','',260,8200000,'SDR-5D, SDR-5V, dropship20',1,2132,'forest.png','0',1),('oshika','multitallented','',2120,0,'SDR-5D, SDR-5V, dropship20',1,3520,'forest.png','0',1),('outpost','Unowned','',510,10390372,'SDR-5D, SDR-5V, dropship20',0,2000,'blue.png','0',1),('outreach','multitallented','',3378,0,'SDR-5D, SDR-5V, dropship20',1,1546,'red.png','0',1),('ozawa','Unowned','No PPCs or ERPPCs',3000,19301846,'SDR-5D, SDR-5V, dropship20',0,2060,'lava.png','0',1),('pandora','Unowned','',1690,7479478,'SDR-5D, SDR-5V, dropship20',0,1060,'dark.png','5',1),('peacock','Unowned','',1920,9350544,'SDR-5D, SDR-5V, dropship20',0,2755,'earth.png','8',1),('phact','Unowned','',4100,16636280,'SDR-5D, SDR-5V, dropship20',0,1600,'forest.png','0',1),('pinnacle','Unowned','',520,9185262,'SDR-5D, SDR-5V, dropship20',0,2500,'desert.png','6',1),('pomme de terre','Unowned','no consumable barrages',986,14539480,'SDR-5D, SDR-5V, dropship20',0,2400,'earth.png','5',1),('port moseby','Unowned','',1960,13301750,'SDR-5D, SDR-5V, dropship20',0,1449,'gas.png','9',1),('quarell','Unowned','',1780,7855403,'SDR-5D, SDR-5V, dropship20',0,1475,'gas.png','7',1),('radstadt','Unowned','',1300,7200000,'SDR-5D, SDR-5V, dropship20',1,2130,'earth.png','0',1),('remulac','Unowned','',3474,16018551,'SDR-5D, SDR-5V, dropship20',0,1220,'dark.png','15',1),('richmond','Unowned','',450,12682484,'SDR-5D, SDR-5V, dropship20',0,2780,'forest.png','0',1),('robinson','multitallented','',3300,0,'SDR-5D, SDR-5V, dropship20',1,2970,'red.png','20',1),('rochester','Unowned','',3130,10583290,'SDR-5D, SDR-5V, dropship20',0,2950,'gas.png','8',1),('rodigo','Unowned','',760,7204982,'SDR-5D, SDR-5V, dropship20',0,1600,'gas.png','7',1),('romulus','Unowned','',798,12572872,'SDR-5D, SDR-5V, dropship20',0,878,'lava.png','2',1),('ryde','Unowned','',2300,19098296,'SDR-5D, SDR-5V, dropship20',0,1376,'forest.png','8',1),('santander v','Unowned','',200,8400000,'SDR-5D, SDR-5V, dropship20',1,2540,'earth.png','20',1),('schwartz','Unowned','',550,13782084,'SDR-5D, SDR-5V, dropship20',0,3228,'red.png','0',1),('shiloh','Unowned','',3154,11713888,'SDR-5D, SDR-5V, dropship20',0,952,'dark.png','24',1),('shirotori','Unowned','',1880,17584920,'SDR-5D, SDR-5V, dropship20',0,1816,'earth.png','0',1),('shitara','Unowned','',2488,7889450,'SDR-5D, SDR-5V, dropship20',0,1708,'lava.png','2',1),('skallevoll','Unowned','',470,12661829,'SDR-5D, SDR-5V, dropship20',0,2250,'blue.png','2',1),('skye','Unowned','',2736,6600000,'SDR-5D, SDR-5V, dropship20',1,1250,'blue.png','0',1),('solaris','multitallented','',3190,0,'SDR-5D, SDR-5V, dropship20',1,792,'gas.png','0',1),('sommerset','Unowned','',500,17382746,'SDR-5D, SDR-5V, dropship20',0,684,'earth.png','17',1),('sonnia','Unowned','No consumable barrages',3420,20712971,'SDR-5D, SDR-5V, dropship20',0,2400,'earth.png','8',1),('spittal','Unowned','',1150,15572646,'SDR-5D, SDR-5V, dropship20',0,2420,'gas.png','15',1),('stapelfeld','Unowned','',715,11338394,'SDR-5D, SDR-5V, dropship20',0,3492,'forest.png','0',1),('steelton','Unowned','',560,11689530,'SDR-5D, SDR-5V, dropship20',0,1030,'red.png','11',1),('stewart','Unowned','',3660,19732001,'SDR-5D, SDR-5V, dropship20',0,751,'earth.png','0',1),('summer','Unowned','',2996,14427282,'SDR-5D, SDR-5V, dropship20',0,1300,'blue.png','8',1),('summit','Unowned','',1820,10170617,'SDR-5D, SDR-5V, dropship20',0,780,'lava.png','9',1),('sutama','Unowned','',2420,13549105,'SDR-5D, SDR-5V, dropship20',0,2380,'gas.png','10',1),('svarstaad','Unowned','',1090,9764727,'SDR-5D, SDR-5V, dropship20',0,1156,'dark.png','5',1),('tamar','multitallented','',1300,0,'SDR-5D, SDR-5V, dropship20',1,1610,'earth.png','0',1),('terra','multitallented','',3168,0,'SDR-5D, SDR-5V, dropship20',1,1586,'earth.png','0',1),('tharkad','multitallented','',2090,0,'SDR-5D, SDR-5V, dropship20',1,262,'earth.png','0',1),('the edge','Unowned','',660,12854474,'SDR-5D, SDR-5V, dropship20',0,1560,'dark.png','9',1),('thessalonika','Unowned','',1343,13738290,'SDR-5D, SDR-5V, dropship20',0,2490,'lava.png','2',1),('thule','Unowned','',460,14638726,'SDR-5D, SDR-5V, dropship20',0,2582,'lava.png','0',1),('tikonov','Unowned','',3400,17207419,'SDR-5D, SDR-5V, dropship20',0,2240,'earth.png','13',1),('toland','Unowned','',490,5865360,'SDR-5D, SDR-5V, dropship20',0,910,'blue.png','0',1),('tortuga prime','Unowned','',3800,3597168,'SDR-5D, SDR-5V, dropship20',0,3500,'dark.png','2',1),('trent','Unowned','',2475,13565493,'SDR-5D, SDR-5V, dropship20',0,680,'lava.png','0',1),('tripoli','Unowned','',2950,7901876,'SDR-5D, SDR-5V, dropship20',0,2870,'lava.png','6',1),('trolloc prime','Unowned','',1998,12582938,'SDR-5D, SDR-5V, dropship20',0,1765,'earth.png','1',1),('trondheim','Unowned','no ERPPCs, LRMs, GaussRifles, AC2s or ERLargeLasers',720,15072604,'SDR-5D, SDR-5V, dropship20',0,2550,'lava.png','11',1),('tukayyid','Unowned','',1740,14108296,'SDR-5D, SDR-5V, dropship20',0,1750,'forest.png','12',1),('turtle bay','Unowned','',676,15338394,'SDR-5D, SDR-5V, dropship20',0,3142,'blue.png','0',1),('unzmarkt','Unowned','',910,9685602,'SDR-5D, SDR-5V, dropship20',0,1760,'earth.png','0',1),('valmiera','Unowned','',2460,4334619,'SDR-5D, SDR-5V, dropship20',0,3210,'earth.png','5',1),('vantaa','Unowned','',975,11547588,'SDR-5D, SDR-5V, dropship20',0,970,'forest.png','3',1),('veckholm','Unowned','',2120,8150737,'SDR-5D, SDR-5V, dropship20',0,820,'forest.png','6',1),('vega','Unowned','',2230,8593920,'SDR-5D, SDR-5V, dropship20',0,1480,'red.png','8',1),('waddesdon','Unowned','',2700,19014105,'SDR-5D, SDR-5V, dropship20',0,2290,'lava.png','0',1),('winfield','Unowned','',670,12548288,'SDR-5D, SDR-5V, dropship20',0,890,'desert.png','0',1),('woodstock','Unowned','',3400,13231066,'SDR-5D, SDR-5V, dropship20',0,1950,'forest.png','0',1),('zoetermeer','Unowned','',1206,7105457,'SDR-5D, SDR-5V, dropship20',0,920,'desert.png','3',1);
/*!40000 ALTER TABLE `planet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roster`
--

DROP TABLE IF EXISTS `roster`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roster` (
  `unit_leader` varchar(25) DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roster`
--

LOCK TABLES `roster` WRITE;
/*!40000 ALTER TABLE `roster` DISABLE KEYS */;
INSERT INTO `roster` VALUES ('krazypoloc','http://mwomercs.com/forums/user/388147-krazypoloc/'),('krazypoloc','http://mwomercs.com/forums/user/336823-thaddios/'),('krazypoloc','http://mwomercs.com/forums/user/52551-lex420/'),('krazypoloc','http://mwomercs.com/forums/user/212293-smitbag/'),('krazypoloc','http://mwomercs.com/forums/user/435061-pdxmatt/'),('krazypoloc','http://mwomercs.com/forums/user/129057-timbar/'),('krazypoloc','http://mwomercs.com/forums/user/17634-filth-pig/'),('krazypoloc','http://mwomercs.com/forums/user/179760-elahen/'),('vas79','http://mwomercs.com/forums/user/111737-vas79/'),('vas79','http://mwomercs.com/forums/user/446681-dr-lemonsquare/'),('vas79','http://mwomercs.com/forums/user/19302-nodebate/'),('vas79','http://mwomercs.com/forums/user/61002-troggy/'),('vas79','http://mwomercs.com/forums/user/402206-auspride/'),('vas79','http://mwomercs.com/forums/user/130711-jackalhit/'),('vas79','http://mwomercs.com/forums/user/175385-gauss-dam/'),('vas79','http://mwomercs.com/forums/user/517712-dirtdiver1776/'),('mech79','http://mwomercs.com/forums/user/86690-mech79/'),('mech79','http://mwomercs.com/forums/user/48539-noonan/'),('mech79','http://mwomercs.com/forums/user/49384-werewolf486/'),('mech79','http://mwomercs.com/forums/user/29197-c0vvb3ll/'),('mech79','http://mwomercs.com/forums/user/945-miscreant/'),('mech79','http://mwomercs.com/forums/user/41297-immelmann/'),('mech79','http://mwomercs.com/forums/user/2875-phantom411/'),('mech79','http://mwomercs.com/forums/user/21249-darkblood/'),('greyghosts','http://mwomercs.com/forums/user/128714-woky/'),('greyghosts','http://mwomercs.com/forums/user/1306-shadow_x/'),('greyghosts','http://mwomercs.com/forums/user/18922-wynce/'),('greyghosts','http://mwomercs.com/forums/user/90791-omegawraith/'),('greyghosts','http://mwomercs.com/forums/user/8993-batwing/'),('greyghosts','http://mwomercs.com/forums/user/255087-diabolus-vulpes/'),('greyghosts','http://mwomercs.com/forums/user/403330-ashes42/'),('greyghosts','http://mwomercs.com/forums/user/187393-vanos/'),('dankith','http://mwomercs.com/forums/user/188531-dankith/'),('dankith','http://mwomercs.com/forums/user/316248-n-danger/'),('dankith','http://mwomercs.com/forums/user/426749-johnny-redburn/'),('dankith','http://mwomercs.com/forums/user/456678-gurrenx/'),('dankith','http://mwomercs.com/forums/user/420062-wip3ou7/'),('dankith','http://mwomercs.com/forums/user/389007-lysander-voidrunner/'),('dankith','http://mwomercs.com/forums/user/434620-xeff/'),('dankith','http://mwomercs.com/forums/user/187135-vulix/'),('cef command','http://mwomercs.com/forums/user/30394-william-mcnab/'),('cef command','http://mwomercs.com/forums/user/406276-nomadiccanuck/'),('cef command','http://mwomercs.com/forums/user/439430-phoenix-woot/'),('cef command','http://mwomercs.com/forums/user/33203-wob-particleman/'),('cef command','http://mwomercs.com/forums/user/3365-arwdassain/'),('cef command','http://mwomercs.com/forums/user/425533-sylintfrog/'),('cef command','http://cdnsmall.mwomercs.com/forums/user/107788-dark-fury/'),('cef command','http://mwomercs.com/forums/user/7350-trex/'),('deadfire','http://mwomercs.com/forums/user/53438-deadfire/'),('deadfire','http://mwomercs.com/forums/user/385072-vercinix/'),('deadfire','http://mwomercs.com/forums/user/10313-queenblade/'),('deadfire','http://mwomercs.com/forums/user/450767-grim57/'),('deadfire','http://mwomercs.com/forums/user/17203-r13/'),('deadfire','http://mwomercs.com/forums/user/388064-defunked/'),('deadfire','http://mwomercs.com/forums/user/290130-pikachar/'),('deadfire','http://mwomercs.com/forums/user/34199-rez-kalamari/'),('crazyhorse','http://mwomercs.com/forums/user/7967-crazyhorse/'),('crazyhorse','http://mwomercs.com/forums/user/19144-caballo/'),('crazyhorse','http://mwomercs.com/forums/user/440766-skrunjen/'),('crazyhorse','http://mwomercs.com/forums/user/434481-gusano/'),('crazyhorse','http://mwomercs.com/forums/user/96161-hyoga/'),('crazyhorse','http://mwomercs.com/forums/user/273614-god-and-davion/'),('crazyhorse','http://mwomercs.com/forums/user/273015-z3ro/'),('crazyhorse','http://mwomercs.com/forums/user/468609-khayron/'),('3rdworld','http://mwomercs.com/forums/user/49989-3rdworld/'),('3rdworld','http://mwomercs.com/forums/user/457681-caervyn/'),('3rdworld','http://mwomercs.com/forums/user/261601-antonius-rex/'),('3rdworld','http://mwomercs.com/forums/user/62040-imminent/'),('3rdworld','http://mwomercs.com/forums/user/198935-araara/'),('3rdworld','http://mwomercs.com/forums/user/71476-black-alexidor/'),('3rdworld','http://mwomercs.com/forums/user/440214-twinkyoverlord/'),('3rdworld','http://mwomercs.com/forums/user/131576-captain-terrific/');
/*!40000 ALTER TABLE `roster` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `username` varchar(25) NOT NULL,
  `password` varchar(500) DEFAULT NULL,
  `cbills` int(20) DEFAULT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `wins` int(11) NOT NULL DEFAULT '0',
  `loses` int(11) NOT NULL DEFAULT '0',
  `unit_type` varchar(20) NOT NULL DEFAULT 'merc',
  `unit_name` varchar(50) NOT NULL,
  `is_dead` tinyint(1) NOT NULL DEFAULT '0',
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `kills` int(11) NOT NULL DEFAULT '0',
  `last_login` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `last_cbill_gift` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `bio` varchar(10000) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES ('3rdworld','073cdf44e4a62f9158f448aec45b2db2',0,0,0,0,'faction','Sword of Kentares',0,0,0,'2013-10-15 03:45:25','2013-10-15 03:45:25','','3rdworlds@gmail.com'),('cef command','22588bf8d5eaff86f0eda0287677c9e2',0,0,0,0,'pirate','Canadian Expeditionary Force',0,0,0,'2013-10-12 21:04:04','2013-10-12 21:04:04','','command.cefmwo@gmail.com'),('crazyhorse','d428d6d72f8cdfd888c64eb915f101dc',0,0,0,0,'merc','Red Shadows',0,0,0,'2013-10-14 20:35:31','2013-10-14 20:35:31','',''),('dankith','0c03567817d07360407578afae787e38',0,0,0,0,'faction','Carrion Crows',0,0,0,'2013-10-09 08:45:52','2013-10-09 08:45:52','','nicklend@yahoo.com'),('deadfire','65ee06d8d4b8f78032d33e813cb3f08d',0,0,0,0,'pirate','228th Independent Battlemech Regiment',0,0,0,'2013-10-13 00:52:22','2013-10-13 00:52:22','','admin@228ibr.com'),('greyghosts','678abb9ba010a503969408bfd9c80712',0,0,0,0,'merc','Grey Ghosts',0,0,0,'2013-10-08 16:47:21','2013-10-08 16:47:21','','ryanw.814@hotmail.com'),('krazypoloc','d5441874d80b701d21e808fceeb1bfeb',0,0,0,0,'faction','25th Marik Militia',0,0,0,'2013-10-07 21:49:01','2013-10-07 21:49:01','','krazywicki@gmail.com'),('mech79','63c78e7b1ee32e74f35565d8f5c78f32',0,0,0,0,'merc','Crimson Death Commandos',0,0,0,'2013-10-08 04:49:54','2013-10-08 04:49:54','','rylandb79@yahoo.com'),('multitallented','d1678b77a1e66888d5e6d01c388afb63',1485524851,1,10,3,'admin','ComStar',0,1,0,'2013-10-17 03:30:27','2013-10-17 01:32:33','I am the admin of this league and I am also a member of KaoS Legion. I have years of experience with being an admin in an exploit/bug rich environment.<br><br>I am a very relaxed, patient guy and I run the league in the same way. I try to fix things as quickly as possible and be as helpful as I can.<br><br>My primary concern is the success of Mechwarrior Online, and I hope that this league helps in that way.','multitallented@pacbell.net'),('tester01','',558770106,0,1,5,'merc','Testing01',0,1,0,'0000-00-00 00:00:00','0000-00-00 00:00:00','',''),('vas79','5ddbce6b034c842c7d86a5ac82a5b7d3',0,0,0,0,'pirate','Apocalypse Lancers',0,0,0,'2013-10-08 00:51:05','2013-10-08 00:51:05','','b.crowston@hotmail.com');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-10-17 11:25:29
