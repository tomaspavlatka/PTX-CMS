DROP TABLE IF EXISTS `widgets`;
CREATE TABLE `widgets` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `widget_place_id` tinyint(1) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `parent_type` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `position` tinyint(2) unsigned NOT NULL,
  `show_name` tinyint(1) DEFAULT NULL,
  `param` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` int(10) unsigned NOT NULL,
  `updated` int(10) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_place` (`widget_place_id`),
  KEY `user` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
