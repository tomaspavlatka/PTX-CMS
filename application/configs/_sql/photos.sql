DROP TABLE IF EXISTS `photos`;
CREATE TABLE `photos` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(5) unsigned NOT NULL,
  `parent_type` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `note` varchar(255) COLLATE utf8_unicode_ci NULL,
  `file_name` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image_width` int(10) DEFAULT NULL,
  `image_height` int(10) DEFAULT NULL,
  `position` int(10) unsigned NOT NULL,
  `published` int(10) unsigned NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `updated` int(10) unsigned NOT NULL,
  `picasa_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent` (`parent_id`,`parent_type`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;