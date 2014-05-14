CREATE TABLE `user_tokens` (
  `tenant` int(11) NOT NULL DEFAULT '1',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `serial` text NOT NULL,
  `active` int(11) NOT NULL,
  `data` text NOT NULL,
  `type` int(11) NOT NULL,
  `creator` int(11) NOT NULL,
  `last_editor` int(11) NOT NULL,
  `create_time` bigint(20) NOT NULL,
  `modify_time` bigint(20) NOT NULL,
  PRIMARY KEY (`tenant`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8