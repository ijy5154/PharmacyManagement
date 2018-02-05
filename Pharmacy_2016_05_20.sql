-- --------------------------------------------------------
-- 호스트:                          localhost
-- 서버 버전:                        5.0.22-log - Source distribution
-- 서버 OS:                        redhat-linux-gnu
-- HeidiSQL 버전:                  9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- pharmacy 데이터베이스 구조 내보내기
CREATE DATABASE IF NOT EXISTS `pharmacy` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `pharmacy`;


-- 테이블 pharmacy.TB-Company 구조 내보내기
CREATE TABLE IF NOT EXISTS `TB-Company` (
  `Company` int(10) unsigned NOT NULL auto_increment,
  `CompanyName` char(50) NOT NULL,
  `Description` varchar(250) NOT NULL,
  `Date_Entry` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`Company`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 테이블 데이터 pharmacy.TB-Company:~1 rows (대략적) 내보내기
/*!40000 ALTER TABLE `TB-Company` DISABLE KEYS */;
INSERT INTO `TB-Company` (`Company`, `CompanyName`, `Description`, `Date_Entry`) VALUES
	(1, '한마을', '약국', '2016-05-19 02:19:37');
/*!40000 ALTER TABLE `TB-Company` ENABLE KEYS */;


-- 테이블 pharmacy.TB-MedicineInfo 구조 내보내기
CREATE TABLE IF NOT EXISTS `TB-MedicineInfo` (
  `Info` int(10) unsigned NOT NULL auto_increment,
  `User` int(10) unsigned default NULL,
  `Company` int(10) unsigned NOT NULL,
  `Medicine_Name` varchar(200) NOT NULL,
  `Description` varchar(250) NOT NULL,
  `Start_Date` date NOT NULL,
  `End_Date` date NOT NULL,
  `Alarm` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (`Info`),
  KEY `FK_TB-MedicineInfo_TB-UserInfo` (`User`),
  KEY `FK_TB-MedicineInfo_TB-Company` (`Company`),
  CONSTRAINT `FK_TB-MedicineInfo_TB-Company` FOREIGN KEY (`Company`) REFERENCES `TB-Company` (`Company`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_TB-MedicineInfo_TB-UserInfo` FOREIGN KEY (`User`) REFERENCES `TB-UserInfo` (`User`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 테이블 데이터 pharmacy.TB-MedicineInfo:~0 rows (대략적) 내보내기
/*!40000 ALTER TABLE `TB-MedicineInfo` DISABLE KEYS */;
/*!40000 ALTER TABLE `TB-MedicineInfo` ENABLE KEYS */;


-- 테이블 pharmacy.TB-UserInfo 구조 내보내기
CREATE TABLE IF NOT EXISTS `TB-UserInfo` (
  `User` int(10) unsigned NOT NULL auto_increment,
  `Company` int(10) unsigned NOT NULL,
  `UserID` char(50) NOT NULL,
  `Password` char(41) NOT NULL,
  `UserName` char(41) NOT NULL,
  `Master` enum('Y','N') NOT NULL default 'N',
  `Date_Entry` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`User`),
  KEY `FK_TB-UserInfo_TB-Company` (`Company`),
  CONSTRAINT `FK_TB-UserInfo_TB-Company` FOREIGN KEY (`Company`) REFERENCES `TB-Company` (`Company`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 테이블 데이터 pharmacy.TB-UserInfo:~1 rows (대략적) 내보내기
/*!40000 ALTER TABLE `TB-UserInfo` DISABLE KEYS */;
INSERT INTO `TB-UserInfo` (`User`, `Company`, `UserID`, `Password`, `UserName`, `Master`, `Date_Entry`) VALUES
	(1, 1, 'klopk', '*89C6B530AA78695E257E55D63C00A6EC9AD3E977', '이미정', 'Y', '2016-05-19 03:51:58');
/*!40000 ALTER TABLE `TB-UserInfo` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
