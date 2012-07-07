DROP TABLE IF EXISTS `menu_inputs`;
CREATE TABLE `menu_inputs` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(5) unsigned NOT NULL,
  `menu_place_id` tinyint(1) unsigned NOT NULL,
  `input_id` int(10) unsigned DEFAULT NULL,
  `link` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `target` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_type` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `help_type` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `position` smallint(5) unsigned DEFAULT NULL,
  `created` int(10) unsigned NOT NULL,
  `updated` int(10) unsigned NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `id_menu_parent` (`parent_id`),
  KEY `id_menu_type` (`menu_place_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
