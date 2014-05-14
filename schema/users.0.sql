CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `password` text NOT NULL,
  `displayname` text NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `creator` int(11) NOT NULL,
  `last_editor` int(11) NOT NULL,
  `create_time` bigint(20) NOT NULL,
  `modify_time` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_name` (`name`(50))
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8