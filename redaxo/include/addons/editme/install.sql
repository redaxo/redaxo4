CREATE TABLE `rex_em_field` (
  `id` int(11) NOT NULL auto_increment,
  `table_id` int(11) NOT NULL,
  `type_name` varchar(255) NOT NULL,
  `type_id` varchar(255) NOT NULL,
  `field` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `rex_em_table` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
