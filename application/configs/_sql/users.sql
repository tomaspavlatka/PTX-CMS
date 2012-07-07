DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_group_id` int(10) NOT NULL DEFAULT '3',
  `email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` char(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `locale` char(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` int(10) unsigned NOT NULL,
  `updated` int(10) unsigned DEFAULT NULL,
  `deleted` int(10) unsigned DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;