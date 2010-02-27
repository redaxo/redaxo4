DROP TABLE IF EXISTS `%TABLE_PREFIX%679_types`;
CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%679_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL,
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

INSERT INTO `%TABLE_PREFIX%679_types` (`id`, `status`, `name`, `description`) VALUES(1, 1, 'rex_mediapool_detail', 'Zur Darstellung von Bildern in der Detailansicht im Medienpool');
INSERT INTO `%TABLE_PREFIX%679_types` (`id`, `status`, `name`, `description`) VALUES(2, 1, 'rex_mediapool_maximized', 'Zur Darstellung von Bildern im Medienpool wenn maximiert');
INSERT INTO `%TABLE_PREFIX%679_types` (`id`, `status`, `name`, `description`) VALUES(3, 1, 'rex_mediapool_preview', 'Zur Darstellung der Vorschaubilder im Medienpool');