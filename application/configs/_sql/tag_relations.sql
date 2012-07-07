DROP TABLE IF EXISTS `tag_relations`;
CREATE TABLE `tag_relations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int(10) unsigned NOT NULL,
  `parent_id` int(10) unsigned NOT NULL,
  `parent_type` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `tag` (`tag_id`),
  KEY `parent` (`parent_id`,`parent_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;