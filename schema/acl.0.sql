CREATE TABLE `acl` (
  `tenant` int(11) NOT NULL DEFAULT '1',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_type` text NOT NULL,
  `object_id` int(11) NOT NULL COMMENT 'if 0, applies for all objects of this type',
  `target_type` text NOT NULL,
  `target_id` int(11) NOT NULL,
  `negate` int(11) NOT NULL,
  `acl` text NOT NULL,
  `creator` int(11) NOT NULL,
  `last_editor` int(11) NOT NULL,
  `create_time` bigint(20) NOT NULL,
  `modify_time` bigint(20) NOT NULL,
  PRIMARY KEY (`tenant`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8