SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

DROP TABLE IF EXISTS `colors`;
CREATE TABLE IF NOT EXISTS `colors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=25 ;

DROP TABLE IF EXISTS `contacts`;
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `position` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `website` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  `telHome` varchar(20) COLLATE utf8_polish_ci NOT NULL,
  `telWork` varchar(20) COLLATE utf8_polish_ci NOT NULL,
  `telMobile` varchar(20) COLLATE utf8_polish_ci NOT NULL,
  `address` text COLLATE utf8_polish_ci NOT NULL,
  `comments` text COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=4 ;

DROP TABLE IF EXISTS `quantities`;
CREATE TABLE IF NOT EXISTS `quantities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=43 ;

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nr` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  `startDate` int(10) unsigned NOT NULL,
  `closeDate` int(10) unsigned NOT NULL DEFAULT '0',
  `doctor` int(10) unsigned DEFAULT NULL,
  `patient` int(10) unsigned DEFAULT NULL,
  `quantity` decimal(7,2) unsigned NOT NULL DEFAULT '0.00',
  `unit` varchar(20) COLLATE utf8_polish_ci NOT NULL DEFAULT 'szt.',
  `price` decimal(8,2) unsigned NOT NULL DEFAULT '0.00',
  `color` int(10) unsigned DEFAULT NULL,
  `worktype` int(10) unsigned DEFAULT NULL,
  `notes` text COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nr` (`nr`),
  KEY `patient` (`patient`),
  KEY `color` (`color`),
  KEY `worktype` (`worktype`),
  KEY `doctor` (`doctor`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=8 ;

DROP TABLE IF EXISTS `worktypes`;
CREATE TABLE IF NOT EXISTS `worktypes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `price` decimal(8,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci AUTO_INCREMENT=61 ;

DROP TABLE IF EXISTS `worktype_prices`;
CREATE TABLE IF NOT EXISTS `worktype_prices` (
  `doctor` int(10) unsigned NOT NULL,
  `worktype` int(10) unsigned NOT NULL,
  `price` decimal(8,2) unsigned NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`doctor`,`worktype`),
  KEY `worktype` (`worktype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;


ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_3` FOREIGN KEY (`patient`) REFERENCES `contacts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_4` FOREIGN KEY (`color`) REFERENCES `colors` (`id`),
  ADD CONSTRAINT `tasks_ibfk_5` FOREIGN KEY (`worktype`) REFERENCES `worktypes` (`id`),
  ADD CONSTRAINT `tasks_ibfk_6` FOREIGN KEY (`doctor`) REFERENCES `contacts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `worktype_prices`
  ADD CONSTRAINT `worktype_prices_ibfk_2` FOREIGN KEY (`worktype`) REFERENCES `contacts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `worktype_prices_ibfk_1` FOREIGN KEY (`doctor`) REFERENCES `contacts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
