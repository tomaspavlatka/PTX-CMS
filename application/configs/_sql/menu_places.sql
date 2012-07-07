DROP TABLE IF EXISTS `menu_places`;
CREATE TABLE `menu_places` (
  `id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `class` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `updated` int(10) unsigned NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
