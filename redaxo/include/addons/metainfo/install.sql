CREATE TABLE `rex_62_params` (
  `field_id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `name` varchar(255) default NULL,
  `attributes` varchar(255) NOT NULL,
  `type` int(10) unsigned default NULL,
  `default` varchar(255) NOT NULL,
  `params` varchar(255) default NULL,
  `validate` varchar(255) default NULL,
  PRIMARY KEY  (`field_id`),
  UNIQUE KEY `name` (`name`)
);

CREATE TABLE `rex_62_type` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `label` varchar(255) default NULL,
  `dbtype` varchar(255) NOT NULL,
  `dblength` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM ;

INSERT INTO rex_62_type VALUES (1, 'text', 'varchar', 255);
INSERT INTO rex_62_type VALUES (2, 'textarea', 'text', 0);
INSERT INTO rex_62_type VALUES (3, 'select', 'varchar', 255);
INSERT INTO rex_62_type VALUES (4, 'radio', 'varchar', 255);
INSERT INTO rex_62_type VALUES (5, 'checkbox', 'varchar', 255);
INSERT INTO rex_62_type VALUES (6, 'REX_MEDIA_BUTTON', 'varchar', 255);
INSERT INTO rex_62_type VALUES (8, 'REX_LINK_BUTTON', 'varchar', 255);