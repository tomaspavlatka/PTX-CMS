DROP TABLE IF EXISTS `widget_places`;
CREATE TABLE `widget_places` (
  `id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `parent_type` enum('frontend','admin') COLLATE utf8_unicode_ci NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `updated` int(10) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;