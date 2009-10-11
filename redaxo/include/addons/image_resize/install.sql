CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%469_types` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `settings` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

INSERT INTO `%TABLE_PREFIX%469_types` VALUES(1, 'beispiel', '', '300w__##FILE##&rex_filter[]=greyscale');
