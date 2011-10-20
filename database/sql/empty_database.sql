-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 22, 2010 at 08:40 AM
-- Server version: 5.1.41
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `compost_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `annotation`
--
DROP TABLE IF EXISTS `annotation`;
CREATE TABLE IF NOT EXISTS `annotation` (
  `AnnotationId` int(11) NOT NULL AUTO_INCREMENT,
  `CompId` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  `AnnotationX` int(11) NOT NULL,
  `AnnotationY` int(11) NOT NULL,
  `AnnotationText` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`AnnotationId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
-- Dumping data for table `annotation`
--


-- --------------------------------------------------------

--
-- Table structure for table `comment`
--
DROP TABLE IF EXISTS `comment`;
CREATE TABLE IF NOT EXISTS `comment` (
  `CommentId` int(11) NOT NULL AUTO_INCREMENT,
  `AnnotationId` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  `CommentBody` text COLLATE utf8_bin NOT NULL,
  `CommentTimestamp` int(11) NOT NULL,
  `CommentRating` int(11) NOT NULL,
  PRIMARY KEY (`CommentId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
-- Dumping data for table `comment`
--


-- --------------------------------------------------------

--
-- Table structure for table `comp`
--
DROP TABLE IF EXISTS `comp`;
CREATE TABLE IF NOT EXISTS `comp` (
  `CompId` int(11) NOT NULL AUTO_INCREMENT,
  `ProjectId` int(11) NOT NULL,
  `CompName` varchar(255) COLLATE utf8_bin NOT NULL,
  `CompDescription` text COLLATE utf8_bin NOT NULL,
  `CompHidden` tinyint(1) NOT NULL,
  PRIMARY KEY (`CompId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `company`
--
DROP TABLE IF EXISTS `company`;
CREATE TABLE IF NOT EXISTS `company` (
  `CompanyId` int(11) NOT NULL AUTO_INCREMENT,
  `CompanyName` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`CompanyId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`CompanyId`, `CompanyName`) VALUES
(-1, 'Archive');

-- --------------------------------------------------------

--
-- Table structure for table `log`
--
DROP TABLE IF EXISTS `log`;
CREATE TABLE IF NOT EXISTS `log` (
  `LogId` int(11) NOT NULL AUTO_INCREMENT,
  `LogEvent` varchar(255) COLLATE utf8_bin NOT NULL,
  `LogType` text COLLATE utf8_bin NOT NULL,
  `LogTimestamp` int(11) NOT NULL,
  `LogIP` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`LogId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `permission`
--
DROP TABLE IF EXISTS `permission`;
CREATE TABLE IF NOT EXISTS `permission` (
  `PermissionId` int(11) NOT NULL AUTO_INCREMENT,
  `ProjectId` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  PRIMARY KEY (`PermissionId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `project`
--
DROP TABLE IF EXISTS `project`;
CREATE TABLE IF NOT EXISTS `project` (
  `ProjectId` int(11) NOT NULL AUTO_INCREMENT,
  `CompanyId` int(11) NOT NULL,
  `ProjectName` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`ProjectId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`ProjectId`, `CompanyId`, `ProjectName`) VALUES
(-1, -1, 'Miscellaneous');

-- --------------------------------------------------------

--
-- Table structure for table `rating`
--
DROP TABLE IF EXISTS `rating`;
CREATE TABLE IF NOT EXISTS `rating` (
  `RatingId` int(11) NOT NULL AUTO_INCREMENT,
  `CompId` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  `Rating` int(11) NOT NULL,
  PRIMARY KEY (`RatingId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
-- Dumping data for table `rating`
--


-- --------------------------------------------------------

--
-- Table structure for table `read`
--
DROP TABLE IF EXISTS `read`;
CREATE TABLE IF NOT EXISTS `read` (
  `ReadId` int(11) NOT NULL AUTO_INCREMENT,
  `CommentId` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  PRIMARY KEY (`ReadId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

--
-- Dumping data for table `read`
--


-- --------------------------------------------------------

--
-- Table structure for table `revision`
--
DROP TABLE IF EXISTS `revision`;
CREATE TABLE IF NOT EXISTS `revision` (
  `RevisionId` int(11) NOT NULL AUTO_INCREMENT,
  `CompId` int(11) NOT NULL,
  `RevisionUrl` varchar(255) COLLATE utf8_bin NOT NULL,
  `RevisionDate` date NOT NULL,
  `RevisionBackgroundColour` varchar(255) COLLATE utf8_bin NOT NULL,
  `RevisionBackgroundImage` varchar(255) COLLATE utf8_bin NOT NULL,
  `RevisionBackgroundRepeat` varchar(255) COLLATE utf8_bin NOT NULL,
  `RevisionPageFloat` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`RevisionId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--
DROP TABLE IF EXISTS `setting`;
CREATE TABLE IF NOT EXISTS `setting` (
  `SettingId` int(11) NOT NULL AUTO_INCREMENT,
  `SettingName` varchar(255) COLLATE utf8_bin NOT NULL,
  `SettingValue` varchar(255) COLLATE utf8_bin NOT NULL,
  `DefaultValue` varchar(255) COLLATE utf8_bin NOT NULL,
  `DataType` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`SettingId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`SettingName`, `SettingValue`, `DefaultValue`, `DataType`) VALUES
('Site Title', '', 'Design Feedback', 'string'),
('Title Font', 'Georgia,serif', 'Georgia,serif', 'font'),
('Title Colour', 'FFFFFF', 'FFFFFF', 'color'),
('Heading Font', 'Arial,Helvetica,sans-serif', 'Arial,Helvetica,sans-serif', 'font'),
('Heading Colour', '000000', '000000', 'color'),
('Body Background Image', 'main_bg.png', 'main_bg.png', 'file'),
('Header Background Colour', '747474', '747474', 'color'),
('Header Background Image', 'page_bg.png', 'page_bg.png', 'file'),
('Body Font', 'Arial,Helvetica,sans-serif', 'Arial,Helvetica,sans-serif', 'font'),
('Body Colour', '3D3D3D', '3D3D3D', 'color'),
('Menu Background Colour', '8bc642', '8bc642', 'color'),
('Menu Text Colour', 'FFFFFF', 'FFFFFF', 'color'),
('Menu Active Text Colour', '0AAFF4', '0AAFF4', 'color');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `UserId` int(11) NOT NULL AUTO_INCREMENT,
  `CompanyId` int(11) NOT NULL,
  `UserName` varchar(255) COLLATE utf8_bin NOT NULL,
  `UserEmail` varchar(255) COLLATE utf8_bin NOT NULL,
  `UserPassword` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`UserId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

INSERT INTO `user` (`UserId`, `CompanyId`) VALUES (-1,-1);