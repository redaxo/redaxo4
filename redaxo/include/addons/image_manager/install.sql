CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%_img_manager_types` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `settings` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

