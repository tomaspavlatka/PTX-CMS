DROP TABLE IF EXISTS `url_backups`;
CREATE TABLE `url_backups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `parent_type` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `url_hash` char(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `locale` char(2) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `url_hash` (`url_hash`(10),`parent_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;