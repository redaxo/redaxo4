DROP TABLE IF EXISTS `%TABLE_PREFIX%679_types`;
CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%679_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) TYPE=MyISAM;

DROP TABLE IF EXISTS `%TABLE_PREFIX%679_type_effects`;
CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%679_type_effects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `effect` varchar(255) NOT NULL,
  `parameters` text NOT NULL,
  PRIMARY KEY (`id`)
) TYPE=MyISAM;