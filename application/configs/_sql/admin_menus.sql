-- Adminer 3.3.3 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `admin_menus`;
CREATE TABLE `admin_menus` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(5) unsigned DEFAULT NULL,
  `module` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `controller` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `action` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parameters` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `position` smallint(5) DEFAULT NULL,
  `created` int(10) unsigned NOT NULL,
  `updated` int(10) unsigned NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `id_menu_parent` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;