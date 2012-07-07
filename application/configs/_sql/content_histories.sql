DROP TABLE IF EXISTS `content_histories`;
CREATE TABLE `content_histories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `content_id` int(10) unsigned NOT NULL,
  `content_type` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `perex` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `notice` text COLLATE utf8_unicode_ci,
  `locale` char(2) COLLATE utf8_unicode_ci NOT NULL,
  `created` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `content_id` (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;